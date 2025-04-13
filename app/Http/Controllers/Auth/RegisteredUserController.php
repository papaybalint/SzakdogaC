<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => 'required|string|max:25|unique:' . User::class . '|regex:/^[a-zA-Z0-9]+$/',
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
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            // Egyedi hibaüzenetek
            'username.required' => 'A felhasználónév megadása kötelező!',
            'username.regex' => 'A felhasználónév csak betűket és számokat tartalmazhat.',
            'first_name.required' => 'A vezetéknév megadása kötelező!',
            'first_name.regex' => 'A vezetéknév csak betűket tartalmazhat.',
            'last_name.required' => 'A keresztnév megadása kötelező!',
            'last_name.regex' => 'A keresztnév csak betűket tartalmazhat.',
            'birth_place.required' => 'A születési hely megadása kötelező!',
            'birth_place.regex' => 'A születési hely csak betűket tartalmazhat.',
            'phone.regex' => 'A telefonszám formátuma érvénytelen. Csak a +36 előhívószámot használhatod.',
            'phone.min' => 'A telefonszám legalább 12 karakter hosszú kell legyen (pl. +36 20 1234567).',
            'email.unique' => 'Ez az email cím már regisztrálva van.',
            'password.required' => 'A jelszó megadása kötelező.',
            'password.confirmed' => 'A jelszó megerősítése nem egyezik.',
        ]);
        $user = User::create([
            'username' => $request->username,
            'birth_place' => $request->birth_place,
            'birth_date' => $request->birth_date,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect("/");
    }
}
