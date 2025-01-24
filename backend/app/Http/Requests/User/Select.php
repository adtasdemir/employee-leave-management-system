<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;


/**
 * Class Select
 * 
 * Handles the validation logic for selecting a user. 
 * This class validates the `id` parameter, ensuring it's a valid integer 
 * that corresponds to an existing user in the database.
 * 
 * Inputs:
 * - `id` (integer): The ID of the user to select. It must be a valid integer 
 *   greater than or equal to 0 and must exist in the `users` table.
 * 
 * @package App\Http\Requests\User
 */
class Select extends FormRequest
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
            'id' => ['required', 'integer', 'min:0', 'exists:users,id']
        ];
    }


    /**
     * Prepare the data for validation.
     *
     * Merges the ID of the user to be selected from the route parameters
     * into the request data if it is not already present.
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