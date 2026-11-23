# 0001. Record architecture decisions

- **Status:** Accepted
- **Date:** 2026-11-23

## Context and problem

The project is being rewritten from a PHP prototype into a NestJS + React
application. Many choices (framework, ORM, repository layout, dependency
policy) are made in a short time and each has non-obvious reasons. Without a
written record, those reasons are lost: future maintainers cannot tell whether
a choice was deliberate or accidental, and reviewers cannot evaluate the
trade-offs.

## Decision drivers

- The reasoning must live next to the code and be reviewed in the same pull requests.
- Writing a record must be cheap, otherwise it will not happen.
- Decisions change; the history of _why_ they changed must stay readable.

## Considered options

1. Architecture Decision Records in the repository (Markdown, MADR-style)
2. A wiki or external document (Confluence, Notion)
3. Code comments and commit messages only

## Decision

Chosen option: **ADRs in `docs/adr/`**, one decision per file, numbered
sequentially, using a lightweight [MADR](https://adr.github.io/madr/)-style
template ([`template.md`](template.md)).

An accepted ADR is never rewritten. When a decision changes, a new ADR is added
and the old one is marked `Superseded by NNNN`.

## Consequences

**Positive**

- Decisions are versioned and reviewed together with the code they affect.
- New contributors get the "why" in a few minutes of reading.

**Negative / trade-offs**

- Small ongoing cost: every significant decision needs a short document.
- Records can go stale if superseding is not done consistently.

## Links

- [ADR index](README.md)
- Michael Nygard, [Documenting Architecture Decisions](https://cognitect.com/blog/2011/11/15/documenting-architecture-decisions)
