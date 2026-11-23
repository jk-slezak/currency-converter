# Architecture Decision Records

An Architecture Decision Record (ADR) captures one significant technical
decision: the context, the options that were considered, the choice, and its
consequences. The goal is to preserve the _why_ behind the code.

## Index

| ADR                                                   | Title                                                              | Status   |
| ----------------------------------------------------- | ------------------------------------------------------------------ | -------- |
| [0001](0001-record-architecture-decisions.md)         | Record architecture decisions                                      | Accepted |
| [0002](0002-rewrite-php-prototype.md)                 | Rewrite the PHP prototype instead of refactoring it                | Accepted |
| [0003](0003-monorepo-with-npm-workspaces.md)          | Monorepo with npm workspaces                                       | Accepted |
| [0004](0004-nestjs-for-the-api.md)                    | NestJS for the API                                                 | Accepted |
| [0005](0005-postgresql-with-prisma.md)                | PostgreSQL with Prisma                                             | Accepted |
| [0006](0006-react-vite-spa.md)                        | React single-page application built with Vite                      | Accepted |
| [0007](0007-zod-as-single-validation-library.md)      | Zod as the single validation library                               | Accepted |
| [0008](0008-dependency-policy.md)                     | Dependency policy: exact versions, 7-day release age, no overrides | Accepted |
| [0009](0009-local-development-environment.md)         | Local development environment                                      | Accepted |
| [0010](0010-representing-money-and-exchange-rates.md) | Representing money and exchange rates                              | Proposed |

## Adding a new ADR

1. Copy [`template.md`](template.md) to `NNNN-short-title.md`, using the next
   free number.
2. Fill it in, starting with `Status: Proposed`, and open a pull request.
   The ADR is reviewed together with the code it affects.
3. On merge, set `Status: Accepted` and add it to the index above.

Accepted ADRs are not rewritten. If a decision changes, write a new ADR and
mark the old one as `Superseded by NNNN`. Typo fixes and broken links are fine
to edit in place.
