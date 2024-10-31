<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() :void
    {
        $tableName = config('laravel_ticket.table_names.messages', 'messages');

        Schema::create($tableName['table'], function (Blueprint $table) use ($tableName) {
            $table->id();
//            $table->foreignId($tableName['columns']['user_foreign_id']);
//            $table->foreignId($tableName['columns']['ticket_foreign_id']);
            $table->foreignId('sender_id')->constrained('users');
            $table->foreignId('ticket_id');
            $table->string('sender_name')->nullable();

            $table->text('message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
