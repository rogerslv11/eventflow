<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('check_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('participant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('status'); // success, duplicate, invalid, expired, cancelled
            $table->string('code_scanned')->nullable();
            $table->string('gate')->nullable();
            $table->string('location')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->text('notes')->nullable();

            $table->timestamp('checked_in_at');
            $table->timestamps();

            $table->index(['event_id', 'status']);
            $table->index('checked_in_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('check_ins');
    }
};
