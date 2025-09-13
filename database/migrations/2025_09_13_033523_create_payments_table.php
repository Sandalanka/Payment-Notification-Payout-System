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
            $table->string('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->decimal('original_amount', 16, 4)->nullable();
            $table->string('original_currency', 10)->nullable();
            $table->decimal('amount_usd', 16, 4)->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->string('reference_no')->nullable();
            $table->boolean('processed')->default(false);
            // $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->string('uploaded_file')->nullable();
            $table->timestamps();
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
