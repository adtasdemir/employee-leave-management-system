<?php

use App\Enums\LeaveRequestStatus;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', array_column(LeaveRequestStatus::cases(), 'value'))->default(LeaveRequestStatus::PENDING->value);
            $table->string('rejection_reason')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'start_date', 'end_date'], 'unique_user_leave');

        });
    }

    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
