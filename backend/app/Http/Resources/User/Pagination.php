<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class Logout
 *
 * This class transforms the user resource into an array after a successful logout, providing 
 * basic user information like their name and surname, along with metadata indicating the success 
 * of the logout action.
 *
 * @package App\Http\Resources\User
 */
class Pagination extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'data' => User::collection($this->items()), 
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

    public function with($request): array
    {
        return [
            'success' => true,
            'code' => 200,
            'message' => 'Success',
        ];
    }


}
