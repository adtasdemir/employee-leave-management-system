<?php

namespace App\Http\Controllers\Api;
use Exception;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Login as LoginRequest;
use App\Http\Requests\User\Logout as LogoutRequest;
use App\Contracts\Service\UserServiceContract;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\User\Login as LoginResource;
use App\Http\Resources\User\Logout as LogoutResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthController extends Controller
{
    public UserServiceContract $service;
    /**
     * @var array
     */

    /**
     * TagsController constructor.
     * 
     * @param UserServiceContract $service
     */
    public function __construct(UserServiceContract $service)
    {
        $this->service = $service;
    }

    /**
     * Login user and return a token.
     * 
     * @param LoginRequest $request
     * @return JsonResource
     */
    public function login(LoginRequest $request): JsonResource
    {
        try{
            return new LoginResource($this->service->login($request->validated()));
        }catch(Exception $e){
            return new ErrorResource($e);
        }
    }

    /**
     * Logout user and revoke the token.
     * 
     * @param LogoutRequest $request
     * @return JsonResource
     */
    public function logout(LogoutRequest $request): JsonResource
    {
        try{
            $request->user()->tokens()->delete();
            return new LogoutResource($request->user());
        }catch(Exception $e){
            return new ErrorResource($e);
        }
    }
}
