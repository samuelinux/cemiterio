# Sistema de Gestão de Cemitério

Sistema web desenvolvido em Laravel 11 para gestão completa de sepultamentos por múltiplas empresas.

## 🚀 Início Rápido

### Instalação
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
```

## Em WSL
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Entrar em Container DOCKER
```bash
docker exec -it sistema_de_agendamento-app bash
```

### Erro de Banco de Dados
```bash
php artisan migrate:fresh --seed
```

### Erro de Cache
```bash
php artisan config:clear && php artisan cache:clear && php artisan view:clear && php artisan route:clear
```

### Parar de pedir senha ssh
```bash
eval "$(ssh-agent -s)" && ssh-add ~/.ssh/id_ed25519
echo 'eval "$(ssh-agent -s)" > /dev/null && ssh-add -q ~/.ssh/id_ed25519 2>/dev/null || true' | tee -a ~/.zshrc ~/.bashrc
```

### Resetar mudanças Locais
```bash
git reset --hard origin/$(git rev-parse --abbrev-ref HEAD) && git clean -fd && git pull

```

### Faça backup das mudanças
```bash
git add . && git commit -m "Pequenas mudanças" && git push

```
### Acesso
- **Admin**: http://localhost/admin/login
- **Empresa**: http://localhost/{slug-empresa}/login

## 📋 Credenciais de Teste

### Administrador
- **Email**: admin@cemiterio.com
- **Senha**: admin123

### Empresa São João
- **Gestor**: gestor@cemiterio-sao-joao.com (gestor123)
- **Funcionário**: funcionario@cemiterio-sao-joao.com (func123)
- **Consultor**: consultor@cemiterio-sao-joao.com (consultor123)

## 🛠️ Tecnologias

- **Laravel 11** - Framework PHP
- **Livewire 3** - Componentes interativos
- **Tailwind CSS** - Framework CSS responsivo
- **Alpine.js** - Interações do cliente
- **MySQL** - Base de dados
- **Livewire Alert** - Notificações

## 📱 Funcionalidades

### Admin
- ✅ Gestão de empresas
- ✅ Gestão de utilizadores
- ✅ Sistema de permissões
- ✅ Dashboard com estatísticas

### Empresa
- ✅ Cadastro de sepultamentos
- ✅ Pesquisa e filtros avançados
- ✅ Dashboard específico
- ✅ Controlo de permissões

## 🔒 Segurança

- Autenticação multi-tenant
- Sistema de permissões granular
- Middleware de proteção
- Validação completa de dados

## 📖 Documentação

Consulte `documentacao-projeto-cemiterio.md` para documentação completa.

## 🏗️ Estrutura

```
projeto-cemiterio/
├── app/
│   ├── Http/Controllers/
│   ├── Livewire/
│   ├── Models/
│   └── Traits/
├── database/
│   ├── migrations/
│   └── seeders/
└── resources/views/
    ├── layouts/
    ├── auth/
    └── livewire/
```

---

**Desenvolvido com ❤️ usando Laravel 11**
