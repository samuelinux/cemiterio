<?php

namespace App\Livewire\Admin;

use App\Models\Empresa;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class EmpresaFormEdit extends Component
{
    public int $empresaId;

    public string $nome = '';
    public string $slug = '';
    public ?string $email = '';
    public ?string $telefone = '';
    public ?string $endereco = '';
    public ?string $cidade = '';
    public ?string $estado = '';
    public ?string $cep = '';
    public ?string $cnpj = '';
    public bool $ativo = true;

    public function mount(int $empresaId)
    {
        $this->empresaId = $empresaId;
        $empresa = Empresa::findOrFail($empresaId);

        $this->nome = (string) $empresa->nome;
        $this->slug = (string) $empresa->slug;
        $this->email = (string) $empresa->email;
        $this->telefone = (string) $empresa->telefone;
        $this->endereco = (string) $empresa->endereco;
        $this->cidade = (string) $empresa->cidade;
        $this->estado = (string) $empresa->estado;
        $this->cep = (string) $empresa->cep;
        $this->cnpj = (string) $empresa->cnpj;
        $this->ativo = (bool) $empresa->ativo;
    }

    protected function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('empresas', 'slug')->ignore($this->empresaId)],
            'email' => ['nullable', 'email', 'max:255'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'endereco' => ['nullable', 'string'],
            'cidade' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'string', 'max:2'],
            'cep' => ['nullable', 'string', 'max:10'],
            'cnpj' => ['nullable', 'string', 'max:18'],
            'ativo' => ['boolean'],
        ];
    }

    protected array $messages = [
        'nome.required' => 'O nome da empresa é obrigatório.',
        'email.email' => 'O email deve ter um formato válido.',
        'estado.max' => 'O estado deve ter no máximo 2 caracteres.',
        'slug.required' => 'O slug é obrigatório.',
        'slug.unique' => 'Este slug já está em uso.',
    ];

    /** Gera o slug a partir do nome, mas não salva ainda */
    public function suggestSlug(): void
    {
        $base = filled($this->nome) ? $this->nome : $this->slug;
        $this->slug = Str::slug((string) $base);
    }

    public function save(): void
    {
        try {
            // Normaliza slug digitado / ou gera do nome se estiver vazio
            if (blank($this->slug) && filled($this->nome)) {
                $this->slug = Str::slug($this->nome);
            } else {
                $this->slug = Str::slug($this->slug);
            }

            $this->validate();

            // Garante unicidade do slug considerando o próprio registro
            $slugFinal = Empresa::generateUniqueSlug($this->slug, $this->empresaId);

            $empresa = Empresa::findOrFail($this->empresaId);

            $empresa->update([
                'nome' => $this->nome,
                'slug' => $slugFinal,
                'email' => $this->email,
                'telefone' => $this->telefone,
                'endereco' => $this->endereco,
                'cidade' => $this->cidade,
                'estado' => $this->estado,
                'cep' => $this->cep,
                'cnpj' => $this->cnpj,
                'ativo' => $this->ativo, // ativação/inativação
            ]);

            $this->dispatch('toast', type: 'success', title: 'Empresa atualizada!');
        } catch (ValidationException $e) {
            $lista = collect($e->validator->errors()->all())
                ->map(fn ($m) => "<li>{$m}</li>")
                ->implode('');
            $this->dispatch('swal', type: 'error', title: 'Erros de validação', html: "<ul class='list-disc pl-5 text-left'>{$lista}</ul>");
            throw $e;
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('swal',
                type: 'error',
                title: 'Falha ao salvar',
                text: app()->isLocal() ? $e->getMessage() : 'Ocorreu um erro inesperado.'
            );
        }
    }

    public function render()
    {
        return view('livewire.admin.empresa-form-edit');
    }
}
