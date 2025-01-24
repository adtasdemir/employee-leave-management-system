<?php

use App\Enums\UserStatus;
use App\Enums\LocaleLanguage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('surname');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade'); 
            $table->unsignedBigInteger('role_id')->default(2); 
            $table->integer('annual_leave_days')->default(20);
            $table->integer('remaining_annual_leave_days')->default(20);
            $table->enum('status', array_column(UserStatus::cases(), 'value'))->default(UserStatus::ACTIVE->value);
            $table->rememberToken();
            $table->timestamps();
        
            $table->index(['email', 'username'], 'email_username_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
