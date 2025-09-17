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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('city_id');
            $table->unsignedBigInteger('area_id');

            $table->string('name', 60);
            $table->string('slug', 100);
            $table->double('price');
            $table->double('extra_person_cost')->nullable();
            $table->string('main_image', 200)->nullable();
            $table->string('video_link', 200)->nullable();
            $table->integer('default_capacity')->nullable();
            $table->integer('max_capacity')->nullable();
            // $table->date('from_date');
            // $table->date('to_date');
            $table->string('capacity_description', 200)->nullable();
            $table->string('price_description', 200)->nullable();
            $table->timestamp('created_at')
                ->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')
                ->default(
                    DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')
                );
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->tinyInteger('trash')->default(0);
            $table->tinyInteger('status')->default(1);

            //keys
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->foreign('area_id')->references('id')->on('areas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
