<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('speakers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('bio')->nullable();
            $table->string('photo_path')->nullable();
            $table->json('links')->nullable();
            $table->timestamps();
        });

        Schema::create('session_speaker', function (Blueprint $table): void {
            $table->foreignId('session_id')->constrained('event_sessions')->cascadeOnDelete();
            $table->foreignId('speaker_id')->constrained()->cascadeOnDelete();

            $table->primary(['session_id', 'speaker_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_speaker');
        Schema::dropIfExists('speakers');
    }
};
