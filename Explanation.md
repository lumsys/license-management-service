# Centralized License Service – Explanation

## 1. Problem and Requirements 

The goal of this system is to build a **Centralized License Service** that acts as the **single source of truth** for licenses and entitlements across multiple brands within the same ecosystem (e.g., RankMath, WP Rocket).

**Key challenges:**

* Managing license activation and seat limits reliably across multiple products.
* Ensuring **multi-brand isolation** while maintaining a single centralized API.
* Handling **soft-deleted activations** to prevent duplicate entries while keeping seat counts accurate.
* Providing a scalable, observable, and extensible API-only system.

Each brand:

* Manages its own users, payments, and subscriptions.
* Integrates with this service to **provision, update, and query licenses**.

End-user products (WordPress plugins, apps, CLI tools):

* Call this service to **activate licenses**.
* Validate license status and entitlements.
* Optionally manage **activation seats** per license.



## 2. Architecture and Design

### High-Level Architecture



 Brand Systems     ---->  License Service   <----  End-User Apps 
                            (API)                     Plugins/CLI   
                            |
                        MySQL (multi-tenant)
                            


* **Laravel 10 (API-only)** backend
* Stateless REST APIs
* MySQL as the primary datastore
* Clear separation of concerns:

  * Controllers → HTTP handling
  * Services → Business logic
  * Models → Domain entities
  * Resources - Consistence Data Response Structure
  * Request - Validate request input
  * Exception - Cache and return appropriate error message and code
  * Feature and unit tests fully implemented and passing



### Multi-Tenancy Model

* Multi-tenancy handled via **brand isolation**:

  * Each `Product` belongs to a `Brand`.
  * Each `LicenseKey` belongs to exactly one `Brand`.
  * Brand-specific endpoints protected via **API-key authentication**.
* End-user endpoints are brand-agnostic and read-only.
* Avoids shared mutable state across brands while keeping a single service instance.



### Core Domain Model

| Entity     | Responsibility                         |
| ---------- | -------------------------------------- |
| Brand      | Tenant boundary                        |
| Product    | Sellable item per brand                |
| LicenseKey | Shared key unlocking multiple licenses |
| License    | Product-specific entitlement           |
| Activation | Seat/instance consuming a license      |



### Seat Management and Soft-Delete Handling

* Seat enforcement is **per license**, not per license key.
* Each license has a `seat_limit`.
* Each activation consumes one seat.
* Deactivating an activation frees a seat.
* **Soft deletes** are used for activations and deactivation:

  * Attempting to reactivate restores the previous record.
  * Prevents duplicate entry errors while maintaining seat counts.



### Security Model

* Brand APIs secured using **API keys** via `API-KEY` header.
* A single API-KEY mechanism is used to authenticate two roles:
* brand – restricted to resources owned by the authenticated brand
* internal – elevated access for internal/admin operations
* API keys are validated via middleware which:
* Verifies the key exists and is active
* Resolves the associated role and (when applicable) brand context
* Attaches the authenticated role and brand information to the request
* Authorization rules are enforced at the route and controller level based on the resolved role and brand ownership.
* OAuth2 (Laravel Passport) is designed as a **future upgrade** for finer-grained authorization.
* Security Model



## 3. Trade-offs and Decisions

### API Keys vs OAuth2

* **Chosen:** API keys
* **Alternative:** OAuth2 / JWT
* **Reasoning:** Simpler and sufficient for machine-to-machine communication. OAuth2 adds operational complexity but is a clear future upgrade.



### Seat Enforcement Scope

* **Chosen:** Enforced per license.
* **Alternative:** Per license key.
* **Reasoning:** Licenses represent product entitlements, and seat limits differ per product. Avoids ambiguity when a key unlocks multiple products.



### Database-First Validation

* **Chosen:** Database constraints + runtime checks.
* **Alternative:** In-memory or cache-first validation.
* **Reasoning:** License enforcement must be correct and consistent. Caching can be added later for performance optimization.



### Monolith vs Microservices

* **Chosen:** Modular monolith (Laravel)
* **Alternative:** Microservices
* **Reasoning:** Single bounded context simplifies deployment, observability, and development. Can evolve into microservices if scaling requires.



## 4. Scaling Plan and Evolution

### Near-Term

