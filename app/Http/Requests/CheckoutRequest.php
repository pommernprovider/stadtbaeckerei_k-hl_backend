<?php
// app/Http/Requests/CheckoutRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'max:255'],
            'phone'       => ['required', 'string', 'max:50'],
            'adress'        => ['required', 'string', 'max:255'],
            'tax'        => ['required', 'string', 'max:255'],
            'city'        => ['required', 'string', 'max:255'],
            'note'        => ['nullable', 'string', 'max:2000'],
            'branch_id'   => ['required', 'integer', 'exists:branches,id'],
            'date'        => ['required', 'date_format:Y-m-d'],
            'window_start' => ['required', 'date_format:H:i:s'],
            'customer_note' => ['nullable', 'string', 'max:1000'],

            'agree'       => ['accepted'], // DSGVO/AGB Checkbox
        ];
    }

    public function messages(): array
    {
        return [
            'window_start.required' => 'Bitte ein Abholfenster wählen.',
            'branch_id.exists'      => 'Ungültige Filiale.',
        ];
    }
}
