<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Helpers\Utils;
use Illuminate\Database\Seeder;
use App\Enums\LeaveRequestStatus;
use App\Services\LeaveRequestService;

/**
 * Class LeaveRequestSeeder
 *
 * Seeder class for generating and populating leave requests for users.
 * This seeder will create random leave requests for users with remaining annual leave days.
 * The requests will be created with random start and end dates, ensuring that they do not conflict with existing requests.
 * It will also randomly approve or reject the created leave requests.
 *
 * @package Database\Seeders
 */
class LeaveRequestSeeder extends Seeder
{
    /**
     * @var \App\Services\LeaveRequestService
     */
    protected $leaveRequestService;

    /**
     * LeaveRequestSeeder constructor.
     *
     * Injects the LeaveRequestService for managing leave request logic.
     *
     * @param \App\Services\LeaveRequestService $leaveRequestService The service used for leave request operations.
     */
    public function __construct(LeaveRequestService $leaveRequestService)
    {
        $this->leaveRequestService = $leaveRequestService;
    }

    /**
     * Run the database seeder.
     *
     * This method retrieves all users with remaining annual leave days, creates random leave requests for them,
     * and updates the requests with a random approval or rejection status. It ensures no leave request conflicts with
     * other approved or pending leave requests and the leave request duration does not exceed the user's remaining annual leave days.
     *
     * @return void
     */
    public function run(): void
    {
        // Get all users with remaining annual leave days
        $users = User::with('leaveRequests')
            ->where('remaining_annual_leave_days', '>', 0)
            ->get();

        foreach ($users as $user) {

            // Create a random number of leave requests (between 1 and 5)
            $iterations = range(1, mt_rand(1, 5));
            foreach ($iterations as $i) {
                // Generate random start and end dates for the leave request
                $randomNumber = mt_rand(0, 365);
                $startDate = Utils::createDate($randomNumber);
                $endDate = Utils::createDate($randomNumber + mt_rand(1, 20));
                $numberOfDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));

                // Check if the user has enough annual leave days and no conflicting leave requests
                if (
                    $numberOfDays < $user->remaining_annual_leave_days &&
                    !$this->leaveRequestService->hasConflictingLeaveRequest($user, $startDate, $endDate)
                ) {
                    // Create the leave request with the pending status
                    $user->leaveRequests()->create([
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'status' => LeaveRequestStatus::PENDING->value,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Reload user with leave requests to process their statuses
            $user->load('leaveRequests');

            // Update the leave requests randomly to either approved or rejected
            foreach ($user->leaveRequests as $leaveRequest) {
                $status = rand(0, 1) === 0 ? LeaveRequestStatus::REJECTED->value : LeaveRequestStatus::APPROVED->value;
                try {
                    if ($leaveRequest->status == LeaveRequestStatus::PENDING->value) {
                        // Prepare data for updating the leave request
                        $data = [
                            'id' => $leaveRequest->id,
                            'status' => $status,
                        ];
                        if ($status == LeaveRequestStatus::REJECTED->value) {
                            $data['rejection_reason'] = 'Admin rejection';
                        }
                        // Update the leave request using the service
                        $this->leaveRequestService->updateLeaveRequest($data);
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        }
    }
}
