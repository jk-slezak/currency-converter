# Currency Converter

Currency conversion based on official exchange rates published by
[Narodowy Bank Polski (NBP)](https://api.nbp.pl/).

> **Status:** rewrite in progress — from a PHP + MySQL prototype to a
> NestJS + PostgreSQL API with a React frontend. The original version is
> preserved under the [`legacy-php`](../../tree/legacy-php) tag.

## Stack

| Layer    | Technology                                               |
| -------- | -------------------------------------------------------- |
| API      | NestJS 12 (ESM), Prisma 7, PostgreSQL 17, Zod            |
| Frontend | React 19, Vite 8, TypeScript                             |
| Tooling  | npm workspaces, Vitest, oxlint, Prettier, Docker Compose |

## Repository layout

```
apps/
  api/   NestJS REST API (prisma/ holds the schema and migrations)
  web/   React SPA (dev server proxies /api to the API)
```

## Getting started

Requirements: Node.js 26 (`nvm use`), Docker.

```bash
cp .env.example .env
npm install
npm run db:up                                  # PostgreSQL on localhost:5433
npm run prisma:deploy -w apps/api              # apply migrations
npm run dev:api                                # http://localhost:3100/api/health
npm run dev:web                                # http://localhost:5173
```

## Scripts

| Command                        | Description                         |
| ------------------------------ | ----------------------------------- |
| `npm test`                     | Unit tests (all workspaces)         |
| `npm run test:e2e -w apps/api` | API end-to-end tests (needs the DB) |
| `npm run lint`                 | Lint all workspaces                 |
| `npm run build`                | Production build of API and web     |

## Architecture decisions

Key technical choices and their trade-offs are recorded as
[Architecture Decision Records](docs/adr/README.md): stack, repository layout,
data model, validation, dependency policy and the local environment.
See also [SECURITY.md](SECURITY.md).

## License

GPL-3.0
