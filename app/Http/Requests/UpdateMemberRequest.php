<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMemberRequest extends FormRequest
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
            'nama' => 'required|string|max:100',
            'no_hp' => ['required', 'string', 'max:15', Rule::unique('members', 'no_hp')->ignore($this->route('member'))],
        ];
    }

    public function messages(): array {
        return[
            'nama.required' => 'Nama wajib diisi.',
            'nama.max' => 'Maksimal 100 karakter.',
            'no_hp.required' => 'Nomor telepon wajib diisi.',
            'no_hp.max' => 'Maksimal 15 karakter.',
            'no_hp.unique' => 'Nomor HP sudah terdaftar, gunakan nomor lain.',
        ];
    }
}
