<?php

namespace App\Http\Resources\LeaveRequest;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\LeaveRequest\LeaveRequest;

/**
 * Class Pagination
 * 
 * This class is used to transform a paginated collection of leave requests into an array representation.
 * It includes the paginated data (`LeaveRequest` items) and pagination metadata (e.g., total items, current page, etc.).
 *
 * @package App\Http\Resources\LeaveRequest
 */
class Pagination extends JsonResource
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
            'data' => LeaveRequest::collection($this->items()), 
            'pagination' => [
                'total' => $this->total(),
                'current_page' => $this->currentPage(),
                'per_page' => $this->perPage(),
                'last_page' => $this->lastPage(),
                'first_page_url' => $this->url(1),
                'last_page_url' => $this->url($this->lastPage()),
                'next_page_url' => $this->nextPageUrl(),
                'prev_page_url' => $this->previousPageUrl(),
                'from' => $this->firstItem(),
                'to' => $this->lastItem(),
                'path' => $this->path(),
            ],
        ];
    }

    /**
     * Add additional meta information to the response.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
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
