<?php

namespace App\Livewire\Empresa\Sepultamento\Traits;

use App\Models\Sepultamento;
use Illuminate\Support\Facades\Auth;

trait WithSepultamentoQuery
{
    /**
     * Monta a consulta base de sepultamentos aplicando todos os filtros disponíveis.
     *
     * Este método retorna apenas o Query Builder, sem executar a consulta.
     * O consumidor decide se usa ->paginate(), ->get(), ->count() etc.
     */
    protected function getQuerySepultamentos()
    {
        $empresaId = Auth::user()->empresa_id;

        return Sepultamento::query()
            ->where('empresa_id', $empresaId)
            ->select('sepultamentos.*')
            ->selectRaw('ROW_NUMBER() OVER (  PARTITION BY empresa_id, quadra, fila, cova    ORDER BY data_sepultamento ASC, id ASC ) as ordem_sepultamento')

            // -------------------------
            // Filtros de texto (LIKE)
            // -------------------------
            ->when($this->searchNome, fn($q) => $q->where('nome_falecido', 'like', "%{$this->searchNome}%"))
            ->when($this->searchMae, fn($q) => $q->where('mae', 'like', "%{$this->searchMae}%"))
            ->when($this->searchPai, fn($q) => $q->where('pai', 'like', "%{$this->searchPai}%"))
            ->when($this->searchQuadra, fn($q) => $q->where('quadra', $this->searchQuadra))
            ->when($this->searchFila, fn($q) => $q->where('fila', $this->searchFila))
            ->when($this->searchCova, fn($q) => $q->where('cova', $this->searchCova))

            // -------------------------
            // Filtros de data
            // -------------------------
            ->when($this->searchFalecimentoDe, fn($q) => $q->whereDate('data_falecimento', '>=', $this->searchFalecimentoDe))
            ->when($this->searchFalecimentoAte, fn($q) => $q->whereDate('data_falecimento', '<=', $this->searchFalecimentoAte))
            ->when($this->searchSepultamentoDe, fn($q) => $q->whereDate('data_sepultamento', '>=', $this->searchSepultamentoDe))
            ->when($this->searchSepultamentoAte, fn($q) => $q->whereDate('data_sepultamento', '<=', $this->searchSepultamentoAte))

            // -------------------------
            // Filtros de Ano / Mês / Dia (inteligentes)
            // -------------------------

            // Se informou o ano → aplica whereYear
            ->when(
                $this->searchAno,
                fn($q) => $q->whereYear('data_sepultamento', $this->searchAno)
            )

            // Se informou o mês (pode ter ano junto ou não)
            ->when(
                $this->searchMes,
                fn($q) => $q->whereMonth('data_sepultamento', $this->searchMes)
            )

            // Se informou o dia (pode ter ano/mes junto ou não)
            ->when(
                $this->searchDia,
                fn($q) => $q->whereDay('data_sepultamento', $this->searchDia)
            )

            // -------------------------
            // Filtro de status
            // -------------------------
            ->when($this->searchStatus === 'ativo', fn($q) => $q->where('ativo', true))
            ->when($this->searchStatus === 'inativo', fn($q) => $q->where('ativo', false))

            // -------------------------
            // Filtros booleanos de classificação (OR)
            // -------------------------
            ->when($this->filtroIndigente, fn($q) => $q->where('indigente', true))
            ->when($this->filtroNatimorto, fn($q) => $q->where('natimorto', true))
            ->when($this->filtroTranslado, fn($q) => $q->where('translado', true))
            ->when($this->filtroMembro, fn($q) => $q->where('membro', true))
            ->orderBy($this->sortField, $this->sortDirection);
        ;
    }
}
