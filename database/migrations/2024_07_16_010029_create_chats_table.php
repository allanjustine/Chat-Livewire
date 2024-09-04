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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->nullable();
            $table->foreignId('receiver_id')->nullable();
            $table->longText('attachment')->nullable();
            $table->text('message')->nullable();
            $table->boolean('is_seen')->nullable()->default(false);
            $table->enum('status', ['unsent', 'removed', 'active'])->nullable()->default('active');
            $table->boolean('deleted_by_sender')->nullable()->default(false);
            $table->boolean('deleted_by_receiver')->nullable()->default(false);
            $table->timestamps();

            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
