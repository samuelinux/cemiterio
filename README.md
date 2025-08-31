# Sistema de Gestão de Cemitério

Sistema web desenvolvido em Laravel 11 para gestão completa de sepultamentos por múltiplas empresas.

## 🚀 Início Rápido

### Instalação
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

### Acesso
- **Admin**: http://localhost:8000/admin/login
- **Empresa**: http://localhost:8000/{slug-empresa}/login

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
