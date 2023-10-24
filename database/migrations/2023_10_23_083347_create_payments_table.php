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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('credit_id');

            $table->double('amount');
            $table->enum('status', ['PENDING', 'PAID', 'FAILED', 'REFUNDED']);

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('credit_id')->on('credits')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
