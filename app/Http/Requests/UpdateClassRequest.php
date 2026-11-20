<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isLecturer();
    }

    public function rules(): array
    {
        $classId = $this->route('class') instanceof \App\Models\ClassModel 
            ? $this->route('class')->id 
            : $this->route('class');

        return [
            'code'       => ['required', 'string', 'max:20', Rule::unique('classes', 'code')->ignore($classId)],
            'name'       => ['required', 'string', 'max:100'],
            'room'       => ['required', 'string', 'max:50'],
            'day'        => ['required', 'string', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu'],
            'time_start' => ['required', 'date_format:H:i'],
            'time_end'   => ['required', 'date_format:H:i', 'after:time_start'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required'        => 'Kode kelas wajib diisi.',
            'code.unique'          => 'Kode kelas ini sudah digunakan oleh kelas lain.',
            'name.required'        => 'Nama kelas wajib diisi.',
            'room.required'        => 'Ruangan perkuliahan wajib diisi.',
            'day.required'         => 'Hari perkuliahan wajib dipilih.',
            'day.in'               => 'Pilihan hari tidak valid.',
            'time_start.required'  => 'Jam mulai perkuliahan wajib diisi.',
            'time_start.date_format' => 'Format jam mulai harus JJ:MM (contoh: 08:00).',
            'time_end.required'    => 'Jam selesai perkuliahan wajib diisi.',
            'time_end.date_format' => 'Format jam selesai harus JJ:MM (contoh: 10:30).',
            'time_end.after'       => 'Jam selesai harus setelah jam mulai perkuliahan.',
        ];
    }
}
