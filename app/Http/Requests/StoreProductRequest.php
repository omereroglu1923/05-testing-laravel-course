<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'  => 'required',
            'price' => 'required',
            'image' => 'nullable|image|max:2048',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
