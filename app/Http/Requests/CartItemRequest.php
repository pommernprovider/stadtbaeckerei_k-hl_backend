<?php
// app/Http/Requests/CartItemRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartItemRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
            'variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'option_value_ids' => ['array'],
            'option_value_ids.*' => ['integer', 'exists:product_option_values,id'],
            'free_text' => ['array'], // [option_id => string]
        ];
    }
}
