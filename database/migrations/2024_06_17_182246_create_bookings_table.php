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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->string('payment_id', 60)->unique();
            $table->unsignedBigInteger('package_id');
            $table->unsignedBigInteger('decor_id')->nullable();
            $table->unsignedBigInteger('cake_id')->nullable();
            $table->unsignedBigInteger('time_duration_slot_id');
            $table->unsignedBigInteger('slot_id');

            $table->integer('no_of_persons');
            $table->string('name_of_person_decor')->nullable();
            $table->string('occasion')->nullable();
            $table->string('name_on_cake')->nullable();
            $table->string('user_name');
            $table->string('user_mobile')->nullable();
            $table->string('user_email');
            $table->string('default_capacity');

            $table->double('package_price');
            $table->double('extra_person_price')->nullable();
            $table->double('decor_price')->nullable();
            $table->double('cake_price')->nullable();
            $table->double('package_total_amount');
            $table->double('advance_amount');
            $table->double('convenience_fee');
            $table->double('payable_amount');
            $table->timestamp('created_at')
                ->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')
                ->default(
                    DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')
                );
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);

            $table->tinyInteger('payment_status')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('trash')->default(0);

            //keys
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
            $table->foreign('decor_id')->references('id')->on('add_ons')->onDelete('cascade');
            $table->foreign('cake_id')->references('id')->on('add_ons')->onDelete('cascade');
            $table->foreign('time_duration_slot_id')->references('id')->on('slot_durations')->onDelete('cascade');
            $table->foreign('slot_id')->references('id')->on('slots')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
