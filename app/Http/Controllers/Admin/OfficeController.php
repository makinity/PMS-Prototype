<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Office;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OfficeController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureAdminAccess($request);

        $offices = Office::query()
            ->with('head:id,name,email')
            ->withCount([
                'employees',
                'unitWorkPlans',
                'employees as supervisors_count' => function ($query) {
                    $query->where('role', 'supervisor');
                },
            ])
            ->with([
                'employees' => function ($query) {
                    $query->select('id', 'name', 'email', 'role', 'office_id', 'position', 'is_active', 'activated_at')
                        ->orderBy('name');
                },
            ])
            ->orderBy('name')
            ->get();

        $deptHeads = User::query()
            ->where('role', 'dept-head')
            ->select('id', 'name', 'email', 'office_id')
            ->orderBy('name')
            ->get();

        return view('admin.office', compact('offices', 'deptHeads'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureAdminAccess($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:offices,code'],
            'head_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'dept-head')),
            ],
        ]);

        DB::transaction(function () use ($validated) {
            $office = Office::query()->create([
                'name' => trim((string) $validated['name']),
                'code' => strtoupper(trim((string) $validated['code'])),
                'head_id' => null,
            ]);

            $this->assignDeptHead($office, isset($validated['head_id']) ? (int) $validated['head_id'] : null);
        });

        return back()->with('success', 'Office created successfully.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->ensureAdminAccess($request);
        $office = Office::query()->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('offices', 'code')->ignore($office->id)],
            'head_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'dept-head')),
            ],
        ]);

        DB::transaction(function () use ($office, $validated) {
            $office->update([
                'name' => trim((string) $validated['name']),
                'code' => strtoupper(trim((string) $validated['code'])),
            ]);

            $this->assignDeptHead($office, isset($validated['head_id']) ? (int) $validated['head_id'] : null);
        });

        return back()->with('success', 'Office updated successfully.');
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $this->ensureAdminAccess($request);
        $office = Office::query()->findOrFail($id);

        $office->loadCount(['employees', 'unitWorkPlans']);

        if ((int) $office->employees_count > 0 || (int) $office->unit_work_plans_count > 0) {
            return back()->with('error', 'Cannot delete office. Related employees or UWPs exist.');
        }

        $office->delete();

        return back()->with('success', 'Office deleted successfully.');
    }

    private function assignDeptHead(Office $office, ?int $headId): void
    {
        if (!$headId) {
            $office->update(['head_id' => null]);

            return;
        }

        $deptHead = User::query()
            ->whereKey($headId)
            ->where('role', 'dept-head')
            ->first();

        if (!$deptHead) {
            $office->update(['head_id' => null]);

            return;
        }

        Office::query()
            ->where('head_id', $deptHead->id)
            ->whereKeyNot($office->id)
            ->update(['head_id' => null]);

        $office->update(['head_id' => $deptHead->id]);

        if ((int) ($deptHead->office_id ?? 0) !== (int) $office->id) {
            $deptHead->update(['office_id' => $office->id]);
        }
    }

    private function ensureAdminAccess(Request $request): void
    {
        $user = $request->user();

        abort_if(!$user || strtolower((string) $user->role) !== 'admin', 403, 'Unauthorized.');
    }
}
