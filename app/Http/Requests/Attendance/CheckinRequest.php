<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class CheckinRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->role === 'student';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:present,sick,permit'],
            'excuse_notes' => ['nullable', 'string', 'max:500'],
            'medical_note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Pesan validasi kustom dalam Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Status kehadiran wajib dipilih.',
            'status.in' => 'Status kehadiran tidak valid (pilihan: Hadir, Sakit, atau Izin).',
            'excuse_notes.max' => 'Catatan keterangan maksimal 500 karakter.',
            'medical_note.max' => 'Catatan medis maksimal 1000 karakter.',
        ];
    }
}
