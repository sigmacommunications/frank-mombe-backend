<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Note: This migration uses raw SQL to modify columns to avoid Doctrine DBAL dependency.
     */
    public function up(): void
    {
        // Add columns to users table if they don't exist
        if (!Schema::hasColumn('users', 'email_code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('email_code', 250)->nullable()->after('role');
            });
        }
        if (!Schema::hasColumn('users', 'featured')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('featured')->default(0)->after('status');
            });
        }
        if (!Schema::hasColumn('users', 'stripe_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('stripe_id', 250)->nullable()->after('deleted_at');
            });
        }
        if (!Schema::hasColumn('users', 'pm_type')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('pm_type', 250)->nullable()->after('stripe_id');
            });
        }
        if (!Schema::hasColumn('users', 'exp_month')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('exp_month', 250)->nullable()->after('pm_type');
            });
        }
        if (!Schema::hasColumn('users', 'exp_year')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('exp_year', 250)->nullable()->after('exp_month');
            });
        }
        if (!Schema::hasColumn('users', 'pm_last_four')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('pm_last_four', 250)->nullable()->after('exp_year');
            });
        }
        if (!Schema::hasColumn('users', 'trial_ends_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('trial_ends_at', 250)->nullable()->after('pm_last_four');
            });
        }

        // Modify existing columns using raw SQL
        DB::statement('ALTER TABLE users MODIFY COLUMN first_name VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE users MODIFY COLUMN last_name VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE users MODIFY COLUMN phone VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE users MODIFY COLUMN travel_mode TINYINT DEFAULT 0 NULL');
        DB::statement('ALTER TABLE users MODIFY COLUMN holiday_mode TINYINT NULL');
        DB::statement('ALTER TABLE users MODIFY COLUMN rush_service TINYINT NULL');

        if (!Schema::hasColumn('barber_services', 'name')) {
            Schema::table('barber_services', function (Blueprint $table) {
                $table->string('name', 250)->nullable()->after('service_id');
            });
        }

        DB::statement('ALTER TABLE barber_services MODIFY COLUMN user_id INT NULL');
        DB::statement('ALTER TABLE barber_services MODIFY COLUMN service_id INT NULL');
        DB::statement('ALTER TABLE barber_services MODIFY COLUMN price INT NULL');
        DB::statement('ALTER TABLE barber_services MODIFY COLUMN main_service INT NULL');

        if (!Schema::hasColumn('bookings', 'service_time_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->unsignedBigInteger('service_time_id')->nullable()->after('barber_id');
            });
        }
        if (!Schema::hasColumn('bookings', 'event_type')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('event_type')->nullable()->after('additional_customers');
            });
        }
        if (!Schema::hasColumn('bookings', 'event_date')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->date('event_date')->nullable()->after('event_type');
            });
        }
        if (!Schema::hasColumn('bookings', 'number_of_people')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->integer('number_of_people')->nullable()->after('event_date');
            });
        }
        if (!Schema::hasColumn('bookings', 'staff_preference')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('staff_preference')->nullable()->after('number_of_people');
            });
        }
        if (!Schema::hasColumn('bookings', 'service_location')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('service_location')->nullable()->after('staff_preference');
            });
        }
        if (!Schema::hasColumn('bookings', 'number_of_staff_member')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->integer('number_of_staff_member')->nullable()->after('service_location');
            });
        }
        if (!Schema::hasColumn('bookings', 'parking_instructions')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->text('parking_instructions')->nullable()->after('number_of_staff_member');
            });
        }
        if (!Schema::hasColumn('bookings', 'add_ons')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->text('add_ons')->nullable()->after('parking_instructions');
            });
        }
        if (!Schema::hasColumn('bookings', 'preferred_stylist_name')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('preferred_stylist_name')->nullable()->after('add_ons');
            });
        }

        DB::statement('ALTER TABLE bookings MODIFY COLUMN member_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE bookings MODIFY COLUMN barber_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE bookings MODIFY COLUMN booking_time VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE bookings MODIFY COLUMN booking_date VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE bookings MODIFY COLUMN price INT NULL');
        DB::statement('ALTER TABLE bookings MODIFY COLUMN dis_price INT DEFAULT 0');
        DB::statement('ALTER TABLE bookings MODIFY COLUMN total_price INT DEFAULT 0');
        DB::statement('ALTER TABLE bookings MODIFY COLUMN image TEXT NOT NULL');
        DB::statement('ALTER TABLE bookings MODIFY COLUMN custom_location TEXT NULL');
        DB::statement('ALTER TABLE bookings MODIFY COLUMN status VARCHAR(100) NOT NULL');

        if (!Schema::hasColumn('booking_details', 'customer_type')) {
            Schema::table('booking_details', function (Blueprint $table) {
                $table->string('customer_type', 200)->nullable()->after('service_id');
            });
        }
        if (!Schema::hasColumn('booking_details', 'customer_name')) {
            Schema::table('booking_details', function (Blueprint $table) {
                $table->string('customer_name', 200)->nullable()->after('customer_type');
            });
        }

        DB::statement('ALTER TABLE booking_details MODIFY COLUMN booking_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE booking_details MODIFY COLUMN service_id BIGINT UNSIGNED NOT NULL');

        if (Schema::hasColumn('booking_members', 'service')) {
            DB::statement('ALTER TABLE booking_members CHANGE service service_id BIGINT UNSIGNED NULL');
        } else {
            DB::statement('ALTER TABLE booking_members MODIFY COLUMN service_id BIGINT UNSIGNED NULL');
        }

        if (!Schema::hasTable('booking_review')) {
            Schema::create('booking_review', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('member_id')->nullable();
                $table->integer('booking_id')->nullable();
                $table->integer('rating')->nullable();
                $table->longText('description')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
            });
        }

        if (!Schema::hasColumn('reviews', 'barber_id')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->unsignedBigInteger('barber_id')->nullable()->after('member_id');
            });
        }
        if (!Schema::hasColumn('reviews', 'booking_id')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->unsignedBigInteger('booking_id')->nullable()->after('barber_id');
            });
        }

        DB::statement('ALTER TABLE reviews MODIFY COLUMN member_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE reviews MODIFY COLUMN rating INT NULL');

        DB::statement('ALTER TABLE service_timings MODIFY COLUMN barber_id INT NOT NULL');

        if (!Schema::hasColumn('support', 'job_id')) {
            Schema::table('support', function (Blueprint $table) {
                $table->integer('job_id')->nullable()->after('user_id');
            });
        }

        DB::statement('ALTER TABLE que_answers MODIFY COLUMN user_id INT NULL');
        DB::statement('ALTER TABLE que_answers MODIFY COLUMN question_id INT NULL');

        DB::statement('ALTER TABLE wallet MODIFY COLUMN user_id INT NULL');
        DB::statement('ALTER TABLE wallet MODIFY COLUMN amount INT NULL');

        if (!Schema::hasColumn('transaction', 'pm_id')) {
            Schema::table('transaction', function (Blueprint $table) {
                $table->string('pm_id', 250)->nullable()->after('user_id');
            });
        }

        DB::statement('ALTER TABLE transaction MODIFY COLUMN user_id INT NULL');

        DB::statement('ALTER TABLE coupons MODIFY COLUMN min_price INT NULL');

        if (!Schema::hasColumn('wishlist', 'product_id')) {
            Schema::table('wishlist', function (Blueprint $table) {
                $table->integer('product_id')->nullable()->after('barber_id');
            });
        }
        if (!Schema::hasColumn('wishlist', 'type')) {
            Schema::table('wishlist', function (Blueprint $table) {
                $table->string('type', 250)->nullable()->after('product_id');
            });
        }

        DB::statement('ALTER TABLE posts MODIFY COLUMN barber_id BIGINT UNSIGNED NULL');

        if (!Schema::hasTable('user_temporary_address')) {
            Schema::create('user_temporary_address', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->nullable();
                $table->string('lat', 250)->nullable();
                $table->string('lng', 250)->nullable();
                $table->text('name')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
            });
        }

        // Skip OAuth tables as they're already created by Laravel Passport
        if (!Schema::hasTable('oauth_clients')) {
            Schema::create('oauth_clients', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('name');
                $table->string('secret', 100)->nullable();
                $table->string('provider')->nullable();
                $table->text('redirect');
                $table->boolean('personal_access_client');
                $table->boolean('password_client');
                $table->boolean('revoked');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('oauth_access_tokens')) {
            Schema::create('oauth_access_tokens', function (Blueprint $table) {
                $table->string('id', 100)->primary();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->unsignedBigInteger('client_id');
                $table->string('name')->nullable();
                $table->text('scopes')->nullable();
                $table->boolean('revoked');
                $table->timestamps();
                $table->dateTime('expires_at')->nullable();
            });
        }

        if (!Schema::hasTable('oauth_refresh_tokens')) {
            Schema::create('oauth_refresh_tokens', function (Blueprint $table) {
                $table->string('id', 100)->primary();
                $table->string('access_token_id', 100)->index();
                $table->boolean('revoked');
                $table->dateTime('expires_at')->nullable();
            });
        }

        if (!Schema::hasTable('oauth_auth_codes')) {
            Schema::create('oauth_auth_codes', function (Blueprint $table) {
                $table->string('id', 100)->primary();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('client_id');
                $table->text('scopes')->nullable();
                $table->boolean('revoked');
                $table->dateTime('expires_at')->nullable();
            });
        }

        if (!Schema::hasTable('oauth_personal_access_clients')) {
            Schema::create('oauth_personal_access_clients', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('client_id');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_review');
        Schema::dropIfExists('user_temporary_address');
        Schema::dropIfExists('oauth_refresh_tokens');
        Schema::dropIfExists('oauth_access_tokens');
        Schema::dropIfExists('oauth_auth_codes');
        Schema::dropIfExists('oauth_personal_access_clients');
        Schema::dropIfExists('oauth_clients');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'email_code',
                'featured',
                'stripe_id',
                'pm_type',
                'exp_month',
                'exp_year',
                'pm_last_four',
                'trial_ends_at',
            ]);
        });

        DB::statement('ALTER TABLE users MODIFY COLUMN first_name VARCHAR(255) NULL');
        DB::statement('ALTER TABLE users MODIFY COLUMN last_name VARCHAR(255) NULL');
        DB::statement('ALTER TABLE users MODIFY COLUMN phone VARCHAR(255) NULL');
        DB::statement('ALTER TABLE users MODIFY COLUMN travel_mode VARCHAR(255) NULL');
        DB::statement('ALTER TABLE users MODIFY COLUMN holiday_mode VARCHAR(255) NULL');
        DB::statement('ALTER TABLE users MODIFY COLUMN rush_service VARCHAR(255) NULL');

        Schema::table('barber_services', function (Blueprint $table) {
            $table->dropColumn('name');
        });

        DB::statement('ALTER TABLE barber_services MODIFY COLUMN user_id VARCHAR(255) NULL');
        DB::statement('ALTER TABLE barber_services MODIFY COLUMN service_id VARCHAR(255) NULL');
        DB::statement('ALTER TABLE barber_services MODIFY COLUMN price VARCHAR(255) NULL');
        DB::statement('ALTER TABLE barber_services MODIFY COLUMN main_service VARCHAR(255) NULL');

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'service_time_id',
                'event_type',
                'event_date',
                'number_of_people',
                'staff_preference',
                'service_location',
                'number_of_staff_member',
                'parking_instructions',
                'add_ons',
                'preferred_stylist_name',
            ]);
        });

        DB::statement('ALTER TABLE bookings MODIFY COLUMN member_id VARCHAR(255) NULL');
        DB::statement('ALTER TABLE bookings MODIFY COLUMN barber_id VARCHAR(255) NULL');
        DB::statement('ALTER TABLE bookings MODIFY COLUMN booking_time VARCHAR(255) NULL');
        DB::statement('ALTER TABLE bookings MODIFY COLUMN booking_date VARCHAR(255) NULL');
        DB::statement('ALTER TABLE bookings MODIFY COLUMN price VARCHAR(255) NULL');
        DB::statement('ALTER TABLE bookings MODIFY COLUMN dis_price VARCHAR(255) NULL');
        DB::statement('ALTER TABLE bookings MODIFY COLUMN total_price VARCHAR(255) NULL');
        DB::statement('ALTER TABLE bookings MODIFY COLUMN image VARCHAR(255) NULL');
        DB::statement('ALTER TABLE bookings MODIFY COLUMN custom_location VARCHAR(255) NULL');
        DB::statement('ALTER TABLE bookings MODIFY COLUMN status VARCHAR(10) NULL');

        Schema::table('booking_details', function (Blueprint $table) {
            $table->dropColumn(['customer_type', 'customer_name']);
        });

        DB::statement('ALTER TABLE booking_details MODIFY COLUMN booking_id VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE booking_details MODIFY COLUMN service_id VARCHAR(255) NOT NULL');

        if (Schema::hasColumn('booking_members', 'service_id')) {
            DB::statement('ALTER TABLE booking_members CHANGE service_id service VARCHAR(255) NULL');
        }

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['barber_id', 'booking_id']);
        });

        DB::statement('ALTER TABLE reviews MODIFY COLUMN member_id VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE reviews MODIFY COLUMN rating VARCHAR(255) NULL');

        DB::statement('ALTER TABLE service_timings MODIFY COLUMN barber_id VARCHAR(255) NOT NULL');

        Schema::table('support', function (Blueprint $table) {
            $table->dropColumn('job_id');
        });

        DB::statement('ALTER TABLE que_answers MODIFY COLUMN user_id VARCHAR(255) NULL');
        DB::statement('ALTER TABLE que_answers MODIFY COLUMN question_id VARCHAR(255) NULL');

        DB::statement('ALTER TABLE wallet MODIFY COLUMN user_id VARCHAR(255) NULL');
        DB::statement('ALTER TABLE wallet MODIFY COLUMN amount VARCHAR(255) NULL');

        Schema::table('transaction', function (Blueprint $table) {
            $table->dropColumn('pm_id');
        });

        DB::statement('ALTER TABLE transaction MODIFY COLUMN user_id VARCHAR(255) NULL');

        DB::statement('ALTER TABLE coupons MODIFY COLUMN min_price VARCHAR(255) NULL');

        Schema::table('wishlist', function (Blueprint $table) {
            $table->dropColumn(['product_id', 'type']);
        });

        DB::statement('ALTER TABLE posts MODIFY COLUMN barber_id BIGINT UNSIGNED NOT NULL');
    }
};
