<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Resources\ErrorResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Contracts\Service\LeaveRequestServiceContract;
use App\Http\Requests\LeaveRequest\Create as CreateLeaveRequest;
use App\Http\Requests\LeaveRequest\Update as UpdateLeaveRequest;

use App\Http\Requests\LeaveRequest\Pagination as PaginationRequest;
use App\Http\Resources\LeaveRequest\Pagination as PaginationResource;
use App\Http\Resources\LeaveRequest\LeaveRequest as LeaveRequestResource;

class LeaveRequestController extends Controller
{
    public LeaveRequestServiceContract $service;
    /**
     * @var array
     */

    /**
     * LeaveRequestsController constructor.
     * 
     * @param LeaveRequestServiceContract $service
     */
    public function __construct(LeaveRequestServiceContract $service)
    {
        $this->service = $service;
    }

    
    
    /**
     * Create a new LeaveRequest
     * 
     * @param CreateLeaveRequest $request
     * 
     * @return JsonResource
     */
    public function createLeaveRequest(CreateLeaveRequest $request): JsonResource
    {
        try{
            return new LeaveRequestResource($this->service->createLeaveRequest($request->validated()));
        }catch(Exception $e){
            return new ErrorResource($e);
        }
    }

     
    /**
     * Update a LeaveRequest
     * 
     * @param UpdateLeaveRequest $request
     * 
     * @return JsonResource
     */
    public function updateLeaveRequest(UpdateLeaveRequest $request): JsonResource
    {
        try{
            return new LeaveRequestResource($this->service->updateLeaveRequest($request->validated()));
        }catch(Exception $e){
            return new ErrorResource($e);
        }
    }

    
   
    /**
     * List LeaveRequests
     * 
     * @param PaginationRequest $request
     * 
     * @return JsonResource
     */
     public function listLeaveRequests(PaginationRequest $request): JsonResource
    {
        try{
            return new PaginationResource($this->service->listLeaveRequests($request->validated()));
        }catch(Exception $e){
            return new ErrorResource($e);
        }
    }

    
    /**
     * List LeaveRequests by User
     * 
     * @param PaginationRequest $request
     * 
     * @return JsonResource
     */
    public function listUserLeaveRequests(PaginationRequest $request): JsonResource
    {
        try{
            return new PaginationResource($this->service->listUserLeaveRequests($request->validated()));
        }catch(Exception $e){
            return new ErrorResource($e);
        }
    }
}