<?php

namespace App\Livewire\Empresa\Sepultamento\Traits;

use App\Models\Empresa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait WithSepultamentoExport
{
    public function exportToPdf()
    {
        $empresaId = Auth::user()->empresa_id;

        // Consulta com todos os campos originais
        $sepultamentos = $this->getQuerySepultamentos()
            ->orderBy('nome_falecido', 'asc')
            ->get([
                'nome_falecido',
                'mae',
                'pai',
                'quadra',
                'fila',
                'cova',
                'data_falecimento',
                'data_sepultamento',
                'ativo',
                'indigente',
                'natimorto',
                'translado',
                'membro',
            ])
            ->map(function ($sepultamento) {
                return [
                    'nome_falecido' => $sepultamento->nome_falecido ?? '-',
                    'mae' => $sepultamento->mae ?? '-',
                    'pai' => $sepultamento->pai ?? '-',
                    'quadra' => $sepultamento->quadra ?? '-',
                    'fila' => $sepultamento->fila ?? '-',
                    'cova' => $sepultamento->cova ?? '-',
                    'data_falecimento' => $sepultamento->data_falecimento
                        ? \Carbon\Carbon::parse($sepultamento->data_falecimento)->format('d/m/Y') : '-',
                    'data_sepultamento' => $sepultamento->data_sepultamento
                        ? \Carbon\Carbon::parse($sepultamento->data_sepultamento)->format('d/m/Y') : '-',
                    'ativo' => $sepultamento->ativo ? 'Sim' : 'Não',
                    'indigente' => $sepultamento->indigente ? 'Sim' : 'Não',
                    'natimorto' => $sepultamento->natimorto ? 'Sim' : 'Não',
                    'translado' => $sepultamento->translado ? 'Sim' : 'Não',
                    'membro' => $sepultamento->membro ? 'Sim' : 'Não',
                ];
            })
            ->toArray();

        if (empty($sepultamentos)) {
            $this->dispatch('toast', type: 'error', title: 'Nenhum sepultamento encontrado para exportar.');

            return;
        }

        // Buscar empresa e guardar em outra chave de sessão
        $empresa = Empresa::find($empresaId);

        session([
            'sepultamentos_pdf_data' => $sepultamentos, // continua igual (não quebra a rota)
            'sepultamentos_empresa' => $empresa ? $empresa->toArray() : null,
        ]);

        Log::info('Exportação PDF preparada', [
            'empresa' => $empresa?->nome,
            'qtd' => count($sepultamentos),
        ]);

        // Disparar evento para redirecionar para a rota de download
        try {
            $url = route('download.sepultamentos.pdf');
            $this->dispatch('redirect-to-download', url: $url);
        } catch (\Exception $e) {
            Log::error('Erro ao disparar redirecionamento', ['error' => $e->getMessage()]);
            $this->dispatch('toast', type: 'error', title: 'Erro ao gerar PDF', text: 'Falha ao redirecionar para o download.');
        }
    }
}
