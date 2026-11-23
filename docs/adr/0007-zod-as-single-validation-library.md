# 0007. Zod as the single validation library

- **Status:** Accepted
- **Date:** 2026-11-23

## Context and problem

Untrusted data enters the system in several places: environment variables at
startup, HTTP request bodies and query strings, responses from the NBP API, and
form input in the browser. Each needs runtime validation, and the API and the
frontend validate many of the same shapes (e.g. a conversion request).

## Decision drivers

- One validation approach to learn and maintain across API and UI.
- Static types derived from the schemas, not written twice.
- Ability to share schemas between `apps/api` and `apps/web`.
- Native support in the chosen framework.

## Considered options

1. Zod everywhere
2. `class-validator` + `class-transformer` DTOs in the API, Zod in the frontend
3. Joi

## Decision

Chosen option: **Zod 4 everywhere**.

- **Configuration:** environment variables are validated once at startup in
  [`apps/api/src/config/env.ts`](../../apps/api/src/config/env.ts). An invalid
  configuration stops the process with exit code 1 and a list of problems.
  Error messages contain variable names, never their values (no leaked secrets).
- **HTTP input (Phase 2):** NestJS 12 validates Standard Schema libraries
  natively (`StandardSchemaValidationPipe`, `@Body({ schema })`), so request
  validation uses Zod without decorators.
- **External data (Phase 2):** NBP responses are parsed with Zod before they
  touch the database. An external service is untrusted input too.
- **Frontend (Phase 2):** React Hook Form with the Zod resolver.

`class-validator` is the traditional NestJS choice, but its decorator-based
classes cannot be reused in the browser and duplicate the TypeScript types.
Joi has no type inference.

## Consequences

**Positive**

- `z.infer` gives types for free. A schema change updates every consumer at
  compile time.
- Shared schemas can move into a workspace package when the first one is needed
  (see [0003](0003-monorepo-with-npm-workspaces.md)).

**Negative / trade-offs**

- OpenAPI generation from Zod schemas needs its own approach. It will be
  decided and documented in Phase 2 together with the Swagger setup. If no
  maintainable way to produce an accurate OpenAPI document from Zod exists,
  request validation moves to the option that does, in a new ADR superseding
  this one.
- Less community material than `class-validator` for NestJS-specific recipes.
