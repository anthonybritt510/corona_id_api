<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('user_name')->nullable();
            $table->foreignId('tester_id');
            $table->string('tester_name')->nullable();
            $table->string('viral_count');
            $table->boolean('is_positive');
            $table->string('testkit_number')->nullable();
            $table->boolean('is_confirmed')->default(0);
            $table->dateTime('test_date')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tests');
    }
}
