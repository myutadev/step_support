<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResidenceRequest extends FormRequest
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
            'name' => 'required|string|max:50|',
            'contact_name' => 'nullable|string|max:50|',
            'contact_phone' => 'nullable|regex:/^[\+\d\s\-()]+$/u', // 電話番号のバリデーション（数字、プラス記号、スペース、ハイフン、括弧を許可）
            'contact_email' => 'nullable|email:rfc', // メールアドレスのバリデーション（RFC準拠、DNSチェックを含む）
        ];
    }
}
