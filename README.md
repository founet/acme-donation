# 🌱 Laravel Donation Platform API

This is a backend API built with **Laravel** that powers a donation platform. It supports features like campaign creation, user donations, and role-based access, and follows a clean architecture style.

## 🚀 Features

- User registration and login (via Sanctum).
- Campaign management (create, update, delete).
- Secure donation workflow with business rules:
    - Donations only allowed on active campaigns.
    - Role-based authorization.
- API responses standardized through helper methods.
- Containerized environment with Docker.

## 🧱 Architecture

This project is structured following **Ports and Adapters (Hexagonal Architecture)**:

- **Domain Layer**: Pure business logic (entities, services).
- **Application Layer**: Use cases and DTOs.
- **Infrastructure Layer**: Persistence (Eloquent), external services.
- **Interface Layer**: Controllers, HTTP requests/responses.

## 🐳 Getting Started with Docker

### Prerequisites

- Docker
- Docker Compose

### Setup

```bash
git clone git@github.com:founet/acme-donation.git
cd acme-donation

```

```bash
docker compose up -d --build
```

The application will:

- Install dependencies if not already installed.
- Run fresh database migrations and seed data.
- Start the Laravel server at [http://localhost:9001](http://localhost:9001
  )

## 📮 Endpoints

### 🔐 Authentication

To obtain a bearer token, log in with a seeded user:

```http
POST /api/login
Content-Type: application/json

{
  "email": "alice@acme.test",
  "password": "password"
}
```

This returns a `token` that should be used for all authenticated endpoints.

### 💸 Donations

Example:

```http
POST /api/donations
Authorization: Bearer <token>
Content-Type: application/json

{
  "campaign_id": 1,
  "amount": 50,
  "currency": "EUR",
  "payment_source": "stripe"
}
```

## 🧪 Running Tests

```bash
docker exec -it <app-container-name> composer test
```

## 🧪 Static Analysis(phpstan)

```bash
docker exec -it <app-container-name> composer analyse