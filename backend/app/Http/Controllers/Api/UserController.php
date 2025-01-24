<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\ValidationResource;
use App\Contracts\Service\UserServiceContract;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\User as UserResource;
use App\Http\Requests\User\Create as CreateRequest;
use App\Http\Requests\User\Delete as DeleteRequest;
use App\Http\Requests\User\Select as SelectRequest;
use App\Http\Requests\User\Update as UpdateRequest;
use App\Http\Requests\User\Pagination as PaginationRequest;
use App\Http\Resources\User\Pagination as PaginationResource;

class UserController extends Controller
{
    public UserServiceContract $service;
    /**
     * @var array
     */

    /**
     * UsersController constructor.
     * 
     * @param UserServiceContract $service
     */
    public function __construct(UserServiceContract $service)
    {
        $this->service = $service;
    }

    
    
    /**
     * Create a new User
     * 
     * @param CreateRequest $request
     * 
     * @return JsonResource
     */
    public function createUser(CreateRequest $request): JsonResource
    {
        try{
            return new UserResource($this->service->createUser($request->validated()));
        }catch(Exception $e){
            return new ErrorResource($e);
        }
    }

   
    
    /**
     * Delete a User
     * 
     * @param DeleteRequest $request
     * 
     * @return JsonResource
     */
    public function deleteUser(DeleteRequest $request): JsonResource
    {
        try{
            return new UserResource($this->service->deleteUser($request->validated()));
        }catch(Exception $e){
            return new ErrorResource($e);
        }
    }

    /**
     * List Users
     * 
     * @param PaginationRequest $request
     * 
     * @return JsonResource
     */
     public function listUsers(PaginationRequest  $request): JsonResource
    {
        try{
            return new PaginationResource($this->service->listUsers($request->validated()));
        }catch(Exception $e){
            return new ErrorResource($e);
        }
    }

    
    /**
     * Select a User by id
     * 
     * @param SelectRequest $request
     * 
     * @return JsonResource
     */
    public function selectUser(SelectRequest $request): JsonResource
    {
        try{
            return new UserResource($this->service->selectUser($request->validated()));
        }catch(Exception $e){
            return new ErrorResource($e);
        }
    }

   
    /**
     * Update a User
     * 
     * @param UpdateRequest $request
     * 
     * @return JsonResource
     */

    public function updateUser(UpdateRequest $request): JsonResource
    {
        try{
            return new UserResource($this->service->updateUser($request->validated()));
        }catch(Exception $e){
            return new ErrorResource($e);
        }
    }

}