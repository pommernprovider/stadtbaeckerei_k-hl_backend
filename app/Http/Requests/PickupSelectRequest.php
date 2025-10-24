<?php
// app/Http/Requests/PickupSelectRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PickupSelectRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'branch_id'   => ['required', 'integer', 'exists:branches,id'],
            'date'        => ['required', 'date_format:Y-m-d'],
            'window_start' => ['required', 'date_format:H:i:s'], // Startzeit des Fensters
        ];
    }
}
