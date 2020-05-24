<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // $table->id();
            // $table->string('name');
            // $table->string('email')->unique();
            // $table->timestamp('email_verified_at')->nullable();
            // $table->string('password');
            // $table->rememberToken();
            // $table->timestamps();
            $table->renameColumn('name', 'first_name');
            $table->string('last_name')->after('name');
            $table->enum('gender', ['Male', 'Female'])->after('last_name');
            $table->date('birthday')->after('gender');
            $table->string('document_number')->unique()->after('birthday');
            $table->enum('document_type', ['Passport', 'Driver License', 'Identity Card'])->after('document_number');
            $table->string('citizenship_country')->nullable()->after('document_type');
            $table->date('expiration_date')->nullable()->after('citizenship_country');
            $table->string('issuing_authority')->nullable()->after('expiration_date');
            $table->binary('document_photo')->nullable()->after('issuing_authority');
            $table->binary('user_photo')->nullable()->after('document_photo');
            $table->binary('professional_license_photo')->nullable()->after('user_photo');
            $table->boolean('is_professional')->default(false)->after('professional_license_photo');
            $table->boolean('is_activated')->default(false)->after('is_professional');
            $table->string('email')->nullable()->change();
            $table->string('email_verify_code')->nullable()->after('email');
            $table->string('password')->nullable()->change();

            $table->string('order_contact_name')->nullable()->after('email_verified_at');
            $table->string('order_email')->nullable()->after('order_contact_name');
            $table->string('order_phone_number')->nullable()->after('order_email');
            $table->string('order_address_1')->nullable()->after('order_phone_number');
            $table->string('order_address_2')->nullable()->after('order_address_1');

            $table->string('card_number')->nullable()->after('order_address_2');
            $table->string('card_holder')->nullable()->after('card_number');
            $table->string('card_expire_date')->nullable()->after('card_holder');
            $table->string('card_cvv')->nullable()->after('card_expire_date');

        });

        DB::statement('ALTER TABLE users DROP INDEX users_email_unique');
        DB::statement('ALTER TABLE users CHANGE COLUMN document_photo document_photo LONGBLOB');
        DB::statement('ALTER TABLE users CHANGE COLUMN user_photo user_photo LONGBLOB');
        DB::statement('ALTER TABLE users CHANGE COLUMN professional_license_photo professional_license_photo LONGBLOB');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->renameColumn('first_name', 'name');
            $table->dropColumn('last_name');
            $table->dropColumn('gender');
            $table->dropColumn('birthday');
            $table->dropColumn('document_number');
            $table->dropColumn('document_type');
            $table->dropColumn('citizenship_country');
            $table->dropColumn('expiration_date');
            $table->dropColumn('issuing_authority');
            $table->dropColumn('document_photo');
            $table->dropColumn('user_photo');
            $table->dropColumn('professional_license_photo');
            $table->dropColumn('is_professional');
            $table->dropColumn('is_activated');
            $table->dropColumn('email_verify_code');

            $table->dropColumn('order_contact_name');
            $table->dropColumn('order_email');
            $table->dropColumn('order_phone_number');
            $table->dropColumn('order_address_1');
            $table->dropColumn('order_address_2');

            $table->dropColumn('card_number');
            $table->dropColumn('card_holder');
            $table->dropColumn('card_expire_date');
            $table->dropColumn('card_cvc');
        });
    }
}
