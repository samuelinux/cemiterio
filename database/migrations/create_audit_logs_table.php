<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->foreignId('empresa_id')
                  ->nullable()
                  ->constrained('empresas')
                  ->nullOnDelete();

            $table->string('tabela', 100);
            $table->unsignedBigInteger('registro_id')->nullable();
            $table->string('acao', 50); // create, update, delete, restore, login, logout

            $table->json('valores_antes')->nullable();
            $table->json('valores_depois')->nullable();

            $table->string('ip', 45)->nullable(); // suporta IPv6
            $table->text('user_agent')->nullable();

            $table->timestamps(); // created_at = quando ocorreu
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
