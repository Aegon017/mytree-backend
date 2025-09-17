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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Reference to the user
            $table->string('endpoint'); // API endpoint
            $table->string('method'); // HTTP method
            $table->text('request_payload')->nullable(); // Request data
            $table->text('response_payload')->nullable(); // Response data
            $table->ipAddress('ip_address'); // Client IP address
            $table->string('user_agent')->nullable(); // Client's User Agent
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
