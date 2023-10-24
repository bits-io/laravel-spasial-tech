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
        Schema::create('credits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('credit_type', 200);

            $table->string('name', 200);
            $table->double('total_transaction');
            $table->double('tenor');
            $table->double('total_credit');
            $table->enum('status', ['WAITING', 'PROCESSED', 'ONGOING', 'DONE']);

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')->on('users')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credits');
    }
};
