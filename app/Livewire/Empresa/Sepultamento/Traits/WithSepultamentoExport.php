<?php

namespace App\Livewire\Empresa\Sepultamento\Traits;

use App\Models\Empresa;
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
            ->when(
                $this->filtroIndigente || $this->filtroNatimorto || $this->filtroTranslado || $this->filtroMembro,
                function ($consulta) {
                    $consulta->where(function ($subconsulta) {
                        if ($this->filtroIndigente) {
                            $subconsulta->orWhere('indigente', true);
                        }
                        if ($this->filtroNatimorto) {
                            $subconsulta->orWhere('natimorto', true);
                        }
                        if ($this->filtroTranslado) {
                            $subconsulta->orWhere('translado', true);
                        }
                        if ($this->filtroMembro) {
                            $subconsulta->orWhere('membro', true);
                        }
                    });
                }
            )

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
