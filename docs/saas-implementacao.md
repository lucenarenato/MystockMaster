# MystockMaster — Implementação SaaS Multi-Tenant

Documento de rastreamento das decisões e do progresso de transformação do MystockMaster em um produto comercial multi-tenant.

---

## O que foi pedido

A partir do arquivo `multitenancy-notas.md`, o objetivo era preparar o sistema para funcionar como SaaS com múltiplos clientes isolados. Os requisitos principais eram:

1. **Isolamento completo de dados** — cada cliente só vê seus próprios registros
2. **Tenant resolvido em todos os contextos** — web, API, jobs, queues
3. **Sem registros expostos por tenant nulo** — falha segura quando não há tenant
4. **Onboarding automático** — cadastro cria Tenant + User + Settings sem intervenção manual
5. **Segurança de acesso** — separar super-admin do sistema de admin do tenant
6. **Isolamento de storage** — arquivos organizados por tenant
7. **Recursos SaaS** — limites por plano, configurações por tenant, auditoria, billing

---

## O que foi implementado

### Isolamento de dados

| O quê | Arquivo |
|---|---|
| `BelongsToTenant` trait com global scope automático | `app/Traits/BelongsToTenant.php` |
| Fail-closed: `whereRaw('0=1')` quando sem tenant fora do console | `BelongsToTenant.php` |
| `tenant_id` em tabelas principais | `migrations/2026_05_25_000001` |
| `tenant_id` em tabelas restantes (arquivos, adjustments, settings, etc.) | `migrations/2026_06_03_000001` |
| `BelongsToTenant` em 22 models | Customer, Supplier, Sale, Purchase, Product, Warehouse, Expense, CashRegister, Integration, Quotation, CustomerGroup, Brand, Category, Arquivo, Arquivavel, Upload, Adjustment, Setting, Wallet, Printer, Transfer, Movement |

### Tenant em todos os contextos

| Contexto | Solução |
|---|---|
| Web (requests autenticados) | `SetTenant` middleware no grupo `auth` |
| API (Sanctum) | `auth:sanctum` + `setTenant` + `ensure.same.tenant` em `routes/api.php` |
| Jobs / queues | `TenantAware` trait + `SetTenantForJob` job middleware |
| Storage (path de arquivos) | `Arquivo::getCaminho()` inclui `tenant/{id}/` no path |

### Segurança de acesso

| O quê | Arquivo |
|---|---|
| `is_system_admin` na tabela `users` | `migrations/2026_06_03_000002` |
| `Gate::before` apenas para `is_system_admin` (não para todo "Super Admin") | `app/Providers/AuthServiceProvider.php` |
| `EnsureSameTenant` middleware — valida ownership de route model bindings | `app/Http/Middleware/EnsureSameTenant.php` |
| `admin@gmail.com` marcado como `is_system_admin = true` | `SuperUserSeeder.php` |

### Onboarding multi-tenant

| O quê | Arquivo |
|---|---|
| `ProvisionTenant` action — cria Tenant + configura user + cria Settings padrão | `app/Actions/ProvisionTenant.php` |
| Campo `company` nos formulários de registro | `views/auth/register.blade.php`, `views/livewire/auth/register.blade.php` |
| Registro web, Livewire e API chamam `ProvisionTenant` em transaction | `RegisteredUserController`, `Livewire/Auth/Register`, `Api/AuthController` |

### Limites por tenant

| O quê | Detalhe |
|---|---|
| Campos `max_*` em `tenants` | max_users, max_products, max_sales, max_purchases, max_storage_mb, max_customers, max_suppliers |
| `TenantLimits` service | `app/Services/TenantLimits.php` — `check('resource')` lança 402 ao atingir o limite |
| Wired em 9 pontos de criação | Products, Sales, Purchase, Users, Customers, Suppliers — Livewire + API |
| Check de storage (MB) | soma `arquivos.size` por tenant antes de salvar anexos |

### Auditoria por tenant

| O quê | Arquivo |
|---|---|
| Tabela `audit_logs` com tenant_id, user_id, event, old/new values, IP, user agent | `migrations/2026_06_03_000004` |
| `AuditLog` model com `BelongsToTenant` | `app/Models/AuditLog.php` |
| `RecordsActivity` trait — grava created/updated/deleted automaticamente | `app/Traits/RecordsActivity.php` |
| Aplicado em | Sale, Purchase, Product, Customer, Supplier |

### Billing — planos fixos com Stripe

