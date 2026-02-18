Mini Market Place – Architectural Guidelines & System Rules

This document defines the architectural rules, constraints, and design principles of the system.

All code must comply with these rules.

This file exists to protect architectural integrity over time.

1. Architectural Philosophy

The system must follow:

Clean Architecture

Hexagonal Architecture (Ports & Adapters)

Tactical DDD

Explicit Transaction Boundaries

Strong Consistency over Availability

The primary goal is correctness and integrity, not rapid feature growth.

2. Layering Rules

The system is divided into four logical layers:

Domain
Application
Infrastructure
Interface


Dependency direction must always point inward:

Interface → Application → Domain
Infrastructure → Application → Domain


The Domain layer must never depend on:

Laravel

Eloquent

Database

Framework-specific tools

Violation of this rule invalidates the architecture.

3. Domain Layer Rules

The Domain layer:

Contains business rules

Enforces invariants

Protects consistency

Is framework-independent

3.1 Domain Constraints

No framework imports

No database logic

No HTTP logic

No logging logic

Only pure business logic.

3.2 Aggregates

Aggregates must:

Protect invariants

Expose intention-revealing methods

Prevent invalid state transitions

State mutation must be controlled and explicit.

3.3 Value Objects

Value Objects:

Must be immutable

Must not expose identity

Must encapsulate behavior

4. Application Layer Rules

The Application layer:

Orchestrates use cases

Defines ports (interfaces)

Controls transaction boundaries

It must not:

Contain business rules

Depend directly on Eloquent

Contain persistence logic

4.1 Use Case Structure

Each use case must:

Be isolated in its own class

Execute inside a transaction

Coordinate domain entities

5. Infrastructure Layer Rules

Infrastructure implements adapters.

It is allowed to:

Use Laravel

Use Eloquent

Use database transactions

Apply pessimistic locking

It is not allowed to:

Introduce business rules

Modify domain invariants

Bypass aggregate logic

6. Transaction Rules

All write operations must:

Execute inside a transaction

Be atomic

Avoid partial writes

Transaction boundaries belong in the Application layer via abstraction.

7. Concurrency Rules

All critical mutations must:

Use pessimistic locking

Lock the affected rows

Prevent race conditions

Concurrency protection is mandatory for:

Stock reservation

Stock consumption

Order state transitions

8. Order Lifecycle Rules

Order must:

Start in CREATED

Transition to PAID or CANCELLED

Never revert states

Never allow invalid transitions

Stock must:

Never go negative

Never allow over-reservation

Never allow consumption without reservation

9. Repository Rules

Repositories:

Must be defined as interfaces in Application

Must be implemented in Infrastructure

Must hydrate domain aggregates correctly

Repositories must not expose Eloquent models to the Application or Domain layers.

10. ID Strategy

All primary identifiers must:

Be UUID

Be generated through abstraction

Never be generated directly in Infrastructure logic

11. Non-Functional Requirements

The system must guarantee:

Strong consistency

Deterministic state transitions

Concurrency safety

Clear separation of concerns

Performance optimizations must not violate consistency rules.

12. Extension Rules

Future features must:

Respect layer boundaries

Preserve transaction safety

Avoid leaking framework concerns into Domain

Preserve aggregate integrity

If a feature requires breaking these rules, architecture must be reconsidered.

13. What This System Is Not

Not a CRUD demo

Not framework-driven design

Not anemic domain model

Not eventual-consistency-based

It is intentionally consistency-first.

14. Architectural Integrity Policy

If future changes:

Introduce domain logic into Infrastructure

Break transaction boundaries

Allow direct database manipulation bypassing aggregates

They must be rejected.

Architecture is a constraint, not a suggestion.
## API Integration (Frontend-ready)

### Base URL and versioning
- Base: `/api/v1`
- OpenAPI contract: `docs/openapi.yaml`

### Core read endpoints
- `GET /products?categoryId=&companyId=&q=&page=`
- `GET /products/{id}`
- `GET /categories`
- `GET /companies/{id}`
- `GET /companies/{id}/products`
- `GET /orders/{id}`
- `GET /orders?userId=&status=&page=`
- `GET /stocks/{productId}` and `GET /products/{id}/stock`

### Auth endpoints (mock bearer for quick integration)
- `POST /auth/login`
- `POST /auth/logout`
- `GET /me`

### Error format (consistent)
```json
{
  "error": {
    "code": "ORDER_CANNOT_BE_PAID",
    "message": "Order cannot be paid",
    "correlation_id": "9f198f78-4b76-48ea-8d59-76019c07a70c"
  }
}
```

### cURL examples
```bash
curl -X GET 'http://localhost:8000/api/v1/products?q=cafe&page=1'

curl -X GET 'http://localhost:8000/api/v1/orders?userId=<UUID>&status=created&page=1'

curl -X POST 'http://localhost:8000/api/v1/auth/login' \
  -H 'Content-Type: application/json' \
  -d '{"email":"ana@example.com","password":"secret123"}'

curl -X GET 'http://localhost:8000/api/v1/me' \
  -H 'Authorization: Bearer <ACCESS_TOKEN>'
```
