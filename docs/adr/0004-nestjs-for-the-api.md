# 0004. NestJS for the API

- **Status:** Accepted
- **Date:** 2026-11-23

## Context and problem

The API has to sync rates from an external service on a schedule, persist them,
expose REST endpoints, and later handle authentication, roles, rate limiting and
alerts. That is enough moving parts to benefit from a framework with a clear
structure for modules, dependency injection and testing, rather than assembling
one from a minimal HTTP library.

## Decision drivers

- TypeScript end to end: one language, one toolchain and shareable code
  between API and UI.
- Built-in structure: modules, dependency injection, guards, pipes, interceptors.
- First-party building blocks for the roadmap: config, scheduling, health
  checks, OpenAPI, rate limiting.
- Testability: swapping real dependencies for fakes without monkey-patching.

## Considered options

1. NestJS
2. Fastify (plain)
3. Express (plain)
4. Symfony (PHP)

## Decision

Chosen option: **NestJS 12** with the Express platform adapter.

- **Symfony** is a mature, well-structured framework and the most direct
  continuation of the PHP prototype. It was rejected because it splits the
  stack into two languages: no shared types or validation schemas with the
  React frontend ([0007](0007-zod-as-single-validation-library.md)).
- **Plain Fastify** is fast and lightweight, but structure (DI, module
  boundaries, auth guards, scheduled jobs) would have to be assembled and
  maintained by hand.
- **Plain Express** has the same drawback with older ergonomics.

**Express adapter, not Fastify or `NodeAdapter`:** Nest also offers faster HTTP
adapters. Throughput is not a constraint here (one sync per business day and a
light read-mostly API), while Express has the widest middleware compatibility
(`helmet`, `cookie-parser`) and is the adapter most Nest documentation and
examples assume. Switching later is local to the bootstrap code.

The project keeps the Nest 12 scaffold defaults instead of fighting them:

- **ESM** (`"type": "module"`, relative imports end in `.js`).
- **Vitest** as the test runner (also used by the frontend).
- **oxlint** as the linter (also used by the frontend).

Removed from the scaffold: `@nestjs/observe` (third-party telemetry SaaS) and
`@nestjs/mau` (vendor hosting). Neither is needed and the first would send
application data to an external service.

## Consequences

**Positive**

- Clear module boundaries (`config`, `prisma`, `health`, later `rates`,
  `conversions`, `auth`). Services are injected, so tests replace them with
  `Test.createTestingModule` overrides.
- Most roadmap needs are covered by `@nestjs/*` packages instead of ad hoc libraries.
- HTTP setup lives in one function (`configureApp` in `src/app.setup.ts`),
  used by both `main.ts` and the e2e tests, so tests run the production
  configuration.

**Negative / trade-offs**

- More concepts to learn than a minimal framework (decorators, providers, module scopes).
- **TypeScript is pinned to 6.0 in `apps/api`:** `@nestjs/cli` requires
  `~6.0.2`, so `nest build` cannot use TypeScript 7 yet. The frontend uses 7.
- Decorator metadata (`emitDecoratorMetadata`) ties the API to `tsc`-style
  compilation.

## Links

- [NestJS documentation](https://docs.nestjs.com/)
- [0005. PostgreSQL with Prisma](0005-postgresql-with-prisma.md)
