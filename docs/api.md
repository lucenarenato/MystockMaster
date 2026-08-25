# 📦 API REST — Sistema de Gestão de Estoque

Documentação completa da API para o aplicativo de automação de estoque.

---

## 🔐 Autenticação

### Registrar novo usuário
```http
POST /api/register
Content-Type: application/json

{
  "company": "Minha Empresa LTDA",
  "name": "João Silva",
  "email": "joao@email.com",
  "password": "12345678"
}
```

**Resposta 201:**
```json
{
  "access_token": "1|abc123...",
  "token_type": "Bearer"
}
```

### Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "joao@email.com",
  "password": "12345678"
}
```

**Resposta 200:**
```json
{
  "access_token": "1|abc123...",
  "token_type": "Bearer"
}
```

### Token de acesso (Sanctum)
```http
POST /api/sanctum/token
Content-Type: application/json

{
  "email": "joao@email.com",
  "password": "12345678",
  "device_name": "mobile_app"
}
```

---

## 📊 Dashboard

### Resumo geral
```http
GET /api/dashboard
GET /api/dashboard?warehouse_id=1
```

**Resposta 200:**
```json
{
  "summary": {
    "total_products": 150,
    "total_stock_quantity": 5420,
    "total_stock_value": 125000.00,
    "sales_this_month": 45000.00,
    "purchases_this_month": 32000.00,
    "pending_sales_payment": 8500.00,
    "pending_purchases_payment": 12000.00,
    "total_customers": 85,
    "total_suppliers": 30,
    "total_warehouses": 3
  },
  "low_stock_products": [...],
  "recent_sales": [...],
  "recent_purchases": [...]
}
```

### Níveis de estoque
```http
GET /api/dashboard/stock
GET /api/dashboard/stock?warehouse_id=1
GET /api/dashboard/stock?low_stock=1
GET /api/dashboard/stock?search=produto
```

---

## 🔍 Busca de Produtos / Barcode

### Buscar produto
```http
GET /api/products/search?q=arroz
GET /api/products/search?category_id=1
GET /api/products/search?brand_id=2
```

### Leitura de código de barras
```http
GET /api/products/barcode?code=7891234567890
```

**Resposta 200:**
```json
{
  "product": {
    "id": 1,
    "name": "Arroz Tipo 1",
    "code": "7891234567890",
    "price": 25.90,
    "cost": 18.50,
    "quantity": 100,
    "unit": "kg",
    "stock_alert": 20
  },
  "total_stock": 250,
  "avg_price": 25.90,
  "avg_cost": 18.50
}
```

### Detalhes do produto
```http
GET /api/products/1/details
```

---

## 📦 Produtos (CRUD)

```http
GET    /api/products              # Listar produtos
POST   /api/products              # Criar produto
GET    /api/products/{id}         # Detalhes do produto
PUT    /api/products/{id}         # Atualizar produto
DELETE /api/products/{id}         # Excluir produto
```

### Criar produto
```json
POST /api/products
{
  "name": "Arroz Tipo 1",
  "code": "7891234567890",
  "category_id": 1,
  "brand_id": 1,
  "cost": 18.50,
  "price": 25.90,
  "quantity": 100,
  "unit": "kg",
  "stock_alert": 20,
  "status": 1
}
```

---

## 🏷️ Categorias

```http
GET    /api/categories            # Listar categorias
POST   /api/categories            # Criar categoria
GET    /api/categories/{id}       # Detalhes
PUT    /api/categories/{id}       # Atualizar
DELETE /api/categories/{id}       # Excluir
```

---

## 🏭 Marcas

```http
GET    /api/brands                # Listar marcas
POST   /api/brands                # Criar marca
GET    /api/brands/{id}           # Detalhes
PUT    /api/brands/{id}           # Atualizar
DELETE /api/brands/{id}           # Excluir
```

### Criar marca
```json
POST /api/brands
{
  "name": "Marca ABC",
  "description": "Descrição da marca"
}
```

---

## 👥 Clientes

```http
GET    /api/customers             # Listar clientes
POST   /api/customers             # Criar cliente
GET    /api/customers/{id}        # Detalhes
PUT    /api/customers/{id}        # Atualizar
DELETE /api/customers/{id}        # Excluir
```

---

## 🏢 Fornecedores

```http
GET    /api/suppliers             # Listar fornecedores
POST   /api/suppliers             # Criar fornecedor
GET    /api/suppliers/{id}        # Detalhes
PUT    /api/suppliers/{id}        # Atualizar
DELETE /api/suppliers/{id}        # Excluir
```

---

## 🏬 Armazéns

```http
GET    /api/warehouses            # Listar armazéns
POST   /api/warehouses            # Criar armazém
GET    /api/warehouses/{id}       # Detalhes
PUT    /api/warehouses/{id}       # Atualizar
DELETE /api/warehouses/{id}       # Excluir
```

---

## 💰 Vendas

```http
GET    /api/sales                 # Listar vendas
POST   /api/sales                 # Criar venda
GET    /api/sales/{id}            # Detalhes da venda
PUT    /api/sales/{id}            # Atualizar venda
DELETE /api/sales/{id}            # Excluir venda (restaura estoque)
```

### Criar venda
```json
POST /api/sales
{
  "customer_id": 1,
  "warehouse_id": 1,
  "date": "2026-08-25",
  "tax_percentage": 0,
  "discount_percentage": 0,
  "shipping_amount": 0,
  "paid_amount": 51.80,
  "status": 2,
  "payment_method": "cash",
  "note": "Vendabalcão",
  "items": [
    {
      "product_id": 1,
      "quantity": 2,
      "price": 25.90,
      "discount": 0,
      "tax": 0
    }
  ]
}
```

### Listar vendas com filtros
```http
GET /api/sales?status=0
GET /api/sales?payment_status=1
GET /api/sales?customer_id=1
GET /api/sales?warehouse_id=1
GET /api/sales?date_from=2026-08-01&date_to=2026-08-31
GET /api/sales?search=PV
GET /api/sales?per_page=25&sort=created_at&order=desc
```

---

## 🛒 Compras

```http
GET    /api/purchases             # Listar compras
POST   /api/purchases             # Criar compra
GET    /api/purchases/{id}        # Detalhes
PUT    /api/purchases/{id}        # Atualizar
DELETE /api/purchases/{id}        # Excluir (restaura estoque)
```

### Criar compra
```json
POST /api/purchases
{
  "supplier_id": 1,
  "warehouse_id": 1,
  "date": "2026-08-25",
  "tax_percentage": 0,
  "discount_percentage": 0,
  "shipping_amount": 0,
  "paid_amount": 0,
  "status": 0,
  "payment_method": "cash",
  "note": "Compra mensal",
  "items": [
    {
      "product_id": 1,
      "quantity": 50,
      "price": 25.90,
      "cost": 18.50,
      "discount": 0,
      "tax": 0
    }
  ]
}
```

---

## 📋 Ajustes de Estoque (Balanço)

```http
GET    /api/adjustments           # Listar ajustes
POST   /api/adjustments           # Criar ajuste
GET    /api/adjustments/{id}      # Detalhes
PUT    /api/adjustments/{id}      # Atualizar
DELETE /api/adjustments/{id}      # Excluir (reverte mudanças)
```

### Criar ajuste de estoque (balanço)
```json
POST /api/adjustments
{
  "date": "2026-08-25",
  "note": "Balanço mensal - Agosto 2026",
  "warehouse_id": 1,
  "items": [
    {
      "product_id": 1,
      "quantity": 5,
      "type": "add"
    },
    {
      "product_id": 2,
      "quantity": 3,
      "type": "sub"
    }
  ]
}
```

**Tipo:**
- `"add"` — Adicionar ao estoque
- `"sub"` — Subtrair do estoque

---

## 🔄 Transferências

```http
GET    /api/transfers             # Listar transferências
POST   /api/transfers             # Criar transferência
GET    /api/transfers/{id}        # Detalhes
PUT    /api/transfers/{id}        # Atualizar
DELETE /api/transfers/{id}        # Excluir
```

### Criar transferência
```json
POST /api/transfers
{
  "from_warehouse_id": 1,
  "to_warehouse_id": 2,
  "item": "Arroz Tipo 1",
  "total_qty": 20,
  "total_tax": 0,
  "total_cost": 370.00,
  "total_amount": 518.00,
  "shipping": 15.00,
  "status": "pending",
  "note": "Transferência para filial"
}
```

---

## ↩️ Devoluções de Venda

```http
GET    /api/sale-returns          # Listar devoluções de venda
POST   /api/sale-returns          # Criar devolução
GET    /api/sale-returns/{id}     # Detalhes
PUT    /api/sale-returns/{id}     # Atualizar
DELETE /api/sale-returns/{id}     # Excluir (reverte estoque)
```

### Criar devolução de venda
```json
POST /api/sale-returns
{
  "customer_id": 1,
  "warehouse_id": 1,
  "date": "2026-08-25",
  "total_amount": 25.90,
  "paid_amount": 25.90,
  "payment_method": "cash",
  "note": "Produto com defeito",
  "items": [
    {
      "product_id": 1,
      "quantity": 1,
      "price": 25.90
    }
  ]
}
```

---

## ↩️ Devoluções de Compra

```http
GET    /api/purchase-returns          # Listar devoluções de compra
POST   /api/purchase-returns          # Criar devolução
GET    /api/purchase-returns/{id}     # Detalhes
PUT    /api/purchase-returns/{id}     # Atualizar
DELETE /api/purchase-returns/{id}     # Excluir (reverte estoque)
```

### Criar devolução de compra
```json
POST /api/purchase-returns
{
  "supplier_id": 1,
  "warehouse_id": 1,
  "date": "2026-08-25",
  "total_amount": 370.00,
  "paid_amount": 0,
  "payment_method": "cash",
  "note": "Produto danificado",
  "items": [
    {
      "product_id": 1,
      "quantity": 20,
      "price": 18.50
    }
  ]
}
```

---

## 💳 Despesas

```http
GET    /api/expenses              # Listar despesas
POST   /api/expenses              # Criar despesa
GET    /api/expenses/{id}         # Detalhes
PUT    /api/expenses/{id}         # Atualizar
DELETE /api/expenses/{id}         # Excluir
```

---

## 👤 Usuários / Roles

```http
GET    /api/roles                 # Listar roles
POST   /api/roles                 # Criar role
GET    /api/roles/{id}            # Detalhes
PUT    /api/roles/{id}            # Atualizar
DELETE /api/roles/{id}            # Excluir
```

---

## 📱 Exemplos de Consumo (cURL)

### Login + Listar produtos
```bash
# 1. Fazer login
TOKEN=$(curl -s -X POST https://seudominio.com/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@email.com","password":"12345678"}' \
  | jq -r '.access_token')

