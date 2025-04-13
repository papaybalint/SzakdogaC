<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateItemRequest extends FormRequest
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
    public function rules()
    {
        $currentYear = now()->year;
        
        return [
            'author' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'inventory_number' => 'required|string|max:50',
            'barcode' => 'required|numeric|digits_between:1,20',
            'isbn' => 'required|string|max:20',
            'year_of_purchasing' => 'required|date_format:Y.m.d|before_or_equal:today',
            'published_year' => "required|numeric|digits:4|max:$currentYear",
            'supplier' => 'required|string|max:255',
            'categories_id' => 'required|exists:categories,id', 
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'author.required' => 'A szerző mező kitöltése kötelező.',
            'title.required' => 'A cím mező kitöltése kötelező.',
            'inventory_number.required' => 'A készlet szám mező kitöltése kötelező.',
            'barcode.required' => 'A vonalkód mező kitöltése kötelező.',
            'isbn.required' => 'Az ISBN mező kitöltése kötelező.',
            'year_of_purchasing.required' => 'A beszerzési év mező kitöltése kötelező.',
            'published_year.required' => 'A kiadás éve mező kitöltése kötelező.',
            'supplier.required' => 'A szállító mező kitöltése kötelező.',
            'categories_id.required' => 'A kategória mező kitöltése kötelező.',
        ];
    }
}
