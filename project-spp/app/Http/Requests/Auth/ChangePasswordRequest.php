<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Form Request terpisah untuk change password.
 * Lebih bersih daripada validate() inline di controller.
 * Inject 'current_username' otomatis untuk rule 'different'.
 */
class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                // Pastikan password baru berbeda dari NIS (username)
                // current_username di-inject via prepareForValidation()
                'different:current_username',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'password.required'  => 'Password baru wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.different' => 'Password baru tidak boleh sama dengan NIS Anda.',
        ];
    }

    /**
     * Inject username siswa yang sedang login ke dalam request data
     * agar rule 'different:current_username' bisa bekerja.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'current_username' => Auth::user()?->username ?? '',
        ]);
    }
}
