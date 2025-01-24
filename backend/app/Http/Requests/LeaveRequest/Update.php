<?php

namespace App\Http\Requests\LeaveRequest;
use Illuminate\Validation\Rule;
use App\Enums\LeaveRequestStatus;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class Update
 * 
 * Handles the validation logic for updating a leave request. 
 * This includes validating the `id` of the leave request and the `status` 
 * of the request. The `id` must correspond to an existing leave request 
 * with a `pending` status, and the `status` must be one of the allowed 
 * statuses defined in the `LeaveRequestStatus` enum.
 * 
 * Inputs:
 * - `id` (integer): The ID of the leave request being updated. 
 *   It must be a valid ID of a request with a `pending` status.
 * - `status` (string): The status to update the leave request to. 
 *   It must be one of the valid statuses defined in the `LeaveRequestStatus` enum 
 *   (e.g., `pending`, `approved`, `rejected`).
 * 
 * @package App\Http\Requests\LeaveRequest
 */
class Update extends FormRequest
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
     * @return array
     */
    public function rules(): array
    {
        $statusList = implode(',', array_map(fn ($status) => $status->value, LeaveRequestStatus::cases()));
        return [
            'id' => [
                'required',
                'integer',
                'min:0',
                Rule::exists('leave_requests')->where(function ($query) {
                    $query->where('status', 'pending');
                }),
            ],
            'status' => [
                'required', 
                'in:' . $statusList,
            ],
        ];
    }

    /**
     * Prepare the data for validation.
     * 
     * This method automatically sets the 'status' field based on the request's URI.
     * If the URI contains 'approve-request', it sets the status to 'approved'.
     * If the URI contains 'reject-request', it sets the status to 'rejected'.
     * It also merges the 'id' from the route parameters into the request data.
     *
     * @return void
     */

    protected function prepareForValidation(): void
    {
        $routeUri = $this->path(); 

        if (strpos($routeUri, 'approve-request') !== false) {
            $status = LeaveRequestStatus::APPROVED->value;
        } elseif (strpos($routeUri, 'reject-request') !== false) {
            $status = LeaveRequestStatus::REJECTED->value;
        }

        $this->merge([
            'id' => $this->route('id') ?? null,
            'status' => $status,
        ]);
    }
}