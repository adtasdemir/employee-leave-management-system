<?php

namespace App\Http\Resources\LeaveRequest;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class LeaveRequest
 * 
 * This class is used to transform the leave request data into an array representation, 
 * which is used for JSON responses. It includes important details such as the leave request's 
 * ID, start and end dates, status, associated user information, rejection reason (if any), 
 * and timestamps for creation and update.
 *
 * @package App\Http\Resources\LeaveRequest
 */
class LeaveRequest extends JsonResource
{
    /**
     * Transform the leave request resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     * 
     * This method returns an array representation of the leave request resource,
     * including fields such as ID, start and end dates, status, user ID, username,
     * rejection reason, and timestamps for creation and last update.
     */

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'user_id' => $this->user_id,
            'username' => $this->user->username,
            'rejection_reason' => $this->rejection_reason,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }

    /**
     * Add metadata to the resource.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     *
     * This method returns an associative array containing metadata about the
     * leave request resource, including boolean success indicator, a message
     * string, and a numeric code. The values of these keys are always the same
     * for successful requests.
     */
    public function with($request): array
    {
        return [
            'success' => true,
            'message' => 'Success',
            'code' => 200,
        ];
    }
}
