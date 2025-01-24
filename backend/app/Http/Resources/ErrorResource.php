<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class User
 *
 * This class transforms the user resource into an array format for API responses.
 * It includes the user's detailed information such as their ID, name, email, 
 * status, role, and other details related to leave days, along with metadata 
 * indicating the success of the request.
 *
 * @package App\Http\Resources\User
 */
class ErrorResource extends JsonResource
{
   
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     *
     * This method returns an associative array containing error information. The
     * array contains the following keys:
     *
     * - `data`: An array of validation errors, if applicable.
     * - `code`: The HTTP status code of the error.
     * - `success`: A boolean indicating whether the request was successful.
     * - `message`: A message describing the error.
     */
    public function toArray($request): array
    {
        $exception = $this->resource;

        $code = $exception instanceof HttpException
            ? $exception->getStatusCode()
            : ($exception->getCode() ?: 500); 

        $message = $exception->getMessage() ?: 'An unexpected error occurred.';

        if (method_exists($exception, 'errors')) {
            $data = $exception->errors();
        }

        return [
            'data' => $data ?? [],
            'code' => $code,
            'success' => false, 
            'message' => $message,
        ];
    }

    /**
     * Add metadata to the resource.
     * 
     * @param \Illuminate\Http\Request $request
     * 
     * @return array
     * 
     * This method returns an empty array, since the error resource does not
     * require any additional metadata.
     */
    public function with($request): array
    {
        return []; 
    }
}
