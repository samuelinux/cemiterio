<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();

            $table->string('target_type', 60);     // ex.: "sepultamentos"
            $table->unsignedBigInteger('target_id');

            $table->string('evento', 60);          // ex.: update, delete, restore, attach_causa, detach_causa, upload_certidao...

            $table->json('diff')->nullable();      // campos alterados (antes/depois) quando aplicável
            $table->json('meta')->nullable();      // contexto: route, ip, ua, channel, permission, motivo, etc.

            $table->string('correlation_id', 64)->nullable(); // para agrupar eventos correlatos
            $table->string('tz_offset', 6)->nullable();       // ex.: "-03:00"

            $table->timestamps();

            $table->index(['empresa_id','target_type','target_id','created_at'], 'audit_target_idx');
            $table->index(['empresa_id','user_id','created_at'], 'audit_user_idx');
            $table->index(['empresa_id','evento','created_at'], 'audit_event_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
