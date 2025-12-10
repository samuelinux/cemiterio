<?php

namespace App\Livewire\Empresa\Sepultamento\Traits;

use App\Models\Sepultamento;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

trait WithSepultamentoCrud
{
    public function create(): void
    {
        if (!$this->canCreate) {
            $this->logAcao('permission.denied', null, null, ['acao' => 'cadastrar']);
            $this->dispatch('toast', type: 'error', title: 'Sem permissão para cadastrar.');

            return;
        }

        $this->resetValidation();
        $this->resetForm();
        $this->logAcao('ui.open_create_modal');
        $this->showCreateModal = true;
    }

    public function store(): void
    {
        if (!$this->canCreate) {
            $this->dispatch('toast', type: 'error', title: 'Sem permissão para cadastrar.');

            return;
        }

        try {
            $this->validate();

            $empresaId = Auth::user()->empresa_id;
            $userId = Auth::id();

            DB::transaction(function () use ($empresaId, $userId) {
                $data = $this->formData($empresaId, $userId);

                if ($this->certidao_obito) {
                    // Armazena apenas o nome do arquivo, não o caminho completo
                    $fileName = $this->certidao_obito->store('certidoes', 'public');
                    // Remove o prefixo 'certidoes/' para armazenar apenas o nome do arquivo
                    $data['certidao_obito_path'] = basename($fileName);
                }

                $s = Sepultamento::create($data);
                $s->causas()->sync($this->causasSelecionadas ?? []);

                $this->logAcao('create.success', $s->id);
            });

            $this->dispatch('toast', type: 'success', title: 'Sepultamento cadastrado!');
            $this->closeModals();
            $this->resetPage();
            $this->resetForm();
        } catch (ValidationException $e) {
            $this->handleValidationException($e, 'create');
            throw $e;
        } catch (\Throwable $e) {
            $this->handleThrowable($e, 'create');
        }
    }

    public function edit(int $id): void
    {
        if (!$this->canEdit) {
            $this->logAcao('permission.denied', $id, null, ['acao' => 'editar']);
            $this->dispatch('toast', type: 'error', title: 'Sem permissão para editar.');

            return;
        }

        $this->resetValidation();

        $empresaId = Auth::user()->empresa_id;

        $s = Sepultamento::with('causas')
            ->where('empresa_id', $empresaId)
            ->findOrFail($id);

        $this->preencherFormulario($s);

        $this->logAcao('ui.open_edit_modal', $id);
        $this->showEditModal = true;
    }

    public function update(): void
    {
        if (!$this->canEdit) {
            $this->dispatch('toast', type: 'error', title: 'Sem permissão para editar.');

            return;
        }

        if (!$this->sepultamentoId) {
            $this->dispatch('swal', type: 'error', title: 'Registro inválido.');

            return;
        }

        try {
            $this->validate();

            $empresaId = Auth::user()->empresa_id;

            DB::transaction(function () use ($empresaId) {
                $s = Sepultamento::where('empresa_id', $empresaId)
                    ->findOrFail($this->sepultamentoId);

                $antes = $s->getOriginal();

                $data = $this->formData($empresaId, $s->user_id);

                if ($this->certidao_obito) {
                    if ($s->certidao_obito_path) {
                        Storage::disk('public')->delete($s->certidao_obito_path);
                    }
                    // Armazena apenas o nome do arquivo, não o caminho completo
                    $fileName = $this->certidao_obito->store('certidoes', 'public');
                    // Remove o prefixo 'certidoes/' para armazenar apenas o nome do arquivo
                    $data['certidao_obito_path'] = basename($fileName);
                }

                $s->update($data);
                $s->causas()->sync($this->causasSelecionadas ?? []);

                $this->logAcao('update.success', $s->id, $antes, $s->getChanges());
            });

            $this->dispatch('toast', type: 'success', title: 'Sepultamento atualizado!');
            $this->closeModals();
            $this->resetForm();
        } catch (ValidationException $e) {
            $this->handleValidationException($e, 'update');
            throw $e;
        } catch (\Throwable $e) {
            $this->handleThrowable($e, 'update');
        }
    }

