<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Http\Responses\LoginResponse;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);

        RateLimiter::for('login', function (Request $request) {
            $name = (string) $request->input('name');
            return Limit::perMinute(5)->by(Str::lower($name).'|'.$request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        Fortify::authenticateUsing(function (Request $request) {
            $loginField = Fortify::username();
            $value = trim((string) $request->input($loginField));

            if (! $value || ! $request->password) {
                return null;
            }

            $normalizedValue = Str::lower($value);
            $user = User::query()
                ->whereRaw('LOWER(name) = ?', [$normalizedValue])
                ->orWhereRaw('LOWER(email) = ?', [$normalizedValue])
                ->orWhereRaw('LOWER(employee_id) = ?', [$normalizedValue])
                ->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                return null;
            }

            if (! $user->activated_at) {
                $key = $loginField ?: 'name';
                throw ValidationException::withMessages([
                    $key => 'Activate account first.',
                ]);
            }

            if (! $user->is_active) {
                $key = $loginField ?: 'name';
                throw ValidationException::withMessages([
                    $key => 'This account is currently disabled.',
                ]);
            }

            return $user;
        });
    }
}
