<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'nis'      => ['required', 'string', 'max:20', 'unique:students,nis', 'unique:users,username'],
            'name'     => ['required', 'string', 'max:100'],
            'gender'   => ['required', 'in:L,P'],
            'class_id' => ['required', 'integer', 'exists:classes,id'],
            'major_id' => ['required', 'integer', 'exists:majors,id'],
            'phone'    => ['nullable', 'string', 'max:15'],
            'address'  => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'nis.required'      => 'NIS wajib diisi.',
            'nis.unique'        => 'NIS sudah terdaftar di sistem.',
            'name.required'     => 'Nama siswa wajib diisi.',
            'gender.required'   => 'Jenis kelamin wajib dipilih.',
            'gender.in'         => 'Jenis kelamin tidak valid.',
            'class_id.required' => 'Kelas wajib dipilih.',
            'class_id.exists'   => 'Kelas yang dipilih tidak valid.',
            'major_id.required' => 'Jurusan wajib dipilih.',
            'major_id.exists'   => 'Jurusan yang dipilih tidak valid.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nis' => trim($this->nis),
        ]);
    }
}
