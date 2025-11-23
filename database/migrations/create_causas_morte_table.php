<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('causas_morte', function (Blueprint $table) {
            $table->id();
            $table->string('descricao');
            $table->string('codigo_cid10', 16)->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            $table->index('descricao');
            $table->index('codigo_cid10');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('causas_morte');
    }
};
