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
        Schema::create('donation_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->string('razorpay_order_id')->unique(); // Unique Razorpay order ID
            $table->string('razorpay_payment_id')->nullable(); // Razorpay payment ID
            $table->string('razorpay_signature')->nullable(); // Razorpay signature
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending'); // Payment status
            $table->decimal('amount', 10, 2); // Amount in INR (use decimals for precision)
            $table->string('entity')->nullable(); // Entity type
            $table->string('currency', 3)->default('INR'); // Currency code
            $table->string('payment_status')->nullable(); // Payment status (captured, authorized, etc.)
            $table->string('invoice_id')->nullable(); // Razorpay invoice ID
            $table->boolean('international')->default(false); // International payment flag
            $table->string('method')->nullable(); // Payment method (card, UPI, etc.)
            $table->decimal('amount_refunded', 10, 2)->default(0); // Refunded amount
            $table->string('refund_status')->nullable(); // Refund status
            $table->boolean('captured')->default(false); // Captured flag
            $table->text('description')->nullable(); // Payment description
            $table->string('card_id')->nullable(); // Card ID used in payment
            $table->string('bank')->nullable(); // Bank details
            $table->string('wallet')->nullable(); // Wallet used (if applicable)
            $table->string('vpa')->nullable(); // Virtual Payment Address (for UPI)
            $table->string('email')->nullable(); // Customer email
            $table->string('contact')->nullable(); // Customer contact number
            $table->json('notes')->nullable(); // Notes as JSON
            $table->decimal('fee', 10, 2)->nullable(); // Transaction fee
            $table->decimal('tax', 10, 2)->nullable(); // Tax on the transaction
            $table->string('error_code')->nullable(); // Error code, if any
            $table->text('error_description')->nullable(); // Error description, if any
            $table->timestamp('pay_created_at')->nullable(); // Razorpay's created_at timestamp
            $table->timestamps(); // Laravel's created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donation_transactions');
    }
};
