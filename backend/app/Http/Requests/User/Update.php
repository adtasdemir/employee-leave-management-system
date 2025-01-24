<?php

namespace App\Http\Requests\User;
use App\Enums\UserStatus;
use Illuminate\Foundation\Http\FormRequest;


/**
 * Class Update
 * 
 * Handles the validation logic for updating a user's information. 
 * The request validates the `id` of the user to be updated, as well as optional fields 
 * such as `name`, `surname`, `username`, `email`, and `status`. The validation also checks 
 * that the updated username and email are unique for that user, ensuring that no duplicates exist 
 * in the database. Additionally, a custom validation rule is implemented to ensure that at least one 
 * field among `name`, `surname`, `username`, `email`, or `status` is provided for updating.
 * 
 * Inputs:
 * - `id` (integer): The ID of the user to be updated. It must exist in the `users` table.
 * - `name` (string, optional): The name of the user.
 * - `surname` (string, optional): The surname of the user.
 * - `username` (string, optional): The username of the user. It must be unique except for the user being updated.
 * - `email` (string, optional): The email of the user. It must be unique except for the user being updated.
 * - `status` (enum, optional): The status of the user, based on `UserStatus`.
 * 
 * @package App\Http\Requests\User
 */
class Update extends FormRequest
{

    /**
     * The fields that can be updated in the user update request.
     * 
     * @var array<string>
     */
    const FIELDS = ['name', 'surname', 'username', 'email', 'status'];
    
    
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
     * @return array The validation rules
     */
    public function rules(): array
    {
        $userId = $this->route('id');
        $statusList = implode(',', array_map(fn ($status) => $status->value, UserStatus::cases()));

        return [
            'id' => ['required', 'integer', 'min:0', 'exists:users,id'],

            'name' => ['nullable', 'string', 'max:255'],

            'surname' => ['nullable', 'string', 'max:255'],

            'username' => [
                'nullable', 
                'string', 
                'max:255', 
                'unique:users,username,' . $userId, 
            ],

            'email' => [
                'nullable', 
                'string', 
                'email', 
                'max:255', 
                'unique:users,email,' . $userId,
            ],

            'status' => [
                'nullable', 
                'in:' . $statusList,
            ],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * Merges the ID of the user to be updated from the route parameters
     * into the request data, along with any fields defined in the 
     * `FIELDS` constant, if they are present in the request.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'id' => $this->route('id') ?? null,
            $this->only(self::FIELDS),
        ]);
        
    }

    
    /**
     * Adds a custom validation rule to check that at least one of the updatable fields
     * has been provided. If none of the fields have been provided, adds a custom error.
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    protected function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $allNull = collect(self::FIELDS)->every(function ($field) {
                return is_null($this->input($field));
            });
    
            if ($allNull) {
                $validator->errors()->add('custom', 'No updatable data provided.');
            }
        });
    }
}