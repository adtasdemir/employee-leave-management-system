<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class Logout
 * 
 * Handles the validation logic for logging out a user. 
 * Currently, no specific input validation is required for this action.
 * 
 * This request class is typically used when handling the user's intent to log out,
 * and it's expected that other middleware or functionality will handle the actual 
 * process of logging the user out.
 * 
 * @package App\Http\Requests\User
 */
class Logout extends FormRequest
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
     * @return array<string, mixed> The validation rules
     */
    public function rules(): array
    {
        return [
            
        ];
    }
}