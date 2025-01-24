<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Enums\LeaveRequestStatus;
use App\Helpers\Utils;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;

class LeaveRequestControllerTest extends TestCase
{
    use RefreshDatabase;

    public static function leaveRequestDataProvider(){
        return [
            [   
                // Case 0: Valid leave request with a duration of 7 days. 
                // Expectation: The request is created successfully.
                [
                    'start_date' => Carbon::now()->format('Y-m-d'),
                    'end_date' => Carbon::now()->addDays(7)->format('Y-m-d'),
                ],
                [
                    'data' => [
                        'start_date' => Carbon::now()->format('Y-m-d'),
                        'end_date' => Carbon::now()->addDays(7)->format('Y-m-d'),
                        'status' => LeaveRequestStatus::PENDING->value,
                    ],
                    'success' => true,
                    'code' => 200,
                    'message' => 'Success',
                ],
                [
                    'user_id' => 1,
                    'assert_database_has' => true,
                ]
            ],
            [   
                // Case 1: Leave request with start and end dates matching a previous request.
                // Expectation: The request is rejected due to a date conflict.
                [
                    'start_date' => Carbon::now()->format('Y-m-d'),
                    'end_date' => Carbon::now()->addDays(7)->format('Y-m-d'),
                ],
                [
                    'data' => [
                        'start_date' => [
                            'The leave request conflicts with existing leave requests.',
                        ],
                    ],
                    'success' => false,
                    'code' => 500,
                    'message' => 'The leave request conflicts with existing leave requests.',
                ],
                [
                    'user_id' => 1,
                    'insert_same_leave_request_data' => true
                ]
            ],
            [   
                // Case 2: Leave request with overlapping dates within the range of a previously approved leave request.
                // Expectation: The request is rejected due to a date conflict.
                [
                    'start_date' => Carbon::now()->addDays(1)->format('Y-m-d'),
                    'end_date' => Carbon::now()->addDays(4)->format('Y-m-d'),
                ],
                [
                    'data' => [
                        'start_date' => [
                            'The leave request conflicts with existing leave requests.',
                        ],
                    ],
                    'success' => false,
                    'code' => 500,
                    'message' => 'The leave request conflicts with existing leave requests.',
                ],
                [
                    'user_id' => 1,
                    'pervious_data' => [
                        'status' => LeaveRequestStatus::APPROVED->value,
                    ]
                ]
            ],
            [   
                // Case 3: Leave request with overlapping dates that extend outside the range of a previously approved leave request.
                // Expectation: The request is rejected due to a date conflict.
                [
                    'start_date' => Carbon::now()->addDays(1)->format('Y-m-d'),
                    'end_date' => Carbon::now()->addDays(10)->format('Y-m-d'),
                ],
                [
                    'data' => [
                        'start_date' => [
                            'The leave request conflicts with existing leave requests.',
                        ],
                    ],
                    'success' => false,
                    'code' => 500,
                    'message' => 'The leave request conflicts with existing leave requests.',
                ],
                [
                    'user_id' => 1,
                    'pervious_data' => [
                        'status' => LeaveRequestStatus::APPROVED->value,
                    ]
                ]
            ],
            [   
                // Case 4: Leave request with dates fully enclosed by a previously approved leave request.
                // Expectation: The request is rejected due to a date conflict.
                [
                    'start_date' => Carbon::now()->addDays(3)->format('Y-m-d'),
                    'end_date' => Carbon::now()->addDays(4)->format('Y-m-d'),
                ],
                [
                    'data' => [
                        'start_date' => [
                            'The leave request conflicts with existing leave requests.',
                        ],
                    ],
                    'success' => false,
                    'code' => 500,
                    'message' => 'The leave request conflicts with existing leave requests.',
                ],
                [
                    'user_id' => 1,
                    'pervious_data' => [
                        'status' => LeaveRequestStatus::APPROVED->value,
                    ]
                ]
            ],
            [   
                // Case 5: Leave request with non-overlapping dates outside the range of a previously approved leave request.
                // Expectation: The request is created successfully.
                [
                    'start_date' => Carbon::now()->addDays(6)->format('Y-m-d'),
                    'end_date' => Carbon::now()->addDays(10)->format('Y-m-d'),
                ],
                [
                    'data' => [
                        'start_date' => Carbon::now()->addDays(6)->format('Y-m-d'),
                        'end_date' => Carbon::now()->addDays(10)->format('Y-m-d'),
                        'status' => LeaveRequestStatus::PENDING->value,
                    ],
                    'success' => true,
                    'code' => 200,
                    'message' => 'Success',
                ],
                [
                    'user_id' => 1,
                    'assert_database_has' => true,
                    'pervious_data' => [
                        'status' => LeaveRequestStatus::APPROVED->value,
                    ]
                ]
            ],
            [   
                // Case 6: Leave request with overlapping dates within the range of a previously pending leave request.
                // Expectation: The request is created successfully.
                [
                    'start_date' => Carbon::now()->addDays(4)->format('Y-m-d'),
                    'end_date' => Carbon::now()->addDays(10)->format('Y-m-d'),
                ],
                [
                    'data' => [
                        'start_date' => Carbon::now()->addDays(4)->format('Y-m-d'),
                        'end_date' => Carbon::now()->addDays(10)->format('Y-m-d'),
                        'status' => LeaveRequestStatus::PENDING->value,
                    ],
                    'success' => true,
                    'code' => 200,
                    'message' => 'Success',
                ],
                [
                    'user_id' => 1,
                    'assert_database_has' => true,
                    'pervious_data' => [
                        'status' => LeaveRequestStatus::PENDING->value,
                    ]
                ]
            ],
            [   
                // Case 7: Leave request with a start date that matches the end date of a previously approved leave request.
                // Expectation: The request is created successfully.
                [
                    'start_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
                    'end_date' => Carbon::now()->addDays(10)->format('Y-m-d'),
                ],
                [
                    'data' => [
                        'start_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
                        'end_date' => Carbon::now()->addDays(10)->format('Y-m-d'),
                        'status' => LeaveRequestStatus::PENDING->value,
                    ],
                    'success' => true,
                    'code' => 200,
                    'message' => 'Success',
                ],
                [
                    'user_id' => 1,
                    'assert_database_has' => true,
                    'pervious_data' => [
                        'status' => LeaveRequestStatus::APPROVED->value,
                    ]
                ]
            ],
            [   
                // Case 8: Leave request with an end date that matches the start date of a previously approved leave request.
                // Expectation: The request is created successfully.
                [
                    'start_date' => Carbon::now()->format('Y-m-d'),
                    'end_date' => Carbon::now()->addDays(2)->format('Y-m-d'),
                ],
                [
                    'data' => [
                        'start_date' => Carbon::now()->format('Y-m-d'),
                        'end_date' => Carbon::now()->addDays(2)->format('Y-m-d'),
                        'status' => LeaveRequestStatus::PENDING->value,
                    ],
                    'success' => true,
                    'code' => 200,
                    'message' => 'Success',
                ],
                [
                    'user_id' => 1,
                    'assert_database_has' => true,
                    'pervious_data' => [
                        'status' => LeaveRequestStatus::APPROVED->value,
                    ]
                ]
            ]
        ];
    }
    

