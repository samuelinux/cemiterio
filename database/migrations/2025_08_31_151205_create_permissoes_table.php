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
        Schema::create('permissoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('tabela'); // nome da tabela (sepultamentos, empresas, etc.)
            $table->boolean('consultar')->default(false);
            $table->boolean('cadastrar')->default(false);
            $table->boolean('editar')->default(false);
            $table->boolean('excluir')->default(false);
            $table->timestamps();
            
            $table->unique(['user_id', 'tabela']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissoes');
    }
};
