<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('event_categories')->nullOnDelete();

            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->longText('content')->nullable();

            $table->string('image_path')->nullable();
            $table->string('banner_path')->nullable();

            $table->string('venue_name')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 2)->nullable();
            $table->string('zipcode', 10)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->string('timezone')->default('America/Sao_Paulo');

            $table->integer('capacity')->nullable();
            $table->integer('tickets_sold')->default(0);
            $table->integer('check_ins_count')->default(0);

            $table->string('status')->default('draft'); // draft, published, active, finished, cancelled, archived
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_public')->default(true);

            $table->json('settings')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'slug']);
            $table->index(['status', 'starts_at']);
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
