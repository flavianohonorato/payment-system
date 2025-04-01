# Sistema de Pagamentos

Sistema de pagamentos desenvolvido com Laravel (Backend) e Vue.js + Quasar Framework (Frontend), utilizando Docker para containerização.

## Tecnologias Utilizadas

### Backend
- Laravel (PHP Framework)
- MySQL 8.0
- Docker
- PHP 8.2+

### Frontend
- Vue.js 3
- Quasar Framework
- Vite
- TailwindCSS

## Pré-requisitos

- Docker e Docker Compose
- Node.js 20+ (para desenvolvimento local)
- PHP 8.2+ (para desenvolvimento local)
- Composer (para desenvolvimento local)

## Configuração do Ambiente

1. Clone o repositório:
```bash
git clone https://github.com/flavianohonorato/payment-system
cd payment-system
```

2. Copie o arquivo de ambiente:
```bash
cp .env.example .env
```

3. Configure as variáveis de ambiente no arquivo `.env`:
```env
DB_DATABASE=payment_system
DB_USERNAME=root
DB_PASSWORD=root
DB_ROOT_PASSWORD=root
DATABASE_PORT=3306
BACKEND_PORT=9000
WEBSERVER_PORT=8000
FRONTEND_PORT=8080
API_URL=http://localhost:8000/api
CORS_ALLOWED_ORIGIN=http://localhost:8080
```

4. Inicie os containers Docker:
```bash
docker-compose up -d
```

5. Instale as dependências do backend:
```bash
docker-compose exec backend composer install
```

6. Gere a chave da aplicação:
```bash
docker-compose exec backend php artisan key:generate
```

7. Execute as migrações do banco de dados:
```bash
docker-compose exec backend php artisan migrate
```

8. Instale as dependências do frontend:
```bash
docker-compose exec frontend npm install
```

## Executando o Projeto

O projeto estará disponível nos seguintes endereços:

- Frontend: http://localhost:8080
- Backend API: http://localhost:8000
- MySQL: localhost:3306

## Estrutura do Projeto

```
payment-system/
├── backend/            # API Laravel
├── frontend/           # Aplicação Vue.js + Quasar
├── docker/             # Configurações Docker
├── docker-compose.yml
└── .env
```

## Desenvolvimento

### Backend (Laravel)
- A API está disponível em `http://localhost:8000`

### Frontend (Vue.js + Quasar)
- A aplicação está disponível em `http://localhost:8080`
- Hot-reload ativado para desenvolvimento

## Tests

Para executar os testes do backend:
```bash
docker-compose exec backend php artisan test
```
