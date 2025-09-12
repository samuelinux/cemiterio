<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sepultamento_causa', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sepultamento_id')->constrained('sepultamentos')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('causa_morte_id')->constrained('causas_morte')->cascadeOnDelete()->cascadeOnUpdate();

            // sem "principal"; todas as causas são equivalentes
            // se quiser ordenar visualmente, opcional:
            // $table->unsignedSmallInteger('ordem')->nullable();

            $table->timestamps();

            $table->unique(['sepultamento_id', 'causa_morte_id'], 'sepultamento_causa_unique');
            $table->index('sepultamento_id');
            $table->index('causa_morte_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sepultamento_causa');
    }
};
