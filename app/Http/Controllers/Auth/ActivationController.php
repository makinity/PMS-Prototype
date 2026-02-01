<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivationToken;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ActivationController extends Controller
{
    public function verify(Request $request): JsonResponse
    {
        $key = 'activate-verify:'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 10)) {
            return response()->json(['message' => 'Too many attempts. Please try again later.'], 429);
        }

        $data = $request->validate([
            'employee_id' => ['required', 'string', 'regex:/^EMP-\d{4}-\d{5}$/'],
            'email' => ['required', 'email'],
        ]);

        /** @var ?User $user */
        $user = User::where('employee_id', $data['employee_id'])
            ->whereRaw('LOWER(email) = ?', [Str::lower($data['email'])])
            ->first();

        if (! $user) {
            RateLimiter::hit($key, 300);
            return response()->json(['message' => 'Invalid Employee ID or Email.'], 422);
        }

        if ($user->is_active || $user->activated_at) {
            return response()->json(['message' => 'Account already activated. Please login.'], 409);
        }

        $token = Str::random(64);
        $tokenHash = hash('sha256', $token);

        ActivationToken::create([
            'user_id' => $user->id,
            'token_hash' => $tokenHash,
            'expires_at' => now()->addMinutes(10),
            'used_at' => null,
        ]);

        RateLimiter::clear($key);

        return response()->json([
            'message' => 'Verified',
            'token' => $token,
            'user' => [
                'name' => $user->name,
                'role' => $user->role,
                'employee_id' => $user->employee_id,
            ],
        ]);
    }

    public function complete(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'profile_photo' => ['sometimes', 'nullable', 'image', 'max:5120'],
        ]);

        $tokenHash = hash('sha256', $data['token']);

        /** @var ?ActivationToken $activationToken */
        $activationToken = ActivationToken::where('token_hash', $tokenHash)
            ->whereNull('used_at')
            ->first();

        if (! $activationToken || $activationToken->expires_at->isPast()) {
            return response()->json(['message' => 'Invalid or expired token.'], 422);
        }

        $user = $activationToken->user;

        if (! $user) {
            return response()->json(['message' => 'Invalid or expired token.'], 422);
        }

        if ($user->is_active || $user->activated_at) {
            return response()->json(['message' => 'Account already activated. Please login.'], 409);
        }

        DB::transaction(function () use ($activationToken, $user, $data) {
            if (isset($data['profile_photo']) && $data['profile_photo']) {
                $path = $data['profile_photo']->store('profile-photos', 'public');
                $user->profile_photo_path = $path;
            }

            $user->password = Hash::make($data['password']);
            $user->is_active = true;
            $user->activated_at = now();
            $user->save();

            $activationToken->used_at = now();
            $activationToken->save();
        });

        return response()->json(['message' => 'Account activated successfully.']);
    }
}
