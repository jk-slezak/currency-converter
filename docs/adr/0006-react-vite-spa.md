# 0006. React single-page application built with Vite

- **Status:** Accepted
- **Date:** 2026-11-23

## Context and problem

The frontend shows the current rates table, a conversion form and rate history
charts, and later login, a watchlist and alerts. All data comes from the API.
Content is user-specific or interactive, and there is no requirement for search
engine indexing of individual pages.

## Decision drivers

- Team expertise: React + TypeScript.
- A clean separation: the API owns business logic and data. The frontend is a client.
- Fast local development (HMR) and a simple static deployment.
- Same-origin API calls in development and production, so that cookie-based
  authentication works without cross-site cookies.

## Considered options

1. React SPA built with Vite
2. Next.js (React with server rendering and its own server layer)
3. Server-rendered templates from the API

## Decision

Chosen option: **React 19 SPA built with Vite 8**, in `apps/web`.

- Next.js would add a second server layer (route handlers, server actions) that
  overlaps with the NestJS API. SSR and SEO bring little value for this
  interactive, data-driven UI.
- Server-rendered templates would couple UI to the API and drop the React skill
  set the project is meant to show.

Routing of API calls:

- **Development:** the Vite dev server proxies `/api/*` to the NestJS API
  (port from the shared `.env`).
- **Production:** the static host rewrites `/api/*` to the API service.
  The browser always talks to one origin.

Libraries planned for Phase 2: TanStack Query (server state), React Hook Form +
Zod (forms, see [0007](0007-zod-as-single-validation-library.md)).

## Consequences

**Positive**

- No CORS configuration in development or production. The future refresh-token
  cookie is first-party (`SameSite=Lax`), which avoids Safari's third-party
  cookie blocking.
- The frontend deploys as static files to any CDN.

**Negative / trade-offs**

- First paint needs JavaScript. Acceptable for an application UI, but the
  landing view should stay lightweight.
- The production host must support rewrites (Vercel, Netlify, Nginx all do).
- The API global prefix `/api` is part of the contract and must not change.
