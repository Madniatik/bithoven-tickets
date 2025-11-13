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
        Schema::create('canned_responses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('shortcut')->unique(); // e.g., /greeting, /thanks
            $table->text('content');
            $table->foreignId('category_id')->nullable()->constrained('ticket_categories')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(false); // false = internal note
            $table->integer('usage_count')->default(0);
            $table->timestamps();

            $table->index('category_id');
            $table->index('shortcut');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('canned_responses');
    }
};
