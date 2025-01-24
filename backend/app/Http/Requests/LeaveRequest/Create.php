<?php

namespace App\Http\Requests\LeaveRequest;


use App\Helpers\Utils;
use Illuminate\Validation\Validator;
use App\Services\LeaveRequestService;
use Illuminate\Foundation\Http\FormRequest;


/**
 * Class Create
 * 
 * Handles the validation logic for creating a leave request.
 * This includes ensuring that:
 * - The start date is today or later.
 * - The end date is after the start date.
 * - The number of requested leave days does not exceed the user's remaining annual leave.
 * - There are no conflicts with existing leave requests.
 * 
 * Inputs:
 * - `start_date` (string): The start date of the leave request. Must be today or a future date.
 * - `end_date` (string): The end date of the leave request. Must be after the start date.
 * 
 * @package App\Http\Requests\LeaveRequest
 */
class Create extends FormRequest
{

    protected $leaveRequestService;

    /**
     * Create a new instance of the Create leave request request.
     *
     * @param LeaveRequestService $leaveRequestService
     *      The leave request service to access the leave request service.
     * @return void
     */
    public function __construct(LeaveRequestService $leaveRequestService)
    {
        $this->leaveRequestService = $leaveRequestService;
    }
    
    
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
     * Get the validation rules for creating a leave request.
     *
     * @return array An array of validation rules, ensuring that the start date is 
     *               today or later, and the end date is after the start date.
     */

    public function rules(): array
    {
        return [
            'start_date' => [
                'required',
                'date',
                'after_or_equal:today', 
            ],
            'end_date' => [
                'required',
                'date',
                'after:start_date', 
            ],
        ];
    }


    /**
     * Run additional validation after the default validation rules have been 
     * checked. This method is called after the default validation rules have 
     * been checked.
     * 
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator(Validator $validator): void
    {
        if ($validator->fails()) {
            return;
        }

        $validator->after(function ($validator) {
            $user = $this->user();
            $startDate = $this->input('start_date');
            $endDate = $this->input('end_date');

            $numberOfRequestedDays = Utils::diffInDays($startDate, $endDate); 
            if ($numberOfRequestedDays > $user->remaining_annual_leave_days) {
                $validator->errors()->add('end_date', 'The requested leave days exceed your remaining annual leave days.');
            }

            if ($this->leaveRequestService->hasConflictingLeaveRequest(auth()->user(), $startDate, $endDate)) {
                $validator->errors()->add('start_date', 'The leave request conflicts with existing leave requests.');
            }
           
        });
    }
  
}
