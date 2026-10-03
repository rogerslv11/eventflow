# EventFlow

Plataforma SaaS completa para **gestão de eventos, ingressos, pedidos, participantes,
equipes, finanças e check-in** — do checkout público ao relatório de lucro.
Construída com Laravel + Livewire (TALL stack) e empacotada em Docker.

![Laravel 13](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel)
![Livewire 4](https://img.shields.io/badge/Livewire-4-4E1D96)
![PHP 8.3](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php)
![MySQL 8.4](https://img.shields.io/badge/MySQL-8.4-4479A1?logo=mysql)
![Docker](https://img.shields.io/badge/Docker-ready-2496ED?logo=docker)
![Tests](https://img.shields.io/badge/tests-5_passing-brightgreen)

## ✨ Funcionalidades

### Vendas e financeiro
- 🎯 **Dashboard** com KPIs, gráficos de receita/ingressos (Chart.js) e atividade recente
- 🧾 **Pedidos** com busca, filtros, detalhe, baixa manual, cancelamento e reembolso
- 🎫 **Ingressos** com tipos, lotes progressivos, limites por pedido e janela de vendas
- 🛒 **Checkout público** com cupons e pagamento simulado (troque pelo gateway real em `CheckoutService`)
- 🏷️ **Cupons** percentuais ou fixos, com limites, vigência e valor mínimo
- 💸 **Despesas** por evento/fornecedor, com receita × pago × lucro em tempo real

### Operação do evento
- 🎉 **Eventos** com workflow de status, duplicação, arquivamento e página pública
- ✅ **Check-in** em tempo real por QR Code, com histórico por hora
- 👥 **Participantes** com busca, filtros, status, exportação CSV e importação
- ⏳ **Lista de espera** pública + gestão (notificar → converter → cancelar)
- 📋 **Tarefas** operacionais em kanban (a fazer → em andamento → concluídas), com prioridades e prazos
- 📅 **Programação** com agenda, timeline, salas e palestrantes vinculados

### Conteúdo e relacionamento
- 🎤 **Palestrantes** com bio, redes sociais e destaques
- ⭐ **Avaliações e NPS** — formulário público + gestão e nota média no dashboard
- 🏆 **Patrocinadores** por cotas (Master, Ouro, Prata, Bronze, Apoio)
- 🚚 **Fornecedores** categorizados e vinculados às despesas
- 👨‍💼 **Equipe** com funções, papéis e permissões por evento

### Plataforma
- 📊 **Relatórios** com filtros por período/evento e exportação CSV
- 🔔 **Notificações** centralizadas (banco + dropdown)
- 🔍 **Busca global** com command palette (`Ctrl`/`Cmd` + `K`)
- 📜 **Auditoria** — log de todas as ações relevantes
- ⚙️ **Configurações** de conta, organização e aparência
- 🏢 **Multi-tenancy** — dados isolados por organização
- 🎟️ **Ingresso digital** com QR Code único

## 🧱 Stack

| Camada | Tecnologia |
|--------|------------|
| Backend | Laravel 13 · PHP 8.3 |
| UI reativa | Livewire 4 · Alpine.js 3 · Blade |
| Estilo | Tailwind CSS 4 (design system próprio em `resources/css/app.css`) |
| Banco / cache / filas | MySQL 8.4 · Redis 7 |
| Gráficos | Chart.js · Ícones SVG inline |
| Auth | Sessão web (+ Sanctum p/ tokens) |
| Testes | PHPUnit 12 contra MySQL (`eventflow_testing`) |
| Deploy | Dockerfile multi-stage + Compose (app Apache, worker, MySQL, Redis) |

## 🚀 Início rápido (Docker — recomendado)

Pré-requisitos: Docker 24+ com plugin Compose.

```bash
git clone <repo> eventflow && cd eventflow
cp .env.example .env

# Sobe tudo (build + migrations automáticas no boot do app)
docker compose up -d --build
docker compose logs -f app
```

Acesse **http://localhost:8080** e entre com (após seed — veja abaixo):

| Email | Senha | Papel |
|-------|-------|-------|
| `demo@eventflow.app` | `password` | Owner |
| `admin@eventflow.app` | `password` | Admin |

Primeira subida com dados demo:

```bash
SEED_ON_BOOT=true docker compose up -d --build
```

| Serviço | Porta local | Descrição |
|---------|-------------|-----------|
| `app` | `8080` → 80 | Apache + PHP 8.3 (assets Vite compilados no build) |
| `mysql` | `3307` → 3306 | MySQL 8.4 · volume `mysql_data` |
| `redis` | `6380` → 6379 | Cache, sessões e filas (`phpredis`) |
| `worker` | — | `queue:work` consumindo as filas |

Variáveis úteis no `.env`:

| Variável | Padrão | Descrição |
|----------|--------|-----------|
| `APP_PORT` / `MYSQL_PORT` / `REDIS_PORT` | `8080`/`3307`/`6380` | Portas publicadas |
| `MIGRATE_ON_BOOT` | `true` | `migrate --force` ao iniciar o `app` |
| `SEED_ON_BOOT` | `false` | `db:seed --force` no boot |
| `CACHE_STORE` / `QUEUE_CONNECTION` / `SESSION_DRIVER` | `redis` | Volte a `database` se subir sem Redis |

## 💻 Desenvolvimento local (sem Docker)

Pré-requisitos: PHP 8.3 (+`pdo_mysql`), Composer, Node 18+ e MySQL 8+ rodando.

```bash
cp .env.example .env
php artisan key:generate

# MySQL local via Compose (só infra) ou ajuste DB_* no .env p/ seu servidor
docker compose up -d mysql redis

composer install
npm install
php artisan migrate --seed

# Terminais separados:
php artisan serve   # http://127.0.0.1:8000
npm run dev         # HMR do Vite
```

> Sem a extensão `pdo_mysql` no PHP local, use os containers para tudo:
> `docker compose exec app php artisan <comando>`.

## 🧪 Testes

A suíte roda contra MySQL (`eventflow_testing`) — SQLite foi removido do projeto.

```bash
docker compose up -d mysql redis
docker compose -f docker-compose.yml -f docker-compose.test.yml run --rm app php artisan test --compact

# Filtro
docker compose -f docker-compose.yml -f docker-compose.test.yml run --rm app php artisan test --filter=NewFeaturesTest
```

> Local sem Docker: instale `php8.3-mysql`, crie a base `eventflow_testing` com grant
> ao usuário do `.env` e rode `DB_HOST=127.0.0.1 DB_PORT=3307 php artisan test --compact`.

## 🗂️ Estrutura

```
app/
├── Enums/                  # EventStatus, OrderStatus, TicketType, ...
├── Http/Controllers/
│   ├── Auth/               # Login, Register, PasswordReset, Verification
│   ├── Dashboard/          # Dashboard, Profile, Settings
│   └── Public/             # Página do evento, checkout
├── Livewire/               # Componentes por domínio
│   ├── CheckIn/ Coupons/ Dashboard/ Events/ Expenses/
│   ├── Orders/ Participants/ Public/ Reports/ Reviews/
│   ├── Schedule/ Speakers/ Sponsors/ Tasks/ Team/
│   └── Tickets/ Vendors/ Waitlist/
├── Models/                 # Eloquent (Event, Order, Ticket, ...)
├── Policies/               # Autorização (Gate::authorize)
├── Services/               # ActivityLogger, CheckoutService, StatsService, ...
├── Jobs/ Mail/ Notifications/
└── Support/helpers.php     # event_status_badge(), route_exists(), ...

database/
├── migrations/             # 30 migrations (MySQL 8.4, FKs ordenadas)
├── factories/              # Factories p/ todos os modelos
└── seeders/                # Seed demo (owner + admin + dados)

resources/
├── css/app.css             # Design system (cards, botões, tabelas, modais, ...)
├── js/app.js               # Alpine stores, Chart.js, atalhos (Ctrl+K)
└── views/
    ├── auth/               # Login, registro, reset
    ├── components/         # layouts, sidebar, topbar, page-header, empty-state, ...
    ├── dashboard/          # Activity, notifications
    ├── livewire/           # Views dos componentes
    ├── profile/ settings/  # Conta e organização
    └── public/             # Eventos, evento, checkout, ingresso, sucesso

docker/                     # apache vhost, php.ini prod, entrypoint
Dockerfile                  # multi-stage: node build → composer → apache+php
docker-compose.yml          # app + worker + mysql + redis
docker-compose.test.yml     # override: imagem com phpunit/pint
```

## 🔐 RBAC

Papéis em `App\Enums\OrganizationRole`: Super Admin, Organization Owner, Admin,
Event Manager, Staff, Check-in Staff, Finance e Viewer. Policies em `app/Policies/`.

## 🛣️ Rotas

**Públicas:** `GET /events` · `GET /event/{slug}` · `GET /event/{slug}/checkout` ·
`GET /order/{code}/success` · `GET /ticket/{code}` (+ `POST /event/{slug}/checkout/process`)

**Auth:** `/login` · `/register` · `/forgot-password` · `/reset-password/{token}`

**Dashboard** (`/dashboard`, auth + verified): `/` · `/events…` · `/orders` ·
`/tickets` · `/participants` · `/check-in` · `/waitlist` · `/schedule` ·
`/speakers` · `/tasks` · `/reviews` · `/team` · `/vendors` · `/sponsors` ·
`/coupons` · `/expenses` · `/reports` · `/profile` · `/settings` ·
`/notifications` · `/activity`

**API interna:** `GET /api/search?q=…` (busca global autenticada)

## 🗄️ Banco (31 tabelas)

```
users · organizations · organization_user · event_categories · events
ticket_types · ticket_batches · orders · order_items · tickets
participants · check_ins · coupons · coupon_usages · staff_roles · event_staff
speakers · event_speaker · schedules · vendors · sponsors
event_reviews · waitlists · event_tasks · event_expenses
activity_logs · notifications (+ cache, jobs, sessions do framework)
```

## 🎨 Design system

Todo o visual nasce de `resources/css/app.css` (Tailwind 4): `.card`, `.toolbar`,
`.btn` (+`primary/secondary/danger/ghost/sm/lg`), `.input`, `.badge` (6 tons),
`table.table` com header sticky, `.modal-panel/backdrop`, `.dropdown-panel/item`,
`.sidebar-link`, `.page-header`, `x-empty-state`, `x-stat-card`, toasts e
command palette. Para trocar o roxo principal, ajuste as variáveis
`--color-primary-*` no `@theme`.

## 🔌 Integrações futuras

A arquitetura já prevê: gateway de pagamento (`CheckoutService::processPayment`),
e-mail transacional (`MAIL_*`), WhatsApp/SMS (`NotificationService`), S3
(`FILESYSTEM_DISK`), API REST (`routes/api.php`) e app mobile como backend JSON.

## 🛠️ Comandos úteis

```bash
php artisan optimize:clear            # limpar caches
php artisan migrate:fresh --seed     # reset total + demo
docker compose logs -f app worker    # logs da stack
docker compose exec app php artisan tinker
docker compose down                  # para (mantém dados)
docker compose down -v               # APAGA banco e redis
```

## 🩺 Troubleshooting

| Sintoma | Causa provável / solução |
|---------|--------------------------|
| `Bind for :::8080 failed` | Porta ocupada → `APP_PORT=8081 docker compose up -d` |
| `could not find driver` (local) | PHP sem `pdo_mysql` → use `docker compose exec app …` |
| `SQLSTATE[HY000] [2002]` nos testes | MySQL fora do ar → `docker compose up -d mysql redis` |
| `Table ... doesn't exist` no seed | Boot sem migrate → `MIGRATE_ON_BOOT=true` (padrão) |
| Assets desatualizados no Docker | Rebuild: `docker compose up -d --build app worker` |

## 📝 Licença

MIT — use como quiser.

---

**EventFlow** — do checkout ao check-in · Laravel 13 + Livewire 4 + MySQL · 2026
