<?php

// app/Http/Requests/PickupWindowsRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PickupWindowsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'date'      => ['required', 'date_format:Y-m-d'],
        ];
    }
}
