<?php

// app/Livewire/Admin/UserPermissionsForm.php

namespace App\Livewire\Admin;

use App\Models\Permissao;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Livewire\Component;

class UserPermissionsForm extends Component
{
    public int $userId;

    /** Todas as tabelas reais do banco (filtradas) */
    public array $tables = [];

    /** Busca */
    public string $search = '';

    /**
     * rows[table] = [
     *   'selected'  => bool,
     *   'consultar' => bool,
     *   'cadastrar' => bool,
     *   'editar'    => bool,
     *   'excluir'   => bool,
     * ].
     */
    public array $rows = [];

    public function mount(int $userId): void
    {
        $this->userId = $userId;

        $this->tables = $this->listDatabaseTables();

        // inicia rows com todas as tabelas
        foreach ($this->tables as $t) {
            $this->rows[$t] = [
                'selected' => false,
                'consultar' => false,
                'cadastrar' => false,
                'editar' => false,
                'excluir' => false,
            ];
        }

        // aplica permissões existentes
        $perms = Permissao::where('user_id', $userId)->get();
        foreach ($perms as $p) {
            if (isset($this->rows[$p->tabela])) {
                $this->rows[$p->tabela] = [
                    'selected' => true,
                    'consultar' => (bool) $p->consultar,
                    'cadastrar' => (bool) $p->cadastrar,
                    'editar' => (bool) $p->editar,
                    'excluir' => (bool) $p->excluir,
                ];
            }
        }
    }

    /** Busca tabelas base do MySQL, exclui tabelas de infra do Laravel */
    protected function listDatabaseTables(): array
    {
        $rows = DB::select('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');
        if (empty($rows)) {
            return [];
        }

        // achar coluna Tables_in_{dbname}
        $first = (array) $rows[0];
        $col = collect(array_keys($first))->first(fn ($k) => str_starts_with($k, 'Tables_in_'));
        if (!$col) {
            return [];
        }

        $names = collect($rows)->map(fn ($r) => (array) $r)->pluck($col)->filter()->values();

        $exclude = [
            'migrations', 'failed_jobs', 'jobs', 'job_batches',
            'cache', 'sessions', 'password_reset_tokens', 'personal_access_tokens',
        ];

        return $names->reject(fn ($t) => in_array($t, $exclude, true))
                     ->sort()->values()->all();
    }

    /** Retorna as tabelas que passam no filtro de busca */
    protected function visibleTables(): array
    {
        $q = Str::lower(trim($this->search));

        return array_values(array_filter($this->tables, function ($t) use ($q) {
            return $q === '' || str_contains(Str::lower($t), $q);
        }));
    }

    /** Seleciona/Desmarca todas as visíveis */
    public function selectVisible(bool $state): void
    {
        foreach ($this->visibleTables() as $t) {
            $this->rows[$t]['selected'] = $state;

            if ($state
                && $this->rows[$t]['consultar'] === false
                && $this->rows[$t]['cadastrar'] === false
                && $this->rows[$t]['editar'] === false
                && $this->rows[$t]['excluir'] === false) {
                $this->rows[$t]['consultar'] = true; // padrão ao marcar
            }

            if (!$state) {
                $this->rows[$t]['consultar'] = false;
                $this->rows[$t]['cadastrar'] = false;
                $this->rows[$t]['editar'] = false;
                $this->rows[$t]['excluir'] = false;
            }
        }
    }

    /** Ação em massa por permissão para tabelas SELECIONADAS e VISÍVEIS */
    public function bulk(string $perm, bool $state): void
    {
        if (!in_array($perm, ['consultar', 'cadastrar', 'editar', 'excluir'], true)) {
            return;
        }

        foreach ($this->visibleTables() as $t) {
            if ($this->rows[$t]['selected']) {
                $this->rows[$t][$perm] = $state;
            }
        }
    }

    public function save(): void
    {
        // Apenas tabelas válidas
        $valid = collect($this->tables)->flip();

        $selected = [];
        foreach ($this->rows as $table => $r) {
            if (!$r['selected']) {
                continue;
            }

            if (!$valid->has($table) || !Schema::hasTable($table)) {
                $this->dispatch('swal', type: 'error', title: 'Tabela inválida', text: "A tabela '{$table}' não existe.");

                return;
            }

            Permissao::updateOrCreate(
                ['user_id' => $this->userId, 'tabela' => $table],
                [
                    'consultar' => (bool) $r['consultar'],
                    'cadastrar' => (bool) $r['cadastrar'],
                    'editar' => (bool) $r['editar'],
                    'excluir' => (bool) $r['excluir'],
                ]
            );

            $selected[] = $table;
        }

        // apaga as permissões que foram desmarcadas
        Permissao::where('user_id', $this->userId)
            ->when(!empty($selected), fn ($q) => $q->whereNotIn('tabela', $selected))
            ->when(empty($selected), fn ($q) => $q) // nada selecionado => remove todas
            ->delete();

        $this->dispatch('toast', type: 'success', title: 'Permissões atualizadas!');
    }

    public function render()
    {
        return view('livewire.admin.user-permissions-form', [
            'tableList' => $this->visibleTables(),
        ]);
    }
}
