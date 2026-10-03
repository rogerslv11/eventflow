<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();

            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();

            $table->string('type')->default('paid'); // free, paid, vip, premium, courtesy, press
            $table->decimal('price', 10, 2)->default(0);

            $table->integer('quantity')->default(0);
            $table->integer('sold')->default(0);
            $table->integer('reserved')->default(0);

            $table->integer('min_per_order')->default(1);
            $table->integer('max_per_order')->default(10);

            $table->dateTime('sales_start_at')->nullable();
            $table->dateTime('sales_end_at')->nullable();

            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->json('benefits')->nullable();
            $table->json('settings')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['event_id', 'slug']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_types');
    }
};
