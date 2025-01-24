<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class Login
 * 
 * Handles the validation logic for logging in a user. 
 * This includes validating the email and password fields. 
 * The email must be a valid email address, and the password is required as a string.
 * 
 * Inputs:
 * - `email` (string): The email address of the user. It must be a valid email format.
 * - `password` (string): The password for the user. It must be a non-empty string.
 * 
 * @package App\Http\Requests\User
 */
class Login extends FormRequest
{
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool true if the user is authorized, false otherwise
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string> The validation rules
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ];
    }
}