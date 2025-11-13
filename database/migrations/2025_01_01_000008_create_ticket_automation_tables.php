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
        Schema::create('ticket_automation_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // auto_close, auto_escalate, auto_assign, auto_response
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            
            // Conditions (JSON)
            $table->json('conditions')->nullable(); // category_id, priority, status, age_hours, etc.
            
            // Actions (JSON)
            $table->json('actions'); // close, escalate, assign_to, send_response, etc.
            
            // Configuration (JSON)
            $table->json('config')->nullable(); // timeouts, thresholds, templates, etc.
            
            // Execution tracking
            $table->integer('execution_count')->default(0);
            $table->timestamp('last_executed_at')->nullable();
            
            // Metadata
            $table->integer('execution_order')->default(0); // Order of execution
            
            $table->timestamps();
            
            $table->index(['type', 'is_active']);
            $table->index('execution_order');
        });

        // Log table for automation actions
        Schema::create('ticket_automation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_id')->constrained('ticket_automation_rules')->onDelete('cascade');
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->string('action_type'); // closed, escalated, assigned, responded
            $table->json('action_data')->nullable(); // old_value, new_value, etc.
            $table->text('result')->nullable(); // success message or error
            $table->timestamp('executed_at');
            
            $table->index(['ticket_id', 'executed_at']);
            $table->index('executed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_automation_logs');
        Schema::dropIfExists('ticket_automation_rules');
    }
};
