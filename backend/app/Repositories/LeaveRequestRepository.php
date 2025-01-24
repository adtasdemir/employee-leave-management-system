<?php

namespace App\Repositories;

use App\Helpers\Utils;

use App\Models\LeaveRequest;
use App\Enums\LeaveRequestStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Contracts\Repository\LeaveRequestRepositoryContract;

/**
 * Class LeaveRequestRepository
 *
 * This repository handles the operations related to LeaveRequest models.
 * It extends the BaseRepository to leverage common repository methods and implements the LeaveRequestRepositoryContract.
 * It includes methods to create, update, and list leave requests.
 *
 * @package App\Repositories
 */
class LeaveRequestRepository extends BaseRepository implements LeaveRequestRepositoryContract
{
    /**
     * LeaveRequestRepository constructor.
     * 
     * @param LeaveRequest $model
     */
    public function __construct(LeaveRequest $model)
    {
        parent::__construct($model);
    }

    /**
     * Create a new LeaveRequest
     *
     * @param string $name
     *
     * @return \App\Models\LeaveRequest
     */
    public function createLeaveRequest(array $data): LeaveRequest
    {
        return $this->create($data);
    }

   
    /**
     * List LeaveRequests
     * 
     * @param array $data
     * 
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function listLeaveRequests(array $data):LengthAwarePaginator
    {
        $query = $this->query();
        if (isset($data['user_id'])) {
            $query->where('user_id', $data['user_id']);
        }
        $query->orderBy('id', 'desc')->orderBy('user_id', 'asc');

        return $query->paginate();
    }
 
    
    /**
     * Update a LeaveRequest.
     *
     * This method updates an existing leave request with the provided data. If the leave request is approved,
     * the user's remaining annual leave days will be updated, and conflicting or exceeding leave requests will be rejected.
     * The method is wrapped in a database transaction to ensure atomicity.
     *
     * @param int $id The ID of the leave request to update.
     * @param array $data The data to update the leave request with.
     * @return \App\Models\LeaveRequest The updated LeaveRequest model instance.
     * @throws \Exception If there is not enough annual leave days or if any errors occur during the transaction.
     */
    public function updateLeaveRequest(int $id, array $data): LeaveRequest
    {
        DB::beginTransaction();
        try {
            // Find the leave request by ID
            $LeaveRequest = $this->query()->find($id);
            $LeaveRequest->update($data);

            // Check if the leave request is approved
            if ($LeaveRequest->status == LeaveRequestStatus::APPROVED->value) {
                $user = $LeaveRequest->user;

                // Calculate the number of leave days for the request
                $leaveDays = Utils::diffInDays($LeaveRequest->start_date, $LeaveRequest->end_date);

                // Check if the user has enough remaining annual leave days
                if ($user->remaining_annual_leave_days < $leaveDays) {
                    throw new \Exception('Not enough annual leave days', 400);
                }

                // Deduct the leave days from the user's remaining annual leave
                $user->remaining_annual_leave_days -= $leaveDays;
                $user->save();

                $startDate = $LeaveRequest->start_date;
                $endDate = $LeaveRequest->end_date;

                // Reject leave requests that exceed remaining annual leave days
                $user->leaveRequests()
                    ->where('status', LeaveRequestStatus::PENDING->value)
                    ->whereRaw('DATEDIFF(end_date, start_date) > ?', [$user->remaining_annual_leave_days])
                    ->update([
                        'status' => LeaveRequestStatus::REJECTED->value,
                        'rejection_reason' => 'Exceeded remaining annual leave days',
                        'updated_at' => now(),
                    ]);

                // Reject leave requests that conflict with the specified start and end dates
                $user->leaveRequests()
                    ->where('status', LeaveRequestStatus::PENDING->value)
                    ->whereRaw('? < end_date AND ? > start_date', [$startDate, $endDate])
                    ->update([
                        'status' => LeaveRequestStatus::REJECTED->value,
                        'rejection_reason' => 'Conflict with an approved leave request',
                        'updated_at' => now(),
                    ]);
            }

            // Commit the transaction
            DB::commit();

            return $LeaveRequest;
        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollBack();
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }
  
}


