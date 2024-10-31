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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable();
//            $table->foreignId('user_id');
            $table->foreignId('sender_id')->constrained('users');
            $table->string('title');
//            $table->string('message')->nullable();
            $table->enum('priority', ['low', 'medium', 'high'])->default('low');
            $table->enum('status', ['waiting for reply', 'in progress', 'replied', 'closed'])->default('waiting for reply');
            $table->boolean('is_resolved')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