* Add Redis caching for license status checks.
* Database indexes on `license_key`, `customer_email`.
* Read replicas for high read traffic.

### Mid-Term

* Webhooks for brand synchronization.
* Async processing for analytics/events.
* Rate limiting per brand/product.

### Long-Term

* OAuth2 with fine-grained scopes.
* Event-driven architecture (Kafka / SNS).
* Regional deployments for latency reduction.

---

## 5. User Stories Mapping

| User Story                                           | Status                                                                                                        |
| ---------------------------------------------------- | ------------------------------------------------------------------------------------------------------------- |
| **US1 – Brand can provision a license**              | Fully implemented: brands can create license keys, associate multiple licenses, and tie licenses to products. |
| **US2 – Brand can change license lifecycle**         | Partially implemented (renew); suspend/resume designed for future extension.                                  |
| **US3 – End-user product can activate a license**    | Fully implemented with seat enforcement, instance-based activations, and soft-delete handling.                |
| **US4 – User can check license status**              | Fully implemented: validate license key, list entitled products, show expiration/status.                      |
| **US5 – Deactivate a seat**                          | Fully implemented: soft-deletes activation and frees a seat.                                                  |
| **US6 – Brands can list licenses by customer email** | Designed; endpoint implemented but sample data required for testing.                                          |

---

User Stories

### **US1 – Brand can provision a license**

* Endpoint: `POST /brands/{brand}/licenses`
* Creates a license key and associates licenses to products.
* Each license has a status (`valid`, `suspended`, `cancelled`) and expiration date.
* Supports adding additional licenses to an existing key.

<img width="679" height="446" alt="Brand can provision a license" src="https://github.com/user-attachments/assets/ad7e762c-bd09-4972-99d1-01096e22a758" />


### **US2 – Brand can change license lifecycle**

* Endpoints:
  * `PATCH /licenses/{license}/status` – Change license status (suspend/resume/cancel)
  * `PATCH /licenses/{license}/renew` – Extend license expiration
* Allows brands to manage license lifecycle.

  <img width="686" height="359" alt="Brand can change license lifecycle" src="https://github.com/user-attachments/assets/cd982fdc-f81f-4aff-8b2f-61f497e3bf73" />

  <img width="694" height="423" alt="Brand can renew" src="https://github.com/user-attachments/assets/fb0bf414-0867-4856-b0d1-dca853ad3390" />



### **US3 – End-user product can activate a license**

* Endpoint: `POST /licenses/activate`
* Activates a license for a specific instance (`instance_id`)
* Seat limits are enforced per license.

  <img width="644" height="407" alt="End-user product can activate a license-" src="https://github.com/user-attachments/assets/6f1df935-5da0-45d9-8d52-eae34a9df8f0" />


### **US4 – User can check license status**

* Endpoint: `GET /licenses/{key}`
* Returns:
  * License key
  * Validity
  * Entitlements (per product)
  * Remaining seats
  * Status and expiration
<img width="679" height="439" alt="Can check license status" src="https://github.com/user-attachments/assets/7f612734-a7a1-4b17-8c13-92fd89736a6c" />


### **US5 – End-user product can deactivate a seat**

* Endpoint: `POST /licenses/deactivate`
* Deactivates a specific license activation freeing a seat.

<img width="680" height="364" alt="User can deactivate license" src="https://github.com/user-attachments/assets/a176df50-9ae1-4e51-bd78-256c31c4460f" />

  

### **US6 – Brands can list licenses by customer email**

* Endpoint: `GET /brands/licenses?email={email}`
* Returns all licenses for a given email across the brand.
* Only accessible by brand API keys.

  <img width="685" height="449" alt="Brands can list licenses by customer email-" src="https://github.com/user-attachments/assets/6376113e-9133-44f5-b487-2dee44edce75" />


## 3. API Endpoints

| Method | Endpoint | Description | Authentication |
| ------ | -------- | ----------- | -------------- |
| POST | `/brands/{brand}/licenses` | Provision license key & licenses | API-Key |
| PATCH | `/licenses/{license}/status` | Update license status | API-Key |
| PATCH | `/licenses/{license}/renew` | Renew license | API-Key |
| GET | `/brands/licenses` | List licenses by email | API-Key |
| POST | `/licenses/activate` | Activate a license for an instance | API-Key |
| POST | `/licenses/deactivate` | Deactivate a license instance | API-Key |
| GET | `/licenses/{key}` | Check license status | API-Key |

