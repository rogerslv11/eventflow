<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('speaker_id')->nullable()->constrained()->nullOnDelete();

            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type')->default('talk'); // talk, workshop, panel, break, show, activity, opening, closing
            $table->string('room')->nullable();

            $table->dateTime('starts_at');
            $table->dateTime('ends_at');

            $table->integer('capacity')->nullable();
            $table->boolean('is_featured')->default(false);

            $table->timestamps();

            $table->index(['event_id', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
