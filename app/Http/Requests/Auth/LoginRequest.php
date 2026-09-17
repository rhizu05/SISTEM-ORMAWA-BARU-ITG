<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // BR-01: mahasiswa dapat login memakai NIM/username, bukan hanya email.
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $login = (string) $this->string('email');
        $password = (string) $this->input('password');
        $remember = $this->boolean('remember');

        // Coba email lebih dulu, lalu fallback ke NIM/username (hanya akun yang aktif)
        $authenticated = Auth::attempt(['email' => $login, 'password' => $password, 'status_akun' => 'aktif'], $remember)
            || Auth::attempt(['username' => $login, 'password' => $password, 'status_akun' => 'aktif'], $remember);

        if (! $authenticated) {
            // Cek apakah akun ada tapi nonaktif
            $userInactive = \App\Models\User::where('email', $login)->orWhere('username', $login)->first();
            if ($userInactive && $userInactive->status_akun === 'nonaktif' && \Illuminate\Support\Facades\Hash::check($password, $userInactive->password)) {
                RateLimiter::hit($this->throttleKey());
                throw ValidationException::withMessages([
                    'email' => 'Akun Anda berstatus nonaktif. Silakan hubungi administrator.',
                ]);
            }

            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