## 6. How to Run Locally

### Requirements

* PHP 8.2+
* Composer
* MySQL
* Optional: Postman or cURL for testing

### Setup
```
git clone <repo-url>
cd <repo>
composer install
cp .env.example .env
php artisan key:generate
```
##Set database credentials in `.env`:

*env
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=license_service
DB_USERNAME=root
DB_PASSWORD=root
```

* Run migrations and seed sample data:
```
php artisan migrate:fresh --seed
```

* Start server:
```
php artisan serve
```
* run scribe open api:
```
php artisan scribe:generate
```
```
*serve using: http://127.0.0.1:8000/docs

```
---

### Sample API Requests

#### Provision License (Brand)

```
curl -X POST http://localhost:8000/api/brands/1/licenses \
  -H "X-API-KEY: your-brand-key" \
  -H "Content-Type: application/json" \
  -d '{
    "customer_email": "user@example.com",
    "licenses": [
      {"product_code": "rankmath_pro", "expires_at": "2026-01-01"}
    ]
  }'
```

#### Activate License

```
curl -X POST http://localhost:8000/api/licenses/activate \
  -H "Content-Type: application/json" \
  -d '{
    "license_key": "UUID",
    "product_code": "rankmath_pro",
    "instance_id": "https://example.com"
  }'
```

#### Check License Status

```
curl http://localhost:8000/api/licenses/UUID
```

---

## 7. Known Limitations and Next Steps

* OAuth2 not implemented (API keys used instead)
* No webhooks/events for external brand/product updates
* Potential race conditions on concurrent activations
* No caching yet — heavy validation may hit DB under load
* Full audit logging and rate limiting not yet implemented

**Next Steps:**

* Implement OAuth2 for brand APIs.
* Add role-based middleware (role:brand, role:internal)
* Add Redis caching and read replicas.
* Add webhooks for brand synchronization.
* Complete audit logging and rate limiting.
* Extend lifecycle management (suspend/resume) and event-driven architecture.



## 8. Running Tests

This project includes **unit and feature tests** to verify core functionality, including:

* License activation and deactivation
* Seat enforcement
* License provisioning
* License status checks
* Edge cases, e.g., soft-deleted activations

### Running the Test Suite

1. Ensure your environment is set up and the database is migrated:

```
php artisan migrate:fresh --seed
```

2. Run all tests using Laravel’s Artisan command:

```
php artisan test
```

* You should see output like:

<img width="658" height="317" alt="test" src="https://github.com/user-attachments/assets/6370f1e3-ea3e-4e51-be31-fd9f2d69cd2d" />


sunda@Lumsys MINGW64 ~/Documents/License-App/centralized_License_Service (master)
$ php artisan test

   PASS  Tests\Unit\ExampleTest
  ✓ that true is true                                                                                                                                 0.01s

   PASS  Tests\Unit\LicenseActivationServiceTest
  ✓ seat limit enforced                                                                                                                               1.43s

   PASS  Tests\Feature\ExampleTest
  ✓ the application returns a successful response                                                                                                     0.06s

   PASS  Tests\Feature\LicenseActivationTest
  ✓ license can be activated                                                                                                                          1.14s
  ✓ activation fails when seats exceeded                                                                                                              1.09s

   PASS  Tests\Feature\LicenseProvisionTest
  ✓ brand can provision license                                                                                                                       1.14s

   PASS  Tests\Feature\LicenseStatusTest
  ✓ license status endpoint                                                                                                                           1.17s

  Tests:    7 passed (32 assertions)
  Duration: 6.28s



### Notes

* Tests are written using **PHPUnit** and **Laravel testing conventions**.
* **Soft-deleted activations** are included in tests to ensure duplicate key errors do not occur.
* Ensure `.env.testing` or your test database is configured to avoid affecting production or local data.

---

I can also **integrate this Tests section directly into the previous full `Explanation.md`** so the documentation is complete and ready to submit, including endpoints, setup, and tests.

Do you want me to do that?




## Final Note

This solution prioritizes **clarity, correctness, and extensibility**.
It provides a strong foundation suitable for production evolution while fully satisfying the core user stories, with a clear roadmap for scaling and future enhancements.

