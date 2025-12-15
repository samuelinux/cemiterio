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
    // ... existing code ...

    protected function getQuerySepultamentos()
    {
        $empresaId = Auth::user()->empresa_id;

        return Sepultamento::query()
            ->where('empresa_id', $empresaId)
            ->select('sepultamentos.*')
            ->selectRaw('ROW_NUMBER() OVER (  PARTITION BY empresa_id, quadra, fila, cova    ORDER BY data_sepultamento ASC, data_falecimento ASC, id ASC ) as ordem_sepultamento')

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
            // Filtros de data - usando whereRaw para converter diretamente no SQL
            // -------------------------
            ->when($this->searchFalecimentoDe && $this->isValidDate($this->searchFalecimentoDe), function ($q) {
                $q->whereRaw("DATE(data_falecimento) >= STR_TO_DATE(?, '%d/%m/%Y')", [$this->searchFalecimentoDe]);
            })
            ->when($this->searchFalecimentoAte && $this->isValidDate($this->searchFalecimentoAte), function ($q) {
                $q->whereRaw("DATE(data_falecimento) <= STR_TO_DATE(?, '%d/%m/%Y')", [$this->searchFalecimentoAte]);
            })
            ->when($this->searchSepultamentoDe && $this->isValidDate($this->searchSepultamentoDe), function ($q) {
                $q->whereRaw("DATE(data_sepultamento) >= STR_TO_DATE(?, '%d/%m/%Y')", [$this->searchSepultamentoDe]);
            })
            ->when($this->searchSepultamentoAte && $this->isValidDate($this->searchSepultamentoAte), function ($q) {
                $q->whereRaw("DATE(data_sepultamento) <= STR_TO_DATE(?, '%d/%m/%Y')", [$this->searchSepultamentoAte]);
            })

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
            ->when($this->sortField === 'localizacao_ordenada', function ($q) {
                // Ordenação especial por quadra, fila, cova e ordem_sepultamento
                return $q->orderByRaw("CAST(quadra AS UNSIGNED) {$this->sortDirection}, CAST(fila AS UNSIGNED) {$this->sortDirection}, CAST(cova AS UNSIGNED) {$this->sortDirection}, ordem_sepultamento {$this->sortDirection}");
            }, function ($q) {
                // Ordenação normal para outros campos
                return $q->orderByRaw("CASE WHEN {$this->sortField} IS NULL THEN 1 ELSE 0 END, {$this->sortField} {$this->sortDirection}");
            });
    }

    /**
     * Verifica se uma string é uma data válida no formato dd/mm/yyyy
     */
    private function isValidDate(string $date): bool
    {
        $date = trim($date);
        if (empty($date)) {
            return false;
        }

        // Verificar formato dd/mm/yyyy
        if (!preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $date)) {
            return false;
        }

        // Verificar se é uma data válida
        $parts = explode('/', $date);
        return checkdate((int)$parts[1], (int)$parts[0], (int)$parts[2]);
    }
    // ... existing code ...
}
