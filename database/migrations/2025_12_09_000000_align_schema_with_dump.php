<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
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

        if (!Schema::hasColumn('barber_services', 'name')) {
            Schema::table('barber_services', function (Blueprint $table) {
                $table->string('name', 250)->nullable()->after('service_id');
            });
        }

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
                $table->integer('barber_id')->nullable()->after('member_id');
            });
        }
        if (!Schema::hasColumn('reviews', 'booking_id')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->integer('booking_id')->nullable()->after('barber_id');
            });
        }

        if (!Schema::hasColumn('support', 'job_id')) {
            Schema::table('support', function (Blueprint $table) {
                $table->integer('job_id')->nullable()->after('user_id');
            });
        }

        if (!Schema::hasColumn('transaction', 'pm_id')) {
            Schema::table('transaction', function (Blueprint $table) {
                $table->string('pm_id', 250)->nullable()->after('user_id');
            });
        }

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

        $userDrop = array_filter([
            'email_code',
            'featured',
            'stripe_id',
            'pm_type',
            'exp_month',
            'exp_year',
            'pm_last_four',
            'trial_ends_at',
        ], fn ($col) => Schema::hasColumn('users', $col));

        if ($userDrop) {
            Schema::table('users', function (Blueprint $table) use ($userDrop) {
                $table->dropColumn($userDrop);
            });
        }

        if (Schema::hasColumn('barber_services', 'name')) {
            Schema::table('barber_services', function (Blueprint $table) {
                $table->dropColumn('name');
            });
        }

        $bookingDrop = [
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
        ];
        $existingBookingDrops = array_filter($bookingDrop, fn ($col) => Schema::hasColumn('bookings', $col));
        if ($existingBookingDrops) {
            Schema::table('bookings', function (Blueprint $table) use ($existingBookingDrops) {
                $table->dropColumn($existingBookingDrops);
            });
        }

        $bookingDetailsDrop = array_filter(['customer_type', 'customer_name'], fn ($col) => Schema::hasColumn('booking_details', $col));
        if ($bookingDetailsDrop) {
            Schema::table('booking_details', function (Blueprint $table) use ($bookingDetailsDrop) {
                $table->dropColumn($bookingDetailsDrop);
            });
        }

        $reviewsDrop = array_filter(['barber_id', 'booking_id'], fn ($col) => Schema::hasColumn('reviews', $col));
        if ($reviewsDrop) {
            Schema::table('reviews', function (Blueprint $table) use ($reviewsDrop) {
                $table->dropColumn($reviewsDrop);
            });
        }

        if (Schema::hasColumn('support', 'job_id')) {
            Schema::table('support', function (Blueprint $table) {
                $table->dropColumn('job_id');
            });
        }

        if (Schema::hasColumn('transaction', 'pm_id')) {
            Schema::table('transaction', function (Blueprint $table) {
                $table->dropColumn('pm_id');
            });
        }

        $wishlistDrop = array_filter(['product_id', 'type'], fn ($col) => Schema::hasColumn('wishlist', $col));
        if ($wishlistDrop) {
            Schema::table('wishlist', function (Blueprint $table) use ($wishlistDrop) {
                $table->dropColumn($wishlistDrop);
            });
        }
    }
};
