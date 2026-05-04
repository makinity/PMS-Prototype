<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();
        if ($user) {
            $request->attributes->set('audit_actor_snapshot', [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role,
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Logged in.']);
        }

        return redirect('/dashboard');
    }
}
