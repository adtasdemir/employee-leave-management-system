<?php

namespace App\Http\Requests\User;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class Pagination
 * 
 * Handles the validation for pagination-related requests. 
 * This class currently does not enforce any validation rules, but it can 
 * be extended to validate pagination parameters (e.g., `page`, `per_page`) 
 * if needed in the future.
 * 
 * This request is typically used when fetching paginated data (e.g., lists of users), 
 * but the actual validation logic for pagination might be added in other methods or controllers.
 * 
 * @package App\Http\Requests\User
 */
class Pagination extends FormRequest
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
        return [];
    }

}