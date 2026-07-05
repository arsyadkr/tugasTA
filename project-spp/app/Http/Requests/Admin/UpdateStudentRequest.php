<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    public function rules(): array
    {
        $student = $this->route('student');

        return [
            'nis' => [
                'required',
                'string',
                'max:20',

                // Unik di students KECUALI record siswa yang sedang diedit
                Rule::unique('students', 'nis')->ignore($student->id),

                // FIX RACE CONDITION: Unik di users KECUALI user yang terhubung
                // ke siswa ini. Tanpa ->ignore() di sini, NIS siswa itu sendiri
                // akan selalu trigger error karena users.username sudah terisi NIS-nya.
                // Ignore pakai $student->user_id, bukan $student->id.
                Rule::unique('users', 'username')->ignore($student->user_id),
            ],

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
            'nis.unique'        => 'NIS sudah digunakan siswa lain.',
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
        $this->merge(['nis' => trim($this->nis)]);
    }
}
