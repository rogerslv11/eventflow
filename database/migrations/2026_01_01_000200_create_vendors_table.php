<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();

            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('document')->nullable();

            $table->string('category'); // buffet, security, photography, sound, lighting, decoration, transport, cleaning, other
            $table->decimal('cost', 10, 2)->nullable();

            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->json('contacts')->nullable();

            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
