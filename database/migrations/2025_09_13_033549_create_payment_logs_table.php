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
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->integer('row_number')->nullable();
            $table->enum('status', ['failure', 'success'])->default('failure');
            $table->text('message')->nullable();
            $table->json('raw_data')->nullable();
            $table->timestamps();
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_logs');
    }
};
