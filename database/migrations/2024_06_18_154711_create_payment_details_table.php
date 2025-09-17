<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_details', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('booking_id');
            $table->string('r_payment_id')->nullable();
            $table->string('method')->nullable();
            $table->string('currency')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->double('amount');
            $table->string('r_payment_status');
            $table->boolean('ff_payment_status');
            $table->text('json_response');

            $table->timestamp('created_at')
                ->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')
                ->default(
                    DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')
                );
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);


            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('trash')->default(0);

            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_details');
    }
};
