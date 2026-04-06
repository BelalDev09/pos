<?php

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('restaurant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('avatar', 250)->default('https://static.vecteezy.com/system/resources/previews/048/926/084/non_2x/silver-membership-icon-default-avatar-profile-icon-membership-icon-social-media-user-image-illustration-vector.jpg');
            $table->string('name', 250);
            $table->string('phone')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            $table->integer('otp')->nullable()->unsigned()->check('otp BETWEEN 1000 AND 9999'); // 4-digit OTP
            $table->timestamp('otp_created_at')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
            $table->string('reset_token')->nullable();
            $table->enum('role', ['waiter', 'admin', 'user', 'cashier', 'manager', 'staff'])->default('user');
            $table->enum('status', ['pending', 'active', 'suspended', 'inactive', 'completed'])->default('pending');
            $table->string('gender')->nullable();
            $table->string('address')->nullable();
            $table->string('birth_country')->nullable();
            $table->string('country')->nullable();
            $table->longText('description')->nullable();
            // License verification fields
            $table->string('license_number')->nullable();
            $table->string('license_type')->nullable();
            $table->enum('license_status', ['active', 'inactive', 'transfer'])->nullable();
            $table->string('current_brokerage')->nullable();

            // Compliance fields
            $table->boolean('ica_signed')->default(false);
            $table->boolean('policies_accepted')->default(false);
            $table->boolean('w9_completed')->default(false);
            $table->boolean('photo_id_uploaded')->default(false);

            $table->string('tax')->nullable();
            $table->string('ssn_ein')->nullable();
            $table->string('city')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('document')->nullable();
            $table->boolean('privacy_policy')->default(0);

            $table->boolean('is_admin')->default(false);

            // Payment fields
            $table->string('stripe_id')->nullable()->index();
            $table->string('stripe_account_id')->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->string('stripe_subscription_id')->nullable();
            $table->enum('payment_method', ['credit_card', 'ach'])->nullable();


            // Onboarding tracking
            $table->timestamp('onboarding_started_at')->nullable();
            $table->timestamp('onboarding_completed_at')->nullable();
            $table->integer('current_onboarding_step')->default(0);
            $table->boolean('onboard_complete')->default(false);
            $table->string('payment_method_id')->nullable();
            $table->boolean('auto_purchase')->default(false);
            $table->boolean('is_card')->default(false);
            $table->boolean('is_bank')->default(false);

            // Indexes for fast lookup
            $table->index(['license_number', 'license_status']);
            // $table->index(['mls_membership_option']);
            $table->index(['onboarding_completed_at']);
            $table->rememberToken();
            $table->timestamp('last_login')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
