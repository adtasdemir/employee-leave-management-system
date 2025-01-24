<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class logout extends JsonResource
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
            'name' => $this->name,
            'surname' => $this->surname,
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
            'message' => "Successfully logged out",
            'code' => 200
        ];
    }
}
