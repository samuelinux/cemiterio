<?php

namespace App\Livewire\Empresa\Sepultamento\Traits;

use App\Models\Sepultamento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

trait WithSepultamentoExport
{
    public function exportToPdf()
    {
        $empresaId = Auth::user()->empresa_id;

        // Mesma query usada no método render
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
            ->orderByDesc('data_sepultamento')
            ->get(['nome_falecido']);

        // Log para depuração
        Log::info('Exportando PDF', [
            'sepultamentos_count' => $sepultamentos->count(),
            'sepultamentos' => $sepultamentos->pluck('nome_falecido')->toArray(),
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

        if ($sepultamentos->isEmpty()) {
            $this->dispatch('toast', type: 'error', title: 'Nenhum sepultamento encontrado para exportar.');
            return;
        }

        // Armazenar dados na sessão para a rota de download
        session(['sepultamentos_pdf_data' => $sepultamentos->toArray()]);

        // Log para confirmar que os dados foram armazenados
        Log::info('Dados armazenados na sessão para PDF', ['sepultamentos_count' => $sepultamentos->count(), 'sepultamentos' => $sepultamentos->toArray()]);

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