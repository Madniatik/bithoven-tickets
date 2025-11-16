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
        Schema::create('ticket_notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Notification preferences (default: all enabled)
            $table->boolean('ticket_created')->default(true);
            $table->boolean('ticket_assigned')->default(true);
            $table->boolean('comment_added')->default(true);
            $table->boolean('status_changed')->default(true);
            $table->boolean('priority_escalated')->default(true);
            
            $table->timestamps();
            
            // Ensure one preference record per user
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_notification_preferences');
    }
};
