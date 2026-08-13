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
        Schema::create('history', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable()->unique();
            $table->unsignedBigInteger('remote_id')->nullable()->index();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('template_id')->nullable()->constrained()->nullOnDelete();
            $table->text('title')->nullable();
            $table->text('blast');
            $table->enum('status', ['sent', 'failed', 'queued', 'draft', 'scheduled', 'cancelled'])->default('queued');
            $table->enum('sync_status', ['pending', 'blast_synced', 'synced', 'failed'])->default('pending');
            $table->integer('recipients')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->enum('type', ['automation', 'campaign'])->default('campaign');
            $table->enum('send_mode', ['now', 'scheduled', 'alltimes'])->default('now');
            $table->string('slug')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'scheduled_at']);
            $table->index('send_mode');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history');
    }
};
