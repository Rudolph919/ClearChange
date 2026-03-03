# ClearChange

## Change Request & Approval Platform

### Purpose

This application demonstrates a structured, auditable workflow for capturing, reviewing, approving, and processing sensitive data changes. It mirrors real-world business systems commonly found in payroll, HR, finance, and regulated environments.

The focus is on **data integrity**, **clear state transitions**, and **recoverable failure handling**.

---

## Problem Statement

Business-critical systems often require:

- Controlled change workflows
- Clear approval processes
- Full audit trails
- Safe retry mechanisms when processing fails

This application models those requirements in a simple, readable way without over-engineering.

---

## Core Features

- Draft → Submit → Approve → Process → Failed / Completed workflow
- Field-level change tracking
- Role-based approval rules
- Immutable audit log
- Background processing using queues
- Safe retry of failed operations

---

## Tech Stack

- Laravel 12
- Vue 3 (Composition API)
- Inertia.js
- Tailwind CSS
- Spatie Laravel Permission (roles & permissions)
- MySQL / PostgreSQL
- Laravel Queues & Jobs

---

## Roles & Permissions

- **Roles:** `user`, `admin`
- **Permissions:** `view audit logs`, `approve change requests`
- New users receive the `user` role on registration
- The `user` role can view audit logs; the `admin` role can also approve change requests
- To restrict audit visibility, remove `view audit logs` from `user` and assign it only to `admin` or a dedicated auditor role

---

## Audit Log

- Status changes and field updates (title, description, status) are recorded automatically
- **Audit trail page:** Each change request has an Audit button (visible to owners and users with `view audit logs`)
- Timeline shows who made changes, when, and what changed (old → new values for updates)
- Job-run transitions (approved → processing → completed, or → failed) are attributed to **System**

---

## Workflow (Implemented)

1. **Create** — User creates a draft with title and description (stored as ChangeRequestItems). Edit uses Current → Proposed for revisions.
2. **Submit** — Owner submits for approval (draft → submitted)
3. **Approve** — Another user approves (submitted → approved) via "Pending my approval"
4. **Process** — Approved requests are processed asynchronously via queue job (approved → processing → completed)
5. **Retry** — Failed requests can be retried by the owner via the Retry button
6. **Audit** — Any user with permission can view the full audit trail for any change request

---

## Architecture Overview

- Changes are captured as immutable change request items (field_name, old_value, new_value)
- The processing job uses items as the payload when applying changes
- Status transitions are explicitly controlled and validated
- Processing occurs asynchronously to prevent partial failures
- All meaningful actions are recorded in an audit log

This structure prioritises clarity and correctness over abstraction.

---

## Key Design Decisions

- **Explicit workflow states** instead of implicit flags
- **Transactional processing** to prevent partial updates
- **Simple service classes** over deep inheritance trees
- **Readable policies** for authorization logic

---

## Tradeoffs

- Single-application architecture instead of microservices
- Focus on clarity over maximum throughput
- Minimal UI styling in favour of behaviour correctness

---

## What This Demonstrates

- Business systems thinking
- Workflow and state management
- Data safety and auditability
- Calm, maintainable Laravel architecture

---

## Step-by-Step Build Prompts (Commit-Friendly)

| Step | Description | Status |
|------|-------------|--------|
| 1 | **Project Setup** — Create Laravel app with auth and Inertia + Vue 3 | ✅ Done |
| 2 | **Core Models** — Create ChangeRequest, ChangeRequestItem, AuditLog models | ✅ Done |
| 3 | **Basic CRUD (Draft Only)** — CRUD for Change Requests in Draft status | ✅ Done |
| 4 | **Status Transitions** — Draft → Submitted → Approved with validation and policies | ✅ Done |
| 5 | **Audit Logging** — Automatically log status changes and field updates | ✅ Done |
| 6 | **Audit Viewing & Permissions** — Spatie roles, Audit button, audit trail page | ✅ Done |
| 7 | **Background Processing** — Process approved requests asynchronously | ✅ Done |
| 8 | **Failure Handling & Retry** — Capture failures and allow safe retry | ✅ Done |

---

## Setup

After cloning and running migrations, seed roles and permissions:

```bash
php artisan db:seed
```

Or with Docker/Podman:

```bash
podman compose exec clearchange_app php artisan db:seed
```

For a **clean run** (drop all tables, re-migrate, then seed):

```bash
php artisan migrate:fresh --seed
```

Or with Docker/Podman:

```bash
podman compose exec clearchange_app php artisan migrate:fresh --seed
```

Assign roles to existing users if needed (e.g. via tinker: `User::find(1)->assignRole('admin')`).

---

## Demo Data

The default seeder populates demo users and change requests in every workflow state. Use these credentials (password: `password`) to explore:

| User | Email | Role | What to try |
|------|-------|------|-------------|
| **Alice** | alice@example.com | user | Draft (edit, submit), Submitted (awaiting approval), Completed |
| **Bob** | bob@example.com | admin | Pending my approval (approve Alice's submitted request), view audit logs |
| **Carol** | carol@example.com | user | Failed request (click Retry to re-queue processing) |

Change requests seeded:

1. **Draft** — Alice's "Update product pricing" (edit and submit)
2. **Submitted** — Alice's "Rename Marketing department" (log in as Bob to approve)
3. **Completed** — Alice's "Extend API rate limit" (full workflow)
4. **Failed** — Carol's "Sync payroll data" (Retry button; failure message shown under status)

---

## Queue (Async Processing)

With `QUEUE_CONNECTION=database` in `.env`, approved requests are queued for background processing. A queue worker must be running:

```bash
php artisan queue:work
```

Or with Docker/Podman: `podman compose --profile with-queue up -d`. Tests use `sync` by default, so jobs run immediately without a worker.

---

## Extending the Processing Job

`ProcessChangeRequestJob` iterates over `ChangeRequestItem` records. To add real processing logic (API calls, DB updates, etc.), extend the `handle()` method's loop:

```php
foreach ($this->changeRequest->items as $item) {
    // $item->field_name, $item->old_value, $item->new_value
    // e.g. sync to external system, update a target record, etc.
}
```

The payload structure (field_name, old_value, new_value) is ready for integration.

---

## Potential Improvements

- Notifications on status changes
- Workflow configuration per change type
- Reporting and analytics

---

## Testing

- **Unit & Feature tests**: `php artisan test` or `vendor/bin/pest`
- **Browser tests**: Requires Playwright. First run `npm install playwright@latest --legacy-peer-deps` and `npx playwright install`, then `vendor/bin/pest tests/Browser`
- **Profanity check**: `vendor/bin/pest --profanity`
- **Mutation testing**: `vendor/bin/pest --mutate`

---

## Docker / Podman

See [README-docker.md](README-docker.md) for how to run the stack with Podman or Docker.
