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
        Schema::create('sepultamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users'); // quem registou
            
            // Dados do falecido
            $table->string('nome_falecido');
            $table->string('cpf_falecido')->nullable();
            $table->date('data_nascimento')->nullable();
            $table->date('data_falecimento');
            $table->string('causa_morte')->nullable();
            $table->string('naturalidade')->nullable();
            $table->string('profissao')->nullable();
            $table->enum('estado_civil', ['solteiro', 'casado', 'divorciado', 'viuvo'])->nullable();
            $table->enum('sexo', ['masculino', 'feminino'])->nullable();
            
            // Dados do sepultamento
            $table->date('data_sepultamento');
            $table->time('hora_sepultamento')->nullable();
            $table->string('local_sepultamento');
            $table->string('quadra')->nullable();
            $table->string('gaveta')->nullable();
            $table->string('numero_sepultura')->nullable();
            $table->enum('tipo_sepultamento', ['inumacao', 'cremacao'])->default('inumacao');
            
            // Dados do responsável
            $table->string('nome_responsavel');
            $table->string('cpf_responsavel')->nullable();
            $table->string('telefone_responsavel')->nullable();
            $table->string('parentesco')->nullable();
            
            // Documentação
            $table->string('numero_certidao_obito')->nullable();
            $table->string('cartorio_certidao')->nullable();
            $table->string('numero_declaracao_obito')->nullable();
            
            // Observações
            $table->text('observacoes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sepultamentos');
    }
};
