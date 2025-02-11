<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
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
            "author" => [ "string"],
            "title" => [ "string"],
            "inventory_number" => [ "string"],
            "barcode" => [ "integer"],
            "isbn" => [ "string"],
            "year_of_purchasing" => [ "date"],
            "publisher" => [ "string"],
            "published_year" => [ "date"],
            "supplier" => [ "string"],
            "categories_id" => [ "integer"],

        ];
    }
}
