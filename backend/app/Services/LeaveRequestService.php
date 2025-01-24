<?php

namespace App\Services;

use App\Models\User;
use App\Helpers\Utils;
use App\Models\LeaveRequest;
use App\Services\BaseService;
use App\Enums\LeaveRequestStatus;
use App\Repositories\LeaveRequestRepository;
use App\Contracts\Service\LeaveRequestServiceContract;

/**
 * Class LeaveRequestService
 *
 * This service handles all the business logic related to leave requests.
 * It provides methods to create, update, list, and check leave requests,
 * and integrates with the LeaveRequestRepository to interact with the database.
 *
 * @package App\Services
 */
class LeaveRequestService extends BaseService implements LeaveRequestServiceContract
{
    /**
     * LeaveRequestService constructor.
     *
     * @param \App\Repositories\LeaveRequestRepository $repository The repository to be used for leave request operations.
     */
    public function __construct(LeaveRequestRepository $repository)
    {
        parent::__construct($repository);
    }

    
    /**
     * Create a new LeaveRequest
     *
     * This method creates a new leave request for the currently authenticated user.
     * It sets the user ID and status to "PENDING", and then calls the create method on the repository.
     *
     * @param array $request The data to create the leave request with.
     * @return \App\Models\LeaveRequest The created leave request.
     */
    public function createLeaveRequest(array $request): LeaveRequest
    {    
        // Set the user ID and status to "PENDING"
        $request['user_id'] = auth()->user()->id;
        $request['status'] = LeaveRequestStatus::PENDING;
        return $this->repository->createLeaveRequest($request);
    }

    /**
     * List leave requests based on the given criteria.
     *
     * This method retrieves a paginated list of leave requests, with optional filters applied.
     *
     * @param array $request The filters for retrieving the leave requests (e.g., user ID).
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator The paginated leave requests.
     */
    public function listLeaveRequests(array $request)
    {    
        return $this->repository->listLeaveRequests($request);
    }

    /**
     * Update an existing leave request.
     *
     * This method updates an existing leave request with the provided data.
     *
     * @param array $request The updated data for the leave request, including the ID.
     * @return \App\Models\LeaveRequest The updated leave request model.
     */
    public function updateLeaveRequest(array $request): LeaveRequest
    {
        $id = $request['id'];
        unset($request['id']);
        return $this->repository->updateLeaveRequest($id, $request);
    }

    /**
     * List the leave requests for the currently authenticated user.
     *
     * This method retrieves a paginated list of leave requests for the authenticated user,
     * with optional filters applied.
     *
     * @param array $request The filters for retrieving the user's leave requests.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator The paginated leave requests for the user.
     */
    public function listUserLeaveRequests(array $request)
    {    
        // Automatically set the user ID to the authenticated user's ID
        $request['user_id'] = auth()->user()->id;
        return $this->repository->listLeaveRequests($request);
    }

    /**
     * Check if the user has any conflicting leave requests.
     *
     * This method checks if the user already has any approved or conflicting leave
     * requests during the specified date range.
     *
     * @param \App\Models\User $user The user for whom to check leave request conflicts.
     * @param string $startDate The start date of the requested leave.
     * @param string $endDate The end date of the requested leave.
     * @return bool Returns true if there is a conflicting leave request, false otherwise.
     */
    public function hasConflictingLeaveRequest(User $user, string $startDate, string $endDate): bool
    {
        return $user->leaveRequests()
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where(function ($query) use ($startDate, $endDate) {
                    // Check if there's an exact match for the start and end dates
                    $query->where('start_date', $startDate)
                          ->where('end_date', $endDate);
                })
                ->orWhere(function ($query) use ($startDate, $endDate) {
                    // Check if there's an approved leave request that overlaps the requested dates
                    $query->where('status', LeaveRequestStatus::APPROVED->value)
                        ->where(function ($query) use ($startDate, $endDate) {
                            $query->whereRaw('? < end_date AND ? > start_date', [$startDate, $endDate]);
                        });
                });
            })
            ->exists();
    }
}
