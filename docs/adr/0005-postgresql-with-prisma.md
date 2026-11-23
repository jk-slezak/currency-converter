# 0005. PostgreSQL with Prisma

- **Status:** Accepted
- **Date:** 2026-11-23

## Context and problem

The application stores NBP rate tables, the exchange rates in them, the list of
currencies and an audit log of sync runs. Later it adds users, refresh tokens,
watchlists and alerts. The data is relational, money must be exact, and the
schema will evolve, so migrations must be versioned.

## Decision drivers

- Exact decimal arithmetic for money; real `DATE` and `TIMESTAMPTZ` types.
- Strong uniqueness guarantees at the database level (idempotent sync).
- Versioned, reviewable migrations.
- Type-safe queries in TypeScript.
- A readable, single-file description of the data model.

## Considered options

Database:

1. PostgreSQL
2. MySQL (used by the prototype)

ORM / query layer:

1. Prisma
2. Drizzle
3. TypeORM

## Decision

Chosen options: **PostgreSQL 17** and **Prisma 7** with the `@prisma/adapter-pg`
driver adapter (`pg` connection pool).

- PostgreSQL: richer types, strict constraints, first-class support on every
  managed host. MySQL 5.7 from the prototype is end-of-life.
- Prisma: a declarative schema (`apps/api/prisma/schema.prisma`) that doubles as
  documentation of the data model, a generated typed client, and SQL
  migrations committed to git. The migration engine stores a checksum of every
  applied migration and detects drift between history and database, which
  makes accidental edits to applied migrations visible instead of silent.
- Drizzle would also be a valid choice: schema in TypeScript, SQL-like query
  builder, no code generation. Prisma was preferred for the dedicated schema
  file and its more complete migration workflow (`migrate dev` / `deploy` /
  `status`, drift detection). The trade-off is the code-generation step.
- TypeORM's decorator-based entities give weaker type inference for query
  results and its migrations are less reliable to generate.

Data model rules from this decision:

- Rates are `Decimal(18, 8)`, never floats. NBP publishes up to **8** decimal
  places for low-value currencies (e.g. IDR `0.00022317` in table
  227/A/NBP/2026). The first schema used `Decimal(18, 6)`, which would have
  rounded that rate to `0.000223` (≈0.08% error). Migration
  `increase_rate_precision` widens the column. The prototype's `DECIMAL(10, 4)`
  had the same problem, only worse.
- The effective date lives only on `RateTable`. `ExchangeRate` references the
  table instead of repeating the date.
- `@@unique([currencyCode, rateTableId])` and unique `RateTable.effectiveDate` /
  `tableNo` make re-importing the same NBP table a no-op (idempotent sync).
- `SyncRun` records every sync attempt, including the expected `NO_DATA`
  outcome on weekends and public holidays.
- Table and column names are `snake_case` (`@@map`/`@map`), model names are
  `PascalCase` in code.

## Consequences

**Positive**

- One `PrismaService` (one connection pool) shared through Nest DI.
- Migrations are plain SQL files, reviewed in pull requests and applied in
  production with `prisma migrate deploy`.

**Negative / trade-offs**

- A code-generation step: the client is generated into
  `apps/api/src/generated/prisma` (git-ignored). `npm install` runs
  `prisma generate` via `postinstall`, and schema changes require re-running it.
- Prisma 7 requires explicit generator options for this ESM + `nodenext` setup
  (`moduleFormat = "esm"`, `importFileExtension = "js"`).
- The Prisma CLI brings a known advisory in a transitive dependency (`effect`),
  accepted and documented in [`SECURITY.md`](../../SECURITY.md); see
  [0008](0008-dependency-policy.md).
- Prisma 8 is in release-candidate stage with a different architecture. An
  upgrade will need its own ADR.

## Links

- [Prisma schema](../../apps/api/prisma/schema.prisma)
- [Initial migration](../../apps/api/prisma/migrations)
