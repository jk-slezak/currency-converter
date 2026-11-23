# 0003. Monorepo with npm workspaces

- **Status:** Accepted
- **Date:** 2026-11-23

## Context and problem

The application has two deployable parts: a REST API and a browser frontend.
They share an HTTP contract and, later, validation schemas (see
[0007](0007-zod-as-single-validation-library.md)). Features usually touch both
sides at once.

## Decision drivers

- A feature that spans API and UI should be one pull request and one review.
- The contract between API and UI must not drift silently.
- Tooling overhead must stay proportional to a two-app project.

## Considered options

1. Two separate repositories
2. Monorepo with plain npm workspaces
3. Monorepo with a build system (Nx, Turborepo)
4. Monorepo with pnpm workspaces

## Decision

Chosen option: **monorepo with npm workspaces**.

```
apps/
  api/   NestJS REST API
  web/   React SPA
```

- One `package-lock.json`, one `npm install`, root scripts that fan out to
  workspaces (`npm run lint`, `npm test`, `npm run build`).
- Shared root configuration: Prettier, `.npmrc`, `.env`, `.gitignore`.
- Each app keeps its own `tsconfig`, linter config and test config, because
  their runtimes differ (Node ESM vs browser bundle).
- No `packages/shared` yet: it is added only when the first real piece of code
  is shared.

Separate repositories would make every cross-cutting change two pull requests
with a version handshake. Nx and Turborepo bring task graphs and remote caching,
which pay off with many packages, not two. pnpm is a good tool but adds another
binary to install, and npm workspaces cover every need here.

## Consequences

**Positive**

- Atomic changes across API and UI. Easy to share code later.
- Zero extra tooling: Node and npm are enough to work on the project.

**Negative / trade-offs**

- No build caching or affected-only task runs. CI runs everything, which is
  acceptable at this size.
- npm workspaces may hoist different versions of one package (e.g. TypeScript
  6 in `api`, 7 in `web`, see [0004](0004-nestjs-for-the-api.md)).
- Revisit if a third package appears or CI time becomes a problem.
