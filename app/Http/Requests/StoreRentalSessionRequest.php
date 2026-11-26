<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRentalSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'computer_id' => 'required|exists:computers,id',
            'durasi_jam' => 'required|integer|min:1|max:24',
            'paket' => 'nullable|in:reguler,paket_2jam,paket_3jam,paket_5jam,paket_malam',
            'member_id' => 'nullable|exists:members,id',
        ];
    }

    public function messages(): array
    {
        return [
            'computer_id.required' => 'Unit komputer wajib dipilih.',
            'computer_id.exists' => 'Unit komputer yang dipilih tidak ditemukan.',
            'durasi_jam.required' => 'Durasi sewa wajib diisi.',
            'durasi_jam.integer' => 'Durasi sewa harus berupa angka jam bulat.',
            'durasi_jam.min' => 'Durasi sewa minimal 1 jam.',
            'durasi_jam.max' => 'Durasi sewa maksimal 24 jam per transaksi.',
            'paket.in' => 'Paket sewa yang dipilih tidak valid.',
            'member_id.exists' => 'Member yang dipilih tidak ditemukan.',
        ];
    }
}