    public function confirmDelete(int $sepultamentoId): void
    {
        if (!$this->canDelete) {
            $this->logAcao('permission.denied', $sepultamentoId, null, ['acao' => 'excluir']);
            $this->dispatch('toast', type: 'error', title: 'Sem permissão para excluir.');

            return;
        }

        $empresaId = Auth::user()->empresa_id;
        $s = Sepultamento::where('empresa_id', $empresaId)->findOrFail($sepultamentoId);

        $this->logAcao('delete.confirm', $s->id);

        $this->dispatch('swal-confirm-delete',
            id: $s->id,
            title: 'Excluir sepultamento?',
            text: "Confirma excluir o sepultamento de {$s->nome_falecido}?"
        );
    }

    public function delete(int $sepultamentoId): void
    {
        try {
            if (!$this->canDelete) {
                $this->dispatch('toast', type: 'error', title: 'Você não tem permissão para excluir.');

                return;
            }

            $empresaId = Auth::user()->empresa_id;

            $s = Sepultamento::where('empresa_id', $empresaId)
                ->findOrFail($sepultamentoId);

            if ($s->certidao_obito_path) {
                Storage::disk('public')->delete($s->certidao_obito_path);
            }

            $s->delete();
            $this->logAcao('delete.success', $sepultamentoId);

            $this->dispatch('toast', type: 'success', title: 'Sepultamento excluído!');
            $this->resetPage();
        } catch (\Throwable $e) {
            $this->handleThrowable($e, 'delete', $sepultamentoId);
        }
    }

    private function preencherFormulario(Sepultamento $sepultamento): void
    {
        $this->sepultamentoId = $sepultamento->id;
        $this->nome_falecido = $sepultamento->nome_falecido;
        $this->mae = $sepultamento->mae;
        $this->pai = $sepultamento->pai;
        $this->indigente = $sepultamento->indigente;
        $this->natimorto = $sepultamento->natimorto;
        $this->translado = $sepultamento->translado;
        $this->membro = $sepultamento->membro;
        $this->data_falecimento = $sepultamento->data_falecimento ? $sepultamento->data_falecimento->format('d/m/Y') : null;
        $this->data_sepultamento = $sepultamento->data_sepultamento ? $sepultamento->data_sepultamento->format('d/m/Y') : null;
        $this->quadra = $sepultamento->quadra;
        $this->fila = $sepultamento->fila;
        $this->cova = $sepultamento->cova;
        $this->certidao_obito_path = $sepultamento->certidao_obito_path;
        $this->observacoes = $sepultamento->observacoes;
        $this->ativo = $sepultamento->ativo;
        $this->causasSelecionadas = $sepultamento->causas->pluck('id')->toArray();
    }

    // Helpers internos
    private function formData(int $empresaId, int $userId): array
    {
        return [
            'empresa_id' => $empresaId,
            'user_id' => $userId,
            'nome_falecido' => $this->nome_falecido,
            'mae' => $this->mae,
            'pai' => $this->pai,
            'indigente' => $this->indigente,
            'natimorto' => $this->natimorto,
            'translado' => $this->translado,
            'membro' => $this->membro,
            'data_falecimento' => $this->data_falecimento,
            'data_sepultamento' => $this->data_sepultamento,
            'quadra' => $this->quadra,
            'fila' => $this->fila,
            'cova' => $this->cova,
            'observacoes' => $this->observacoes,
            'ativo' => $this->ativo,
        ];
    }

    private function handleValidationException(ValidationException $e, string $context): void
    {
        $this->logAcao("{$context}.validation_failed", $this->sepultamentoId, null, [
            'errors' => $e->validator->errors()->toArray(),
        ]);
        $lista = collect($e->validator->errors()->all())
            ->map(fn ($m) => "<li>{$m}</li>")
            ->implode('');
        $this->dispatch('swal', type: 'error', title: 'Erros de validação',
            html: "<ul class='list-disc pl-5 text-left'>{$lista}</ul>");
    }

    private function handleThrowable(\Throwable $e, string $context, ?int $id = null): void
    {
        $this->logAcao("{$context}.error", $id, null, ['message' => $e->getMessage()]);
        report($e);
        $this->dispatch('swal', type: 'error', title: 'Falha na operação',
            text: app()->isLocal() ? $e->getMessage() : 'Erro inesperado.');
    }
}