# 0009. Local development environment

- **Status:** Accepted
- **Date:** 2026-11-23

## Context and problem

Developers need PostgreSQL, the API and the frontend running locally, with one
place for configuration. The setup must coexist with other local projects,
which commonly occupy the default ports 5432 (PostgreSQL) and 3000 (Node
servers), and must not expose services beyond the developer's machine.

## Decision drivers

- Fast feedback: hot reload for API and UI.
- One command to get a database, no local PostgreSQL install.
- One source of configuration, never committed.
- Secure defaults on shared networks.
- Parity with production where it matters (real PostgreSQL, same `/api` routing).

## Considered options

1. Docker Compose for PostgreSQL only; API and web run on the host
2. Everything in Docker Compose (database, API, web)
3. Locally installed PostgreSQL

## Decision

Chosen option: **Docker Compose for PostgreSQL only**.

| Service    | Runs in                       | Address                     |
| ---------- | ----------------------------- | --------------------------- |
| PostgreSQL | Docker (`postgres:17-alpine`) | `127.0.0.1:5433`            |
| API        | Host (`nest start --watch`)   | `http://localhost:3100/api` |
| Web        | Host (Vite dev server)        | `http://localhost:5173`     |

- **Single root `.env`** (git-ignored) read by Docker Compose, the API
  (`@nestjs/config`), the Prisma CLI (`prisma.config.ts`) and the Vite config
  (proxy target only; nothing is exposed to browser code).
  [`.env.example`](../../.env.example) documents every variable.
- **Database port bound to `127.0.0.1`**, so it is not reachable from other
  machines on the same network, even with development credentials.
- **Non-default ports** (5433, 3100) avoid clashes with other local projects.
- **The API refuses to start** on invalid configuration (Zod validation, see
  [0007](0007-zod-as-single-validation-library.md)) and on an unreachable
  database or wrong credentials (a `SELECT 1` round-trip during bootstrap).
- **`GET /api/health`** (`@nestjs/terminus`) checks the database connection
  at runtime and returns 503 when it is down. The same endpoint serves as a
  readiness probe in production.
- `.env` paths are resolved from the config files' own location, not from the
  current working directory, so commands work from any directory.
- `npm run db:up` waits for the container's health check before returning.

Running the API and web inside containers would slow down file watching and
HMR on macOS. A locally installed PostgreSQL would differ in version between
machines.

## Consequences

**Positive**

- `cp .env.example .env && npm install && npm run db:up` gives a working
  environment.
- Production differs only in where variables come from (platform environment
  instead of `.env`). The configuration is validated the same way
  ([0007](0007-zod-as-single-validation-library.md)).

**Negative / trade-offs**

- Node.js 26 must be installed on the host (`.nvmrc` provided).
- The API is not containerised yet. A production Dockerfile comes in Phase 4.
