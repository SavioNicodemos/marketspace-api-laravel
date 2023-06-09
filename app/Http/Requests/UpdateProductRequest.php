<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name' => ['string'],
            'description' => ['string'],
            'is_new' => ['boolean'],
            'price' => ['integer'],
            'accept_trade' => ['boolean'],
            'is_active' => ['boolean'],
            'payment_methods' => ['array'],
            'payment_methods.*' => ['exists:payment_methods,key'],
        ];
    }
}