    #[DataProvider('leaveRequestDataProvider')]
    public function test_create_leave_request(array $request, array $expected, array $additionalInfo)
    {

        $userRole = Role::factory()->create([
            'title' => 'user',
            'description' => 'Regular user role with limited access.'
        ]);
        $user = User::factory()->create([
            'id' => $additionalInfo['user_id'],
            'role_id' => $userRole->id
        ]);
        $token = $user->createToken('TestToken')->plainTextToken;


        if(isset($additionalInfo['insert_same_leave_request_data'])){
            $user->leaveRequests()->create([
                'user_id' => $user->id,
                'start_date' =>$request['start_date'],
                'end_date' => $request['end_date']
            ]);
        }

        if(isset($additionalInfo['pervious_data'])){
            $user->leaveRequests()->create([
                'user_id' => $user->id,
                'start_date' => Carbon::now()->addDays(2)->format('Y-m-d'),
                'end_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
                'status' => $additionalInfo['pervious_data']['status'] ?? LeaveRequestStatus::PENDING->value
            ]);
        }


        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->postJson(route('createLeaveRequest',$request))->json();

        //var_export($response);
        $this->assertEquals($response['code'],  $expected['code']);
        $this->assertEquals($response['message'],  $expected['message']);

        if(isset($additionalInfo['assertDatabaseHas'])){
            $expected['data']['status'] = LeaveRequestStatus::PENDING->value;
            $this->assertDatabaseHas('leave_requests', $expected['data']);
        }
    }


