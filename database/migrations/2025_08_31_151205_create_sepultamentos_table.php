<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sepultamentos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();

            $table->string('nome_falecido');           // único obrigatório
            $table->string('mae')->nullable();
            $table->string('pai')->nullable();

            $table->boolean('indigente')->default(false);
            $table->boolean('natimorto')->default(false);
            $table->boolean('translado')->default(false);
            $table->boolean('membro')->default(false);

            $table->date('data_falecimento')->nullable();   // YYYY-MM-DD
            $table->date('data_sepultamento')->nullable();  // YYYY-MM-DD

            $table->string('quadra', 50)->nullable();
            $table->string('fila', 50)->nullable();
            $table->string('cova', 50)->nullable();

            // numeração sequencial por empresa e ano
            $table->unsignedSmallInteger('ano_referencia');         // geralmente o ano de data_sepultamento
            $table->unsignedInteger('numero_sepultamento');         // sequencial dentro do ano+empresa

            $table->string('certidao_obito_path', 512)->nullable(); // caminho do arquivo (privado)
            $table->text('observacoes')->nullable();

            $table->boolean('ativo')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // índices e unicidades
            $table->unique(['empresa_id','ano_referencia','numero_sepultamento'], 'sepultamento_numero_unique');

            $table->index(['empresa_id', 'data_sepultamento'], 'sepultamento_empresa_data_idx');
            $table->index(['empresa_id', 'nome_falecido'], 'sepultamento_empresa_nome_idx');
            $table->index(['empresa_id', 'quadra', 'fila', 'cova'], 'sepultamento_local_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sepultamentos');
    }
};
