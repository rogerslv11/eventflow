<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ticket_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('name');
            $table->string('email');
            $table->string('document')->nullable();
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('role')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 2)->nullable();

            $table->string('status')->default('confirmed'); // confirmed, cancelled, pending, attended
            $table->boolean('has_checked_in')->default(false);
            $table->timestamp('checked_in_at')->nullable();

            $table->json('custom_fields')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['event_id', 'status']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
