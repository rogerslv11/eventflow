<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('ticket_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ticket_batch_id')->nullable()->constrained()->nullOnDelete();

            $table->string('participant_name');
            $table->string('participant_email');
            $table->string('participant_document')->nullable();
            $table->string('participant_phone')->nullable();
            $table->string('participant_company')->nullable();
            $table->string('participant_role')->nullable();

            $table->decimal('price_paid', 10, 2)->default(0);
            $table->string('status')->default('active'); // active, used, cancelled, refunded

            $table->string('qr_code')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->string('checked_in_by')->nullable();
            $table->string('check_in_ip')->nullable();

            $table->json('custom_fields')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['event_id', 'status']);
            $table->index('participant_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
