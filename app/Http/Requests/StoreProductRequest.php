<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'is_new' => ['required', 'boolean'],
            'price' => ['required', 'integer'],
            'accept_trade' => ['required', 'boolean'],
            'payment_methods' => ['array', 'min:1'],
            'payment_methods.*' => ['exists:payment_methods,key'],
        ];
    }
}