| O quê | Arquivo |
|---|---|
| `Billable` no `Tenant` + `stripeEmail()` + `applyPlanLimits()` | `app/Models/Tenant.php` |
| Migrations do Cashier (subscriptions, subscription_items) | `migrations/2026_06_03_000007/008` |
| Campos Cashier em `tenants` (stripe_id, pm_type, etc.) | `migrations/2026_06_03_000006` |
| Definição dos 3 planos com limites | `config/plans.php` |
| `SubscriptionController` — subscribe, switchPlan, cancel, portal, plans | `app/Http/Controllers/SubscriptionController.php` |
| `EnsureActiveSubscription` middleware — bloqueia sem assinatura ativa | `app/Http/Middleware/EnsureActiveSubscription.php` |
| `StripeWebhookController` — atualiza limites ao trocar plano via portal Stripe | `app/Http/Controllers/StripeWebhookController.php` |
| Rotas `/billing/*` e `POST /stripe/webhook` | `routes/web.php` |
| Variáveis de ambiente Stripe | `.env` / `.env.example` |

---

## Planos configurados

| Plano | Usuários | Produtos | Vendas | Compras | Clientes | Fornecedores | Storage |
|---|---|---|---|---|---|---|---|
| **Básico** | 3 | 100 | 300 | 300 | 100 | 50 | 512 MB |
| **Pro** | 10 | 500 | 2.000 | 2.000 | 500 | 200 | 2 GB |
| **Enterprise** | ilimitado | ilimitado | ilimitado | ilimitado | ilimitado | ilimitado | ilimitado |

`NULL` em qualquer campo de limite = ilimitado.

---

## O que falta

### Para colocar em produção (obrigatório)

- [ ] **Configurar Stripe** — criar produtos/preços no painel Stripe, preencher `STRIPE_PRICE_*` no `.env`
- [ ] **Configurar webhook** no painel Stripe apontando para `POST /stripe/webhook`
- [ ] **Preencher chaves** `STRIPE_KEY`, `STRIPE_SECRET`, `STRIPE_WEBHOOK_SECRET` no `.env`
- [ ] **Rodar migrations** — `php artisan migrate`

### Funcionalidades não implementadas

- [ ] **Tela de planos e checkout** — frontend para o usuário escolher o plano e inserir cartão (`/billing/plans`). Hoje só existe a lógica de backend.
- [ ] **Trial period** — período de teste gratuito ao criar conta (ex: 14 dias). Cashier suporta com `->trialDays(14)` no `newSubscription()`.
- [ ] **Integrações externas sem tenant** — webhooks do WooCommerce e YouCan chegam sem contexto de tenant. Precisa de uma rota por tenant (ex: `/webhook/{tenant_slug}/woocommerce`) para resolver o tenant antes de despachar o job.
- [ ] **Configurações de tema/logo por tenant** — o campo `company_logo` em `settings` existe mas não há interface para upload por tenant.
- [ ] **Dashboard de uso** — exibir ao admin do tenant quantos recursos estão sendo usados vs o limite do plano.
- [ ] **Notificação de limite próximo** — alertar quando o tenant atingir 80%+ de um limite.
- [ ] **`RecordsActivity` em mais models** — atualmente só em Sale, Purchase, Product, Customer, Supplier. Candidatos: Expense, Adjustment, Transfer, Warehouse.
- [ ] **Painel de auditoria** — interface para o admin do tenant visualizar o `audit_logs`.

### Decisões de produto pendentes

- [ ] **Preços dos planos** — os valores em R$ não foram definidos.
- [ ] **Política de cancelamento** — o que acontece com os dados após cancelamento? Período de carência?
- [ ] **Upgrade/downgrade de plano** — se o tenant tem mais produtos que o plano menor permite, o downgrade deve ser bloqueado ou os produtos removidos?

---

## Como ativar um limite em um tenant

```sql
-- Ativa limite específico
UPDATE tenants SET max_products = 100 WHERE id = 1;

-- Remove limite (ilimitado)
UPDATE tenants SET max_products = NULL WHERE id = 1;
```

Ou via `applyPlanLimits()` ao criar/trocar assinatura:

```php
$tenant->applyPlanLimits('basic'); // seta todos os max_* do plano Básico
```

---

## Como adicionar um novo model com isolamento de tenant

```php
// 1. Migration
$table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->cascadeOnDelete();

// 2. Model
use App\Traits\BelongsToTenant;
use App\Traits\RecordsActivity; // se quiser auditoria

class MinhaEntidade extends Model
{
    use BelongsToTenant;
    use RecordsActivity;
}
```

---

## Como adicionar um novo job com contexto de tenant

```php
use App\Traits\TenantAware;

class MeuJob implements ShouldQueue
{
    use TenantAware;

    public function __construct(...)
    {
        // ...
        $this->captureCurrentTenant(); // sempre no final do __construct
    }
}
```
