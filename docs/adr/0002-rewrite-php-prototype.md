# 0002. Rewrite the PHP prototype instead of refactoring it

- **Status:** Accepted
- **Date:** 2026-11-23

## Context and problem

The original application (PHP 8.2, `mysqli`, MySQL 5.7) fetched NBP exchange
rates, displayed them in a table and converted amounts between currencies.
A review of the code found defects at every layer:

- **Incorrect results:** NBP `mid` is "PLN per 1 unit", so a conversion is
  `amount × source.mid ÷ target.mid`. The code computed
  `amount ÷ source.mid × target.mid`.
- **Security:** SQL injection (currency code from `$_POST` concatenated into
  SQL), reflected XSS (`$_POST` echoed into HTML), credentials committed in
  `config.php`.
- **Data model:** no date column and no unique key. Every page view re-fetched
  the NBP table and inserted ~33 rows again, so the table grew without bound
  and lookups returned the oldest rate.
- **Structure:** no layers, no dependency management, a new DB connection in
  every method, no tests. Money handled as floats.

## Decision drivers

- The application must reach production quality: a real API, a sound data
  model, automated tests, CI and deployment.
- Existing functionality must not be silently dropped.
- Effort should go into the new design, not into preserving code that is wrong.

## Considered options

1. Incremental refactor of the PHP code
2. Rewrite on a new stack, keeping the domain and the features

## Decision

Chosen option: **rewrite**. Almost no part of the prototype is worth keeping
beyond the domain idea: the data model, the conversion logic and the request
handling all need to change. A refactor would end up replacing every file while
carrying the old structure along.

The original code is preserved under the git tag
[`legacy-php`](../../../../tree/legacy-php) and removed from the main branch in
a single commit.

## Consequences

**Positive**

- Clean data model and architecture from the start (see ADRs 0003–0007).
- Each defect above becomes an explicit requirement with a test in the new
  code (correct conversion formula, parameterised queries via the ORM,
  idempotent sync, decimal money).

**Negative / trade-offs**

- Nothing works end to end until the new sync and conversion features land.
- Features to re-deliver: NBP rates sync, rates table, currency conversion.

## Links

- [0003. Monorepo with npm workspaces](0003-monorepo-with-npm-workspaces.md)
- [NBP Web API](https://api.nbp.pl/)
