<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules()
    {  
        return [
            'first_name' => ['required', 'string', 'max:25', 'regex:/^[\pL]+$/u'],
            'last_name' => ['required', 'string', 'max:25', 'regex:/^[\pL]+$/u'],
            'birth_place' => ['required', 'string', 'max:25', 'regex:/^[\pL]+$/u'],
            'birth_date' => 'required|string|max:10',
            'phone' => [
                'required',
                'string',
                'min:12',
                'regex:/^\+36\d{9}$/',
            ],
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
            'first_name.required' => 'A vezetéknév megadása kötelező!',
            'first_name.regex' => 'A vezetéknév csak betűket tartalmazhat.',
            'last_name.required' => 'A keresztnév megadása kötelező!',
            'last_name.regex' => 'A keresztnév csak betűket tartalmazhat.',
            'birth_place.required' => 'A születési hely megadása kötelező!',
            'birth_place.regex' => 'A születési hely csak betűket tartalmazhat.',
            'phone.regex' => 'A telefonszám formátuma érvénytelen. Csak a +36 előhívószámot használhatod.',
            'phone.min' => 'A telefonszám legalább 12 karakter hosszú kell legyen (pl. +36 20 1234567).',
            'email.unique' => 'Ez az email cím már regisztrálva van.',
        ];
    }
}
