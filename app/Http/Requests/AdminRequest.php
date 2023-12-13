<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:50|regex:/^[\p{Script=Hiragana}\p{Script=Katakana}\p{Script=Han}]+$/u', // 漢字、ひらがな、カタカナであることのバリデーション
            'last_name' => 'required|string|max:50|regex:/^[\p{Script=Hiragana}\p{Script=Katakana}\p{Script=Han}]+$/u', // 漢字、ひらがな、カタカナであることのバリデーション
            'email' => 'required|email',
            'password' => 'required | min:4',
            'emp_number' => 'required|integer',
            'role_id' => 'required|integer',
            'hire_date' => 'required',
        ];
    }
}
