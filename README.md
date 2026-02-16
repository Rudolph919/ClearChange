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

- Laravel 11/12
- Vue 3 (Composition API)
- Inertia.js
- MySQL / PostgreSQL
- Laravel Queues & Jobs

---

## Architecture Overview

- Changes are captured as immutable change request items
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

1. **Project Setup** — Create Laravel app with auth and Inertia + Vue 3.  
   *Commit: `chore: bootstrap laravel with inertia and vue`*

2. **Core Models** — Create ChangeRequest, ChangeRequestItem, AuditLog models.  
   *Commit: `feat: add change request core models`*

3. **Basic CRUD (Draft Only)** — CRUD for Change Requests in Draft status.  
   *Commit: `feat: draft change request creation`*

4. **Status Transitions** — Draft → Submitted → Approved with validation and policies.  
   *Commit: `feat: implement change request workflow states`*

5. **Audit Logging** — Automatically log status changes and field updates.  
   *Commit: `feat: add audit logging for change requests`*

6. **Background Processing** — Process approved requests asynchronously.  
   *Commit: `feat: async processing for approved requests`*

7. **Failure Handling & Retry** — Capture failures and allow safe retry.  
   *Commit: `feat: failure handling and retry mechanism`*

---

## Potential Improvements

- Notifications on status changes
- Workflow configuration per change type
- Reporting and analytics

---

## Docker / Podman

See [README-docker.md](README-docker.md) for how to run the stack with Podman or Docker.
