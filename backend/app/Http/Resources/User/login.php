<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class Login
 *
 * This class transforms the authenticated user resource into an array, providing essential
 * information such as user details, role, and an API token after a successful login.
 *
 * @package App\Http\Resources\User
 */
class login extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'surname' => $this->surname,
            'username' => $this->username,
            'email' => $this->email,
            'status' => $this->status,
            'annual_leave_days' => $this->annual_leave_days ?? 20,
            'remaining_annual_leave_days' =>  $this->remaining_annual_leave_days ?? 20,
            'role_id' => $this->role ? $this->role_id : 2,
            'role_title' => $this->role ? $this->role->title : "User",
            'token' =>  $this->createToken('ApiAppToken')->plainTextToken,
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
     * user resource, including boolean success indicator, a message string, and
     * a numeric code. The values of these keys are always the same for successful
     * requests.
     */
    public function with($request): array
    {
        return [
            'success' => true,
            'code' => 200,
            'message' => 'Success',
        ];
    }
}
