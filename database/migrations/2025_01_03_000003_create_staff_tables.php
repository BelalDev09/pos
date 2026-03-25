<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── Staff Profiles ────────────────────────────────────────────────
        // Extends users with employment details
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('employee_id', 40)->nullable();
            $table->string('department', 80)->nullable();
            $table->string('designation', 80)->nullable();

            $table->enum('employment_type', [
                'full_time', 'part_time', 'contract', 'intern'
            ])->default('full_time');

            $table->date('hire_date')->nullable();
            $table->date('termination_date')->nullable();

            // Compensation
            $table->decimal('salary', 15, 4)->nullable();
            $table->enum('salary_type', ['monthly', 'hourly', 'daily'])->default('monthly');
            $table->string('bank_account', 100)->nullable();

            // Emergency
            $table->string('emergency_contact_name', 100)->nullable();
            $table->string('emergency_contact_phone', 30)->nullable();

            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['business_id', 'user_id']);
            $table->unique(['business_id', 'employee_id']);
            $table->index(['business_id', 'department']);
        });

        // ─── Shifts ────────────────────────────────────────────────────────
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();

            $table->string('name', 80);           // "Morning", "Evening", "Night"
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('crosses_midnight')->default(false);
            $table->json('working_days')->nullable(); // [1,2,3,4,5] = Mon-Fri
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('business_id');
        });

        // ─── Staff Shift Assignments ───────────────────────────────────────
        Schema::create('staff_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shift_id')->constrained()->restrictOnDelete();

            $table->date('date');
            $table->enum('status', ['scheduled', 'completed', 'absent', 'swapped'])
                  ->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['staff_id', 'date', 'shift_id']);
            $table->index(['business_id', 'date']);
        });

        // ─── Attendance ────────────────────────────────────────────────────
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained()->cascadeOnDelete();

            $table->date('date');
            $table->timestamp('clock_in')->nullable();
            $table->timestamp('clock_out')->nullable();
            $table->unsignedSmallInteger('break_minutes')->default(0);
            $table->decimal('total_hours', 5, 2)->nullable();
            $table->decimal('overtime_hours', 5, 2)->default(0);

            $table->enum('status', [
                'present', 'absent', 'late', 'half_day', 'holiday', 'leave'
            ])->default('present');

            $table->string('clock_in_ip', 45)->nullable();
            $table->string('clock_out_ip', 45)->nullable();
            $table->decimal('clock_in_lat', 10, 7)->nullable();
            $table->decimal('clock_in_lng', 10, 7)->nullable();

            $table->text('notes')->nullable();
            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamps();

            $table->unique(['staff_id', 'date']);
            $table->index(['business_id', 'date']);
            $table->index(['business_id', 'status']);
        });

        // ─── Leave Requests ────────────────────────────────────────────────
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained()->cascadeOnDelete();

            $table->string('leave_type', 60);    // annual, sick, unpaid
            $table->date('from_date');
            $table->date('to_date');
            $table->unsignedSmallInteger('days');
            $table->text('reason');

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamp('actioned_at')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'staff_id']);
            $table->index(['business_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('staff_shifts');
        Schema::dropIfExists('shifts');
        Schema::dropIfExists('staff');
    }
};
