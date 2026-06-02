# Notas de Multitenancy

Este documento registra as recomendações geradas para preparar o `myStockMaster` como um produto comercial multi-tenant.

## O que já existe

- `app/Models/Tenant.php` com `Tenant::current()` e `Tenant::setCurrent()`.
- `app/Traits/BelongsToTenant.php` aplica um global scope em `tenant_id` e popula `tenant_id` durante a criação.
- `app/Http/Middleware/SetTenant.php` define o tenant atual a partir de `auth()->user()->tenant`.
- Muitas models já usam `BelongsToTenant` (por exemplo: `Customer`, `Supplier`, `Sale`, `Purchase`, `Product`, `Warehouse`).
- Há migração para adicionar `tenant_id` nas tabelas principais: `database/migrations/2026_05_25_000001_add_tenant_id_to_core_tables.php`.

## O que precisa melhorar

1. Garantir isolamento completo de dados
   - Verificar todas as tabelas de dados de cliente e garantir que tenham `tenant_id`.
   - Tabelas novas ou de anexos, especialmente `arquivos` e `arquivaveis`, devem ter `tenant_id` se forem dados de tenant.
   - Sem esse campo, há risco de vazamento de dados entre clientes.

2. Fixar o tenant em todos os contextos
   - Middleware `SetTenant` cobre requests web autenticados.
   - Também é necessário definir o tenant para:
     - jobs/queues
     - comandos Artisan
     - APIs tokenizadas
     - integrações externas

3. Evitar registros `tenant_id` nulos onde não faz sentido
   - O trait atual permite consultas `whereNull(tenant_id)` quando não há tenant definido.
   - Isso é aceitável apenas para dados globais de sistema.
   - Para dados específicos de cliente, prefira `tenant_id` obrigatório.

4. Melhorar autenticação/cadastro multi-tenant
   - Onboarding de tenant separado
   - Cadastro de conta + tenant linkado automaticamente
   - Evitar atribuir tenant manualmente no backend sempre que possível

5. Segurança de acesso
   - Separar super-admin do sistema de admin do tenant.
   - Usar roles/permissions por tenant.
   - Validar sempre que o usuário pertença ao tenant dos dados acessados.

6. Isolamento de arquivos e storage
   - Guardar arquivos em paths ou discos por tenant, por exemplo: `tenant/{tenant_id}/...`.
   - Evitar usar apenas `disk: public` sem subpasta de tenant.
   - Isso reduz risco de arquivos de um cliente serem expostos a outro.

7. Recursos importantes para SaaS
   - Billing/subscription
   - Limites por tenant (usuários, anexos, documentos, transações)
   - Configurações por tenant (tema, logo, parâmetros fiscais)
   - Auditoria e logs por tenant

## Recomendações concretas imediatas

- Adicionar `tenant_id` em `arquivos` e `arquivaveis`.
- Aplicar `BelongsToTenant` em `App\Models\Arquivo` e `App\Models\Arquivavel` se esses modelos forem dados de tenant.
- Revisar consultas manuais que possam contornar o escopo global de tenant.
- Criar um resolvedor de tenant para API e jobs.
- Revisar onboarding de tenant e a criação de usuários para evitar vazamento de dados.

## Próximos passos sugeridos

- Encontrar tabelas/models que ainda não usam `BelongsToTenant`.
- Criar migration `tenant_id` para `arquivos`/`arquivaveis`.
- Reforçar middleware/resolver de tenant para jobs e API.
