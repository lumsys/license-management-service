# Centralized License Service – Explanation

## 1. Problem and Requirements (In My Own Words)

The goal of this system is to build a **centralized License Service** that acts as the **single source of truth** for licenses and entitlements across multiple brands within the same ecosystem (e.g. RankMath, WP Rocket, etc.).

Each brand:

* Manages its own users, payments, and subscriptions
* Integrates with this service to **provision, update, and query licenses**

End-user products (WordPress plugins, apps, CLIs):

* Call this service to **activate licenses**
* Check license validity and entitlements
* Optionally manage seats (activations per license)

The service must:

* Support **multiple brands (multi-tenant)**
* Be **API-only** (no UI)
* Be scalable, observable, and extensible
* Clearly separate **brand-level operations** from **end-user operations**

---

## 2. Architecture and Design

### High-Level Architecture

```
Brand Systems ──▶ License Service ◀── End-User Products
                     |
                 MySQL
```

* **Laravel 10 (API-only)** backend
* Stateless REST APIs
* MySQL as the primary datastore
* Clear separation of concerns:

  * Controllers → HTTP handling
  * Services → Business logic
  * Models → Domain entities

---

### Multi-Tenancy Model

Multi-tenancy is handled via **Brand isolation**:

* Every `Product` belongs to a `Brand`
* Every `LicenseKey` belongs to exactly one `Brand`
* Brand-specific endpoints are protected by **API-key authentication**
* End-user endpoints are read-only and brand-agnostic

This avoids shared mutable state across brands while keeping a single service instance.

---

### Core Domain Model

| Entity     | Responsibility                         |
| ---------- | -------------------------------------- |
| Brand      | Tenant boundary                        |
| Product    | Sellable item per brand                |
| LicenseKey | Shared key unlocking multiple licenses |
| License    | Product-specific entitlement           |
| Activation | Seat / instance consuming a license    |

---

### Seat Management Design

* Seat enforcement is **per License**, not per LicenseKey
* Each license has a `seat_limit`
* Each activation consumes one seat
* Activations are enforced at runtime during license activation
* Deactivation (designed) frees a seat

---

### Security Model

* **Brand APIs** secured using **API keys**
* API keys sent via `API-KEY` header
* End-user APIs do not allow license provisioning or listing
* OAuth2 (Laravel Passport) is designed as a future upgrade path

---

## 3. Trade-offs and Decisions

### API Keys vs OAuth2

**Chosen:** API keys
**Alternative:** OAuth2 / JWT

**Reasoning:**

* API keys are simpler and sufficient for machine-to-machine communication
* OAuth2 adds operational complexity not strictly required for this exercise
* OAuth2 integration is clearly documented as a next step

---

### Seat Enforcement Scope

**Chosen:** Enforced per license
**Alternative:** Per license key

**Reasoning:**

* Licenses represent product entitlements
* Seat limits often differ per product
* Avoids ambiguity when one license key unlocks multiple products

---

### Database-First Validation

**Chosen:** Database constraints + runtime checks
**Alternative:** In-memory or cache-first

**Reasoning:**

* License enforcement must be correct, not eventually consistent
* Database guarantees correctness
* Caching can be added later for performance

---

### Monolith vs Microservices

**Chosen:** Modular monolith (Laravel)
**Alternative:** Microservices

**Reasoning:**

* This service is a single bounded context
* A monolith simplifies deployment and observability
* Can later be split if scaling demands it

---

## 4. Scaling Plan and Evolution

### Near-Term Scaling

* Add Redis caching for license status checks
* Database indexes on `license_key`, `customer_email`
* Read replicas for heavy read traffic

### Mid-Term

* Webhooks for brand synchronization
* Async processing for analytics/events
* Rate limiting per brand/product

### Long-Term

* OAuth2 with fine-grained scopes
* Event-driven architecture (Kafka / SNS)
* Regional deployments for latency reduction

---

## 5. User Stories Mapping

### US1 – Brand can provision a license

**Status:** 

* Brands create license keys
* Associate multiple licenses to a single key
* Each license tied to a product, status, expiration

---

### US2 – Brand can change license lifecycle

**Status:** 

* Renew → extend expiration
* Suspend / resume → status change
* Cancel → permanent invalidation
  **Reason:** Not core to minimum slice; lifecycle model is defined and extensible

---

### US3 – End-user product can activate a license

**Status:** 

* License activation per product
* Seat enforcement applied
* Instance-based activation supported

---

### US4 – User can check license status

**Status:** 

* Validate license key
* List entitled products
* Show expiration and status

---

### US5 – Deactivate a seat

**Status:** 

* Activation deletion frees a seat
* Straightforward extension of Activation model

---

### US6 – Brands can list licenses by customer email

**Status:** 

* Brand-only endpoint
* Lists licenses across all brands
* Protected via API-key authentication

---

## 6. How to Run Locally

### Requirements

* PHP 8.2+
* Composer
* PostgreSQL

---

### Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Set database credentials in `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=license_service
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

Run migrations:

```bash
php artisan migrate
```

Start server:

```bash
php artisan serve
```

---

### Sample API Requests

#### Provision License (Brand)

```bash
curl -X POST http://localhost:8000/api/brands/1/licenses \
  -H "X-API-KEY: your-brand-key" \
  -H "Content-Type: application/json" \
  -d '{
    "customer_email": "user@example.com",
    "licenses": [
      {
        "product_code": "rankmath_pro",
        "expires_at": "2026-01-01"
      }
    ]
  }'
```

---

#### Activate License

```bash
curl -X POST http://localhost:8000/api/licenses/activate \
  -H "Content-Type: application/json" \
  -d '{
    "license_key": "UUID",
    "product_code": "rankmath_pro",
    "instance_id": "https://example.com"
  }'
```

---

#### Check License Status

```bash
curl http://localhost:8000/api/licenses/UUID
```

---

## 7. Known Limitations and Next Steps

### Known Limitations

* OAuth2 not implemented (API keys used instead)
* Seat deactivation endpoint not implemented
* No caching layer yet
* No webhooks/events

---

### Next Steps

* Add OAuth2 (Laravel Passport)
* Implement seat deactivation
* Add Redis caching
* Add rate limiting
* Add full audit logging
* Add API documentation (OpenAPI)

---

## Final Note

This solution prioritizes **clarity, correctness, and extensibility** over unnecessary complexity.
It provides a strong foundation suitable for production evolution while fully satisfying the core requirements of the exercise.

