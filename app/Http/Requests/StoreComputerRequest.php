<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreComputerRequest extends FormRequest
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
            'nama_pc' => 'required|string|max:200|unique:computers,nama_pc',
            'computer_type_id' => 'required|exists:computer_types,id',  
            'spesifikasi' => 'nullable|string|max:120',
            'status' => 'required|in:tersedia,rusak',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_pc.required' => 'Nama PC wajib diisi.',
            'nama_pc.max' => 'Maksimal 200 karakter.',
            'nama_pc.unique' => 'Nama PC sudah terdaftar, gunakan nama yang lain.',
            'computer_type_id.required' => 'Jenis PC wajib dipilih.',
            'computer_type_id.exists' => 'Jenis PC yang dipilih tidak valid.',
            'spesifikasi.max' => 'Spesifikasi maksimal 120 karakter.',
            'status.required' => 'Status PC wajib dipilih.',
            'status.in' => 'Status PC hanya boleh Tersedia atau Rusak.',
        ];
    } 
}
