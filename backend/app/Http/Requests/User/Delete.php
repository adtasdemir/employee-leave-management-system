<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;


/**
 * Class Delete
 * 
 * Handles the validation logic for deleting a user. 
 * This includes validating the `id` of the user to be deleted. 
 * The `id` must be a valid integer corresponding to an existing user in the database.
 * 
 * Inputs:
 * - `id` (integer): The ID of the user to be deleted. It must be a valid integer 
 *   and must exist in the `users` table.
 * 
 * @package App\Http\Requests\User
 */
class Delete extends FormRequest
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
            'id' => ['required', 'integer', 'min:0', 'exists:users,id'],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * Merges the ID from the route parameter into the request data so that it can be validated.
     *
     * @return void
     */

    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id') ?? null,
        ]);
    }
    
}