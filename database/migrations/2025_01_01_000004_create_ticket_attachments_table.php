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
        Schema::create('ticket_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')
                  ->constrained('tickets')
                  ->onDelete('cascade');
            
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            
            $table->string('filename');
            $table->string('original_filename');
            $table->string('mime_type');
            $table->integer('size'); // bytes
            $table->string('path');
            $table->string('disk')->default('public');
            
            $table->timestamps();
            
            // Indexes
            $table->index('ticket_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_attachments');
    }
};
