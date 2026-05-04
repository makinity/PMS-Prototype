<?php

namespace App\Http\Controllers\Admin;

use App\Mail\PmsEmployeeIdIssuedMail;
use App\Http\Controllers\Controller;
use App\Models\Office;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        self::ensureAdmin($request);

        $payload = self::buildIndexPayload($request);

        return view('admin.users', $payload);
    }

    public static function buildIndexPayload(Request $request): array
    {
        self::ensureAdmin($request);

        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'role' => trim((string) $request->query('role', '')),
            'office_id' => trim((string) $request->query('office_id', '')),
            'status' => trim((string) $request->query('status', 'all')),
        ];

        $allowedRoles = ['employee', 'supervisor', 'dept-head', 'pmt'];
        $query = User::query()
            ->with(['office:id,name,code'])
            ->where('role', '!=', 'admin');

        if ($filters['search'] !== '') {
            $search = $filters['search'];
            $query->where(function ($inner) use ($search) {
                $inner->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        if (in_array($filters['role'], $allowedRoles, true)) {
            $query->where('role', $filters['role']);
        }

        if ($filters['office_id'] !== '' && ctype_digit($filters['office_id'])) {
            $query->where('office_id', (int) $filters['office_id']);
        }

        if ($filters['status'] === 'active') {
            $query->where('is_active', true);
        } elseif ($filters['status'] === 'pending') {
            $query->whereNull('activated_at');
        } elseif ($filters['status'] === 'disabled') {
            $query->where('is_active', false)->whereNotNull('activated_at');
        }

        $users = $query
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        $offices = Office::query()
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return [
            'users' => $users,
            'offices' => $offices,
            'filters' => $filters,
        ];
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        self::ensureAdmin($request);

        if ($this->isAdminAccount($user)) {
            return back()->with('error', 'Admin accounts cannot be edited in this module.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'role' => ['required', Rule::in(['employee', 'supervisor', 'dept-head', 'pmt'])],
            'office_id' => ['nullable', 'exists:offices,id'],
            'position' => ['nullable', 'string', 'max:255'],
        ]);

        $role = strtolower((string) $validated['role']);
        $officeId = !empty($validated['office_id']) ? (int) $validated['office_id'] : null;

        if (in_array($role, ['employee', 'supervisor', 'dept-head'], true) && !$officeId) {
            return back()->withInput()->with('error', 'Office is required for Employee, Supervisor, and Dept Head roles.');
        }

        if ($role === 'pmt') {
            $officeId = null;
        }

        if ($role === 'dept-head' && $officeId) {
            $conflictExists = User::query()
                ->where('role', 'dept-head')
                ->where('office_id', $officeId)
                ->whereKeyNot($user->id)
                ->exists();

            if ($conflictExists) {
                return back()->withInput()->with('error', 'Only one Dept Head is allowed per office.');
            }
        }

        $previousRole = strtolower((string) $user->role);
        $previousOfficeId = !empty($user->office_id) ? (int) $user->office_id : null;

        DB::transaction(function () use ($user, $validated, $role, $officeId, $previousRole, $previousOfficeId) {
            $user->name = (string) $validated['name'];
            $user->email = (string) $validated['email'];
            $user->role = $role;
            $user->office_id = $officeId;
            $user->position = isset($validated['position']) ? trim((string) $validated['position']) : null;
            $user->save();

            if ($previousRole === 'dept-head' && $previousOfficeId && ($role !== 'dept-head' || $previousOfficeId !== $officeId)) {
                $oldOffice = Office::query()->find($previousOfficeId);
                if ($oldOffice && (int) ($oldOffice->head_id ?? 0) === (int) $user->id) {
                    $oldOffice->update(['head_id' => null]);
                }
            }

            if ($role === 'dept-head' && $officeId) {
                Office::query()->whereKey($officeId)->update(['head_id' => $user->id]);
            }
        });

        return back()->with('success', 'User updated successfully.');
    }

    public function toggleActive(Request $request, User $user): RedirectResponse
    {
        self::ensureAdmin($request);

        if ($this->isAdminAccount($user)) {
            return back()->with('error', 'Admin accounts cannot be modified in this module.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return back()->with('success', $user->is_active ? 'User activated successfully.' : 'User deactivated successfully.');
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        self::ensureAdmin($request);

        if ($this->isAdminAccount($user)) {
            return back()->with('error', 'Admin accounts cannot be modified in this module.');
        }

        $temporaryPassword = Str::random(10);
        $user->password = Hash::make($temporaryPassword);
        $user->save();

        return back()
            ->with('success', 'Password reset successfully.')
            ->with('temporary_password', $temporaryPassword)
            ->with('temporary_password_user', $user->email);
    }

    public function sendEmployeeCode(Request $request, User $user): RedirectResponse
    {
        self::ensureAdmin($request);

        if ($this->isAdminAccount($user)) {
            return back()->with('error', 'Admin accounts cannot be modified in this module.');
        }

        if (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            return back()->with('error', 'This user does not have a valid email address.');
        }

        if (blank($user->employee_id)) {
            return back()->with('error', 'This user does not have a PMS employee code yet.');
        }

        Mail::to($user->email)->send(new PmsEmployeeIdIssuedMail(
            (string) $user->name,
            (string) $user->employee_id,
            (string) $user->email,
        ));

        return back()->with('success', 'Employee code sent to ' . $user->email . '.');
    }

    private static function ensureAdmin(Request $request): void
    {
        $actor = $request->user();
        abort_if(!$actor || strtolower((string) $actor->role) !== 'admin', 403, 'Unauthorized.');
    }

    private function isAdminAccount(User $user): bool
    {
        return strtolower((string) $user->role) === 'admin';
    }
}
