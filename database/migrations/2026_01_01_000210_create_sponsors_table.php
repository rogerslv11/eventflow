<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();

            $table->string('name');
            $table->string('logo_path')->nullable();
            $table->string('website')->nullable();
            $table->text('description')->nullable();

            $table->string('tier')->default('bronze'); // master, gold, silver, bronze, support
            $table->decimal('contribution', 10, 2)->nullable();
            $table->integer('sort_order')->default(0);

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['event_id', 'tier']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsors');
    }
};
