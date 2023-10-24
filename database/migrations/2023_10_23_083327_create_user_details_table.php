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
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');

            $table->string('first_name', 200);
            $table->string('last_name', 200)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('gender', 20);
            $table->date('date_of_birth');
            $table->string('img_ktp', 255)->nullable();

            $table->foreign('user_id')->on('users')->references('id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