# 2. Listar produtos
curl -s https://seudominio.com/api/products \
  -H "Authorization: Bearer $TOKEN" | jq .
```

### Ler código de barras
```bash
curl -s "https://seudominio.com/api/products/barcode?code=7891234567890" \
  -H "Authorization: Bearer $TOKEN" | jq .
```

### Criar venda
```bash
curl -s -X POST https://seudominio.com/api/sales \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "customer_id": 1,
    "warehouse_id": 1,
    "date": "2026-08-25",
    "paid_amount": 51.80,
    "status": 2,
    "items": [{"product_id": 1, "quantity": 2, "price": 25.90}]
  }' | jq .
```

### Criar ajuste de estoque (balanço)
```bash
curl -s -X POST https://seudominio.com/api/adjustments \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "date": "2026-08-25",
    "warehouse_id": 1,
    "items": [
      {"product_id": 1, "quantity": 5, "type": "add"},
      {"product_id": 2, "quantity": 3, "type": "sub"}
    ]
  }' | jq .
```

---

## 📋 Parâmetros de Filtros (Index)

Todos os endpoints de listagem aceitam:

| Parâmetro     | Tipo    | Descrição                          |
|---------------|---------|------------------------------------|
| `per_page`    | int     | Itens por página (padrão: 15)      |
| `sort`        | string  | Campo para ordenação               |
| `order`       | string  | `asc` ou `desc` (padrão: desc)     |
| `search`      | string  | Busca por referência/nome          |
| `date_from`   | date    | Data inicial (YYYY-MM-DD)          |
| `date_to`     | date    | Data final (YYYY-MM-DD)            |
| `status`      | int     | Filtrar por status                 |

---

## ⚠️ Códigos de Erro

| Código | Descrição                        |
|--------|----------------------------------|
| 200    | Sucesso                          |
| 201    | Criado com sucesso               |
| 401    | Não autenticado                  |
| 403    | Acesso negado                    |
| 404    | Recurso não encontrado           |
| 422    | Erro de validação                |
| 500    | Erro interno do servidor         |

---

## 💡 Valores Monetários

**Importante:** Todos os valores monetários são armazenados no banco em **centavos** (×100) e retornados da API já **divididos por 100**.

- Exemplo: R$ 25,90 → armazenado como `2590` → retornado como `25.90`
