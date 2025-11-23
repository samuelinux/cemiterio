<?php

namespace Database\Seeders;

use App\Models\CausaMorte;
use Illuminate\Database\Seeder;

class CausaMorteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $causas = [
            ['descricao' => 'Infarto Agudo do Miocárdio', 'codigo_cid10' => 'I21', 'ativo' => true],
            ['descricao' => 'Acidente Vascular Cerebral (AVC)', 'codigo_cid10' => 'I64', 'ativo' => true],
            ['descricao' => 'Pneumonia', 'codigo_cid10' => 'J18', 'ativo' => true],
            ['descricao' => 'COVID-19', 'codigo_cid10' => 'U07.1', 'ativo' => true],
            ['descricao' => 'Diabetes Mellitus', 'codigo_cid10' => 'E14', 'ativo' => true],
            ['descricao' => 'Hipertensão Essencial (Primária)', 'codigo_cid10' => 'I10', 'ativo' => true],
            ['descricao' => 'Câncer de Pulmão', 'codigo_cid10' => 'C34', 'ativo' => true],
        ];

        foreach ($causas as $causa) {
            CausaMorte::updateOrCreate(
                ['codigo_cid10' => $causa['codigo_cid10']], // evita duplicados
                $causa
            );
        }
    }
}
