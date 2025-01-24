<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;


/**
 * Class Create
 * 
 * Handles the validation logic for creating a new user. This includes validating 
 * user input data such as name, surname, username, email, and password. 
 * The password must meet specific criteria, including length and character complexity.
 * 
 * Inputs:
 * - `name` (string): The first name of the user. It is required, should be a string, 
 *   and can be up to 255 characters long.
 * - `surname` (string): The last name of the user. It is required, should be a string, 
 *   and can be up to 255 characters long.
 * - `username` (string): The username for the user. It is required, should be a string, 
 *   and must be unique in the `users` table.
 * - `email` (string): The email address for the user. It is required, should be a valid 
 *   email format, and must be unique in the `users` table.
 * - `password` (string): The password for the user. It is required, should be at least 
 *   8 characters long, confirmed, and must contain at least one digit, one uppercase letter, 
 *   and one special character.
 * 
 * @package App\Http\Requests\User
 */
class Create extends FormRequest
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
     * @return array<string, array<int, string>> The validation rules
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'surname' => ['required', 'string', 'max:255'],

            'username' => [
                'required', 
                'string', 
                'max:255', 
                'unique:users,username', 
            ],

            'email' => [
                'required', 
                'string', 
                'email', 
                'max:255', 
                'unique:users,email', 
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed', 
                'regex:/[0-9]/',         
                'regex:/[A-Z]/',         
                'regex:/[@$!%*?&^#()_+=\-]/',
            ],
            
        ];
    }

   
    
}