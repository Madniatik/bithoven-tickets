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
        Schema::table('ticket_attachments', function (Blueprint $table) {
            // Add comment_id column (nullable because attachments can belong to ticket OR comment)
            $table->foreignId('comment_id')
                  ->nullable()
                  ->after('ticket_id')
                  ->constrained('ticket_comments')
                  ->onDelete('cascade');
            
            // Add index for better query performance
            $table->index('comment_id');
            
            // Make ticket_id nullable since attachment can belong to ticket OR comment
            $table->foreignId('ticket_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_attachments', function (Blueprint $table) {
            // Drop foreign key and index
            $table->dropForeign(['comment_id']);
            $table->dropIndex(['comment_id']);
            $table->dropColumn('comment_id');
            
            // Restore ticket_id as NOT NULL
            $table->foreignId('ticket_id')->nullable(false)->change();
        });
    }
};