    public function test_create_leave_request_missing_fields()
    {
        $userRole = Role::factory()->create([
            'title' => 'user',
            'description' => 'Regular user role with limited access.'
        ]);
        $user = User::factory()->create([
            'role_id' => $userRole->id
        ]);
        $token = $user->createToken('TestToken')->plainTextToken;

        $startDate = Carbon::now()->format('Y-m-d');

        $requestData = [
            "start_date" => $startDate,
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->postJson(route('createLeaveRequest',$requestData))->json();

        $this->assertEquals($response['code'], 500);
        $this->assertEquals($response['message'], 'The end date field is required.');
        $this->assertFalse($response['success']);
        $this->assertEquals($response['data'],
        [
            "end_date" => [
                "The end date field is required." 
            ], 
         ], 
        );
    }

    public function test_list_user_leave_requests_by_user()
    {
        $userRole = Role::factory()->create([
            'title' => 'user',
            'description' => 'Regular user role with limited access.'
        ]);
        $user = User::factory()->create([
            'role_id' => $userRole->id
        ]);
        $token = $user->createToken('TestToken')->plainTextToken;

        $startDate = Carbon::now()->format('Y-m-d');
        $endDate = Carbon::now()->addDays(7)->format('Y-m-d'); 

        $user->leaveRequests()->create([  
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => LeaveRequestStatus::PENDING->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $startDate = Carbon::now()->addDays(9)->format('Y-m-d');
        $endDate = Carbon::now()->addDays(12)->format('Y-m-d'); 

        $user->leaveRequests()->create([  
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => LeaveRequestStatus::PENDING->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $startDate = Carbon::now()->addDays(30)->format('Y-m-d');
        $endDate = Carbon::now()->addDays(1)->format('Y-m-d'); 

        $user->leaveRequests()->create([  
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => LeaveRequestStatus::PENDING->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
       

        $anotherUser = User::factory()->create([
            'role_id' => $userRole->id
        ]);

        $startDate = Carbon::now()->format('Y-m-d');
        $endDate = Carbon::now()->addDays(7)->format('Y-m-d'); 

        $anotherUser->leaveRequests()->create([  
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => LeaveRequestStatus::PENDING->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->getJson(route('listUserLeaveRequests'))->json();

        $this->assertEquals($response['code'], 200);
        $this->assertEquals($response['message'], 'Success');
        $this->assertTrue($response['success']);
        $this->assertNotEmpty($response['data']);
        $this->assertCount(3,$response['data']);
        $this->assertNotEmpty($response['pagination']);
    }
    
    public function test_list_all_leave_requests_by_admin()
    {
        $userRole = Role::factory()->create([
            'title' => 'user',
            'description' => 'Regular user role with limited access.'
        ]);
        $user = User::factory()->create([
            'role_id' => $userRole->id
        ]);

        $startDate = Carbon::now()->format('Y-m-d');
        $endDate = Carbon::now()->addDays(7)->format('Y-m-d'); 

        $user->leaveRequests()->create([  
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => LeaveRequestStatus::PENDING->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $startDate = Carbon::now()->addDays(9)->format('Y-m-d');
        $endDate = Carbon::now()->addDays(12)->format('Y-m-d'); 

        $user->leaveRequests()->create([  
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => LeaveRequestStatus::PENDING->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $startDate = Carbon::now()->addDays(30)->format('Y-m-d');
        $endDate = Carbon::now()->addDays(1)->format('Y-m-d'); 

        $user->leaveRequests()->create([  
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => LeaveRequestStatus::PENDING->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
       

        $anotherUser = User::factory()->create([
            'role_id' => $userRole->id
        ]);

        $startDate = Carbon::now()->format('Y-m-d');
        $endDate = Carbon::now()->addDays(7)->format('Y-m-d'); 

        $anotherUser->leaveRequests()->create([  
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => LeaveRequestStatus::PENDING->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        $adminRole = Role::factory()->create([
            'title' => 'admin',
            'description' => 'Administrator role with full access.'
        ]);
        $admin = User::factory()->create([
            'role_id' => $adminRole->id
        ]);
        $token = $admin->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->getJson(route('listLeaveRequests'))->json();

        $this->assertEquals($response['code'], 200);
        $this->assertEquals($response['message'], 'Success');
        $this->assertTrue($response['success']);
        $this->assertNotEmpty($response['data']);
        $this->assertCount(4,$response['data']);
        $this->assertNotEmpty($response['pagination']);
    }


    public function test_list_user_leave_requests_by_admin()
    {
        $userRole = Role::factory()->create([
            'title' => 'user',
            'description' => 'Regular user role with limited access.'
        ]);
        $user = User::factory()->create([
            'role_id' => $userRole->id
        ]);

        $startDate = Carbon::now()->format('Y-m-d');
        $endDate = Carbon::now()->addDays(7)->format('Y-m-d'); 

        $user->leaveRequests()->create([  
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => LeaveRequestStatus::PENDING->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $startDate = Carbon::now()->addDays(9)->format('Y-m-d');
        $endDate = Carbon::now()->addDays(12)->format('Y-m-d'); 

        $user->leaveRequests()->create([  
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => LeaveRequestStatus::PENDING->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $startDate = Carbon::now()->addDays(30)->format('Y-m-d');
        $endDate = Carbon::now()->addDays(1)->format('Y-m-d'); 

        $user->leaveRequests()->create([  
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => LeaveRequestStatus::PENDING->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
       

        $anotherUser = User::factory()->create([
            'role_id' => $userRole->id
        ]);

        $startDate = Carbon::now()->format('Y-m-d');
        $endDate = Carbon::now()->addDays(7)->format('Y-m-d'); 

        $anotherUser->leaveRequests()->create([  
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => LeaveRequestStatus::PENDING->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $adminRole = Role::factory()->create([
            'title' => 'admin',
            'description' => 'Administrator role with full access.'
        ]);
        $admin = User::factory()->create([
            'role_id' => $adminRole->id
        ]);
        $token = $admin->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->getJson(route('listLeaveRequests',['user_id' => $user->id]))->json();

        $this->assertEquals($response['code'], 200);
        $this->assertEquals($response['message'], 'Success');
        $this->assertTrue($response['success']);
        $this->assertNotEmpty($response['data']);
        $this->assertCount(3,$response['data']);
        $this->assertNotEmpty($response['pagination']);
    }

    public function test_approve_leave_request()
    {
        $adminRole = Role::factory()->create([
            'title' => 'admin',
            'description' => 'Administrator role with full access.'
        ]);
        $admin = User::factory()->create([
            'role_id' => $adminRole->id
        ]);
        $token = $admin->createToken('TestToken')->plainTextToken;


        $userRole = Role::factory()->create([
            'title' => 'user',
            'description' => 'Regular user role with limited access.'
        ]);
        $user = User::factory()->create([
            'role_id' => $userRole->id
        ]);

        $startDate = Carbon::now()->addDays(1)->format('Y-m-d');
        $endDate = Carbon::now()->addDays(4)->format('Y-m-d'); 
        $user->leaveRequests()->create([  
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => LeaveRequestStatus::PENDING->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $startDate = Carbon::now()->addDays(8)->format('Y-m-d');
        $endDate = Carbon::now()->addDays(1)->format('Y-m-d'); 
        $leaveRequest = $user->leaveRequests()->create([  
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => LeaveRequestStatus::PENDING->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->putJson(route('approveLeaveRequest',['id' => $leaveRequest->id]))->json();

        $this->assertEquals($response['code'], 200);
        $this->assertEquals($response['message'], 'Success');
        $this->assertTrue($response['success']);
        $this->assertEquals($response['data']['start_date'],$startDate);
        $this->assertEquals($response['data']['end_date'],$endDate);
        $this->assertEquals($response['data']['user_id'],$user->id);
        $this->assertEquals($response['data']['status'],LeaveRequestStatus::APPROVED->value);

        $this->assertDatabaseHas('leave_requests', [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => LeaveRequestStatus::APPROVED->value,
            'id' => $leaveRequest->id
        ]);

        $this->assertDatabaseHas('users', [
            'remaining_annual_leave_days' => $user->annual_leave_days - Utils::diffInDays($startDate, $endDate),
            'id' => $user->id
        ]);
    }


    public function test_approve_one_leave_request_and_reject_another_leave_request_with_not_enough_annual_leave_days()
    {
        $adminRole = Role::factory()->create([
            'title' => 'admin',
            'description' => 'Administrator role with full access.'
        ]);
        $admin = User::factory()->create([
            'role_id' => $adminRole->id
        ]);
        $token = $admin->createToken('TestToken')->plainTextToken;


        $userRole = Role::factory()->create([
            'title' => 'user',
            'description' => 'Regular user role with limited access.'
        ]);
        $user = User::factory()->create([
            'role_id' => $userRole->id
        ]);

        $startDate = Carbon::now()->addDays(1)->format('Y-m-d');
        $endDate = Carbon::now()->addDays(18)->format('Y-m-d'); 
        $leaveRequest = $user->leaveRequests()->create([  
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => LeaveRequestStatus::PENDING->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->putJson(route('approveLeaveRequest',['id' => $leaveRequest->id]))->json();

        $this->assertDatabaseHas('users', [
            'remaining_annual_leave_days' => $user->annual_leave_days - Utils::diffInDays($startDate, $endDate),
            'id' => $user->id
        ]);

        $startDate = Carbon::now()->addDays(18)->format('Y-m-d');
        $endDate = Carbon::now()->addDays(29)->format('Y-m-d'); 
        $leaveRequest = $user->leaveRequests()->create([  
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => LeaveRequestStatus::PENDING->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->putJson(route('approveLeaveRequest',['id' => $leaveRequest->id]))->json();

        $this->assertEquals($response['code'], 400);    
        $this->assertEquals($response['message'], 'Not enough annual leave days');
        $this->assertFalse($response['success']);
        $this->assertEmpty($response['data']);
    }

    public function test_approve_leave_request_and_its_effect_to_another_pendding_records()
    {
        $adminRole = Role::factory()->create([
            'title' => 'admin',
            'description' => 'Administrator role with full access.'
        ]);
        $admin = User::factory()->create([
            'role_id' => $adminRole->id
        ]);
        $token = $admin->createToken('TestToken')->plainTextToken;


        $userRole = Role::factory()->create([
            'title' => 'user',
            'description' => 'Regular user role with limited access.'
        ]);
        $user = User::factory()->create([
            'role_id' => $userRole->id
        ]);

        $startDate1 = Carbon::now()->addDays(1)->format('Y-m-d');
        $endDate1 = Carbon::now()->addDays(18)->format('Y-m-d'); 
        $leaveRequest1 = $user->leaveRequests()->create([  
            'start_date' => $startDate1,
            'end_date' => $endDate1,
            'status' => LeaveRequestStatus::PENDING->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $startDate2 = Carbon::now()->addDays(18)->format('Y-m-d');
        $endDate2 = Carbon::now()->addDays(29)->format('Y-m-d'); 
        $leaveRequest2 = $user->leaveRequests()->create([  
            'start_date' => $startDate2,
            'end_date' => $endDate2,
            'status' => LeaveRequestStatus::PENDING->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->putJson(route('approveLeaveRequest',['id' => $leaveRequest2->id]))->json();

        $this->assertEquals($response['code'], 200);
        $this->assertEquals($response['message'], 'Success');
        $this->assertTrue($response['success']);
        $this->assertEquals($response['data']['start_date'],$startDate2);
        $this->assertEquals($response['data']['end_date'],$endDate2);
        $this->assertEquals($response['data']['user_id'],$user->id);
        $this->assertEquals($response['data']['status'],LeaveRequestStatus::APPROVED->value);

        $this->assertDatabaseHas('leave_requests', [
            'start_date' => $startDate2,
            'end_date' => $endDate2,
            'status' => LeaveRequestStatus::APPROVED->value,
            'id' => $leaveRequest2->id
        ]);

        $this->assertDatabaseHas('leave_requests', [
            'start_date' => $startDate1,
            'end_date' => $endDate1,
            'status' => LeaveRequestStatus::REJECTED->value,
            'id' => $leaveRequest1->id
        ]);

        $this->assertDatabaseHas('users', [
            'remaining_annual_leave_days' => $user->annual_leave_days - Utils::diffInDays($startDate2, $endDate2),
            'id' => $user->id
        ]);
    }
    

    public function test_reject_leave_request()
    {
        $adminRole = Role::factory()->create([
            'title' => 'admin',
            'description' => 'Administrator role with full access.'
        ]);
        $admin = User::factory()->create([
            'role_id' => $adminRole->id
        ]);
        $token = $admin->createToken('TestToken')->plainTextToken;


        $userRole = Role::factory()->create([
            'title' => 'user',
            'description' => 'Regular user role with limited access.'
        ]);
        $user = User::factory()->create([
            'role_id' => $userRole->id
        ]);

        $startDate = Carbon::now()->addDays(1)->format('Y-m-d');
        $endDate = Carbon::now()->addDays(4)->format('Y-m-d'); 
        $user->leaveRequests()->create([  
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => LeaveRequestStatus::PENDING->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $startDate = Carbon::now()->addDays(8)->format('Y-m-d');
        $endDate = Carbon::now()->addDays(1)->format('Y-m-d'); 
        $leaveRequest = $user->leaveRequests()->create([  
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => LeaveRequestStatus::PENDING->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->putJson(route('rejectLeaveRequest',['id' => $leaveRequest->id]))->json();

        $this->assertEquals($response['code'], 200);
        $this->assertEquals($response['message'], 'Success');
        $this->assertTrue($response['success']);
        $this->assertEquals($response['data']['start_date'],$startDate);
        $this->assertEquals($response['data']['end_date'],$endDate);
        $this->assertEquals($response['data']['user_id'],$user->id);
        $this->assertEquals($response['data']['status'],LeaveRequestStatus::REJECTED->value);

        $this->assertDatabaseHas('leave_requests', [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => LeaveRequestStatus::REJECTED->value,
            'id' => $leaveRequest->id
        ]);
    }


    public function test_approve_leave_request_by_user()
    {
        $userRole = Role::factory()->create([
            'title' => 'user',
            'description' => 'Regular user role with limited access.'
        ]);
        $user = User::factory()->create([
            'role_id' => $userRole->id
        ]);
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->putJson(route('approveLeaveRequest',['id' => 1]))->json();

        $this->assertEquals($response['code'], 403);    
        $this->assertEquals($response['message'], 'This action is unauthorized.');
        $this->assertFalse($response['success']);
        $this->assertEmpty($response['data']);
    }

    public function test_reject_leave_request_by_user()
    {
        $userRole = Role::factory()->create([
            'title' => 'user',
            'description' => 'Regular user role with limited access.'
        ]);
        $user = User::factory()->create([
            'role_id' => $userRole->id
        ]);
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->putJson(route('rejectLeaveRequest',['id' => 1]))->json();

        $this->assertEquals($response['code'], 403);    
        $this->assertEquals($response['message'], 'This action is unauthorized.');
        $this->assertFalse($response['success']);
        $this->assertEmpty($response['data']);
    }

}
