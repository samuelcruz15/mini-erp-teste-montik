# Mini ERP - Sistema de E-commerce

Sistema completo de gestão de produtos, pedidos e estoque desenvolvido em **Laravel 12** com interface moderna e **API REST com OAuth2**.

## 🎬 Demonstração Visual

### **👤 Interface do Cliente**
![Interface do Cliente](public/cliente.gif)
*Navegação, carrinho, checkout e histórico de pedidos*

### **⚙️ Painel Administrativo**
![Painel Administrativo](public/admin.gif)
*Gestão de produtos, estoque, pedidos e cupons*

### **🔗 Webhook API**
![Webhook API](public/webhook.gif)
*Integração externa via API REST com OAuth2*

---

## 🚀 Funcionalidades Principais

### ✅ **Gestão de Produtos**
- CRUD completo com URLs de imagem
- Sistema de variações (tamanhos, cores)
- Controle individual de estoque por variação
- Edição inline de estoque (admin)
- Busca e filtros

### ✅ **Sistema de Carrinho**
- Carrinho em sessão com controle em tempo real
- Visualização para usuários não logados
- Login obrigatório apenas no checkout
- Verificação de estoque automática
- Aplicação de cupons de desconto

### ✅ **Processamento de Pedidos**
- Checkout protegido (apenas usuários logados)
- Cálculo automático de frete
- Geração automática de número do pedido
- Email de confirmação automático
- Histórico de pedidos para clientes
- Gestão de status para administradores

### ✅ **API REST com OAuth2**
- Autenticação via Laravel Passport
- Webhooks seguros para atualização de status
- Endpoints para integração externa

### ✅ **Integrações**
- **ViaCEP** para consulta de endereços
- **Sistema de cupons** com regras complexas
- **Cálculo de frete** automático
- **Gestão de endereços** para clientes

## 🛠️ Tecnologias

- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: Bootstrap 5.3, JavaScript ES6
- **Banco**: MySQL
- **Autenticação**: OAuth2 (Passport)
- **APIs**: ViaCEP

## 🚀 Instalação Rápida

```bash
# Clone e configure
git clone <url-do-repositorio>
cd erp
composer install
cp .env.example .env
php artisan key:generate

# Configure banco de dados no .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mini_erp
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

# Execute migrações e seeders
php artisan migrate
php artisan db:seed
php artisan storage:link

# Configure OAuth2
php artisan passport:install

# Inicie o servidor
php artisan serve
```

## 👥 Usuários de Teste

| Tipo | Email | Senha |
|------|-------|-------|
| **Admin** | `admin@minierp.com` | `admin123` |
| **Cliente** | `cliente@exemplo.com` | `cliente123` |

## 🌐 API REST

### **Autenticação:**
```bash
# Login
POST /api/auth/login
{
  "email": "admin@minierp.com",
  "password": "admin123"
}

# Usar token
Authorization: Bearer {token}
```

### **Endpoints Principais:**
- `POST /api/auth/login` - Login
- `POST /api/webhook/order-status` - Atualizar status
- `GET /api/webhook/orders` - Listar pedidos

## 🏗️ Arquitetura

- **Controllers**: Recebem requests e retornam responses
- **Services**: Lógica de negócio centralizada
- **Models**: Relacionamentos e query scopes
- **Form Requests**: Validação centralizada
- **OAuth2**: Autenticação segura para APIs

## 🔒 Segurança

- Middleware de autenticação e autorização
- Validação CSRF
- OAuth2 para APIs
- Sanitização de dados
- Controle de acesso por roles (admin/user)

---

### 🎉 Sistema 100% Funcional!

**Web**: Interface responsiva com Bootstrap  
**API**: OAuth2 para integrações externas
