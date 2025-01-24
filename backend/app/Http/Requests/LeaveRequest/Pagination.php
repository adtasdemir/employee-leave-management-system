<?php
namespace App\Http\Requests\LeaveRequest;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class Pagination
 * 
 * Handles the validation logic for pagination-related requests.
 * This includes ensuring that the `user_id` is a valid integer, if provided.
 * 
 * Inputs:
 * - `user_id` (integer, nullable): The ID of the user for whom the leave requests are being paginated.
 *   It must be a positive integer if provided.
 * 
 * @package App\Http\Requests\LeaveRequest
 */
class Pagination extends FormRequest
{
    
    /**
     * Determine if the user is authorized to make this pagination request.
     *
     * @return bool Always returns true, indicating any user can make this request.
     */

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Returns the validation rules for the pagination request.
     * 
     * @return string[] Validation rules.
     */
    public function rules(): array
    {

        return [
            'user_id' => ['nullable', 'integer', 'min:1'],
        ];
    }
   
}