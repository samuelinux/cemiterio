<?php

namespace App\Livewire\Empresa\Sepultamento\Traits;

use App\Models\Sepultamento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait WithSepultamentoExport
{
    public function exportToPdf()
    {
        $empresaId = Auth::user()->empresa_id;

        // Consulta com todos os campos originais
        $sepultamentos = Sepultamento::query()
            ->where('empresa_id', $empresaId)
            ->when($this->searchNome, fn ($q) => $q->where('nome_falecido', 'like', "%{$this->searchNome}%"))
            ->when($this->searchMae, fn ($q) => $q->where('mae', 'like', "%{$this->searchMae}%"))
            ->when($this->searchPai, fn ($q) => $q->where('pai', 'like', "%{$this->searchPai}%"))
            ->when($this->searchQuadra, fn ($q) => $q->where('quadra', 'like', "%{$this->searchQuadra}%"))
            ->when($this->searchFila, fn ($q) => $q->where('fila', 'like', "%{$this->searchFila}%"))
            ->when($this->searchCova, fn ($q) => $q->where('cova', 'like', "%{$this->searchCova}%"))
            ->when($this->searchFalecimentoDe, fn ($q) => $q->whereDate('data_falecimento', '>=', $this->searchFalecimentoDe))
            ->when($this->searchFalecimentoAte, fn ($q) => $q->whereDate('data_falecimento', '<=', $this->searchFalecimentoAte))
            ->when($this->searchSepultamentoDe, fn ($q) => $q->whereDate('data_sepultamento', '>=', $this->searchSepultamentoDe))
            ->when($this->searchSepultamentoAte, fn ($q) => $q->whereDate('data_sepultamento', '<=', $this->searchSepultamentoAte))
            ->when($this->searchStatus === 'ativo', fn ($q) => $q->where('ativo', true))
            ->when($this->searchStatus === 'inativo', fn ($q) => $q->where('ativo', false))
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
            ])
            ->map(function ($sepultamento) {
                return [
                    'nome_falecido' => $sepultamento->nome_falecido ?? '-',
                    'mae' => $sepultamento->mae ?? '-',
                    'pai' => $sepultamento->pai ?? '-',
                    'quadra' => $sepultamento->quadra ?? '-',
                    'fila' => $sepultamento->fila ?? '-',
                    'cova' => $sepultamento->cova ?? '-',
                    'data_falecimento' => $sepultamento->data_falecimento ? \Carbon\Carbon::parse($sepultamento->data_falecimento)->format('d/m/Y') : '-',
                    'data_sepultamento' => $sepultamento->data_sepultamento ? \Carbon\Carbon::parse($sepultamento->data_sepultamento)->format('d/m/Y') : '-',
                    'ativo' => $sepultamento->ativo ? 'Sim' : 'Não',
                ];
            })
            ->toArray();

        // Log para depuração
        Log::info('Exportando PDF', [
            'sepultamentos_count' => count($sepultamentos),
            'sepultamentos' => $sepultamentos,
            'filtros' => [
                'searchNome' => $this->searchNome,
                'searchMae' => $this->searchMae,
                'searchPai' => $this->searchPai,
                'searchQuadra' => $this->searchQuadra,
                'searchFila' => $this->searchFila,
                'searchCova' => $this->searchCova,
                'searchFalecimentoDe' => $this->searchFalecimentoDe,
                'searchFalecimentoAte' => $this->searchFalecimentoAte,
                'searchSepultamentoDe' => $this->searchSepultamentoDe,
                'searchSepultamentoAte' => $this->searchSepultamentoAte,
                'searchStatus' => $this->searchStatus,
            ],
        ]);

        if (empty($sepultamentos)) {
            $this->dispatch('toast', type: 'error', title: 'Nenhum sepultamento encontrado para exportar.');

            return;
        }

        // Armazenar dados na sessão para a rota de download
        session(['sepultamentos_pdf_data' => $sepultamentos]);

        // Log para confirmar que os dados foram armazenados
        Log::info('Dados armazenados na sessão para PDF', ['sepultamentos_count' => count($sepultamentos)]);

        // Disparar evento para redirecionar para a rota de download
        try {
            $url = route('download.sepultamentos.pdf');
            Log::info('Disparando redirecionamento', ['url' => $url]);
            $this->dispatch('redirect-to-download', url: $url);
        } catch (\Exception $e) {
            Log::error('Erro ao disparar redirecionamento', ['error' => $e->getMessage()]);
            $this->dispatch('toast', type: 'error', title: 'Erro ao gerar PDF', text: 'Falha ao redirecionar para o download. Contate o suporte.');
        }
    }
}
