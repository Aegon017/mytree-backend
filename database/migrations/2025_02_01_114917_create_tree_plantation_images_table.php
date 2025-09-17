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
        Schema::create('tree_plantation_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tree_plantation_id'); // FK to tree_plantations table
            $table->string('image'); // The path or URL of the image
            $table->timestamps();
            // Foreign Key Constraint
            $table->foreign('tree_plantation_id')->references('id')->on('tree_plantations')->onDelete('cascade'); // Cascade delete images if plantation is deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tree_plantation_images');
    }
};
