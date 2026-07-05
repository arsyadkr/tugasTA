<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login'    => ['required', 'string', 'max:100'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'login.required'    => 'Email atau NIS wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ];
    }

    /**
     * Trim whitespace dari input login.
     * Inject 'current_username' agar bisa dipakai oleh rule 'different:current_username'
     * di changePassword() tanpa perlu query manual ke database.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'login'            => trim($this->login),
            'current_username' => Auth::user()?->username ?? '',
        ]);
    }
}
