# Security

## Dependency policy

- All direct dependencies are pinned to exact versions (`save-exact=true` in `.npmrc`).
- npm only installs versions published at least 7 days ago (`min-release-age=7`),
  which keeps freshly compromised releases out of the dependency tree.
- `engine-strict=true` with `engines.npm >= 11.20.0`: older npm versions, which
  would silently ignore `min-release-age`, refuse to install.
- `npm audit` overrides are not used: fixes come from upstream releases.
- Rationale: [ADR 0008](docs/adr/0008-dependency-policy.md).

## Known advisories (accepted risk)

| Advisory                                                                               | Package         | Path                                      | Status                                                        |
| -------------------------------------------------------------------------------------- | --------------- | ----------------------------------------- | ------------------------------------------------------------- |
| [GHSA-7589-6fvf-x4pw](https://github.com/advisories/GHSA-7589-6fvf-x4pw) (high, ReDoS) | `effect@3.20.0` | `prisma@7.10.1` → `@prisma/config@7.10.1` | Accepted until Prisma ships a release with `effect >= 3.22.1` |

**Why it is not exploitable here:** `effect` is used only by the Prisma CLI to
parse `apps/api/prisma.config.ts`, a file we write and commit. The vulnerable
code path (`Schema.TemplateLiteral`) needs attacker-controlled input, and no
request data ever reaches it. The application runtime (`@prisma/client` +
`@prisma/adapter-pg`) does not load `effect`.

**Why not fixed yet:** `@prisma/config@7.10.1` pins `effect` to exactly `3.20.0`;
7.10.1 is the latest stable Prisma release and `npm audit fix --force` would
downgrade to Prisma 6. Re-check on every Prisma release and remove this entry
once the advisory is resolved.

_Last reviewed: 2026-11-23._

## Reporting a vulnerability

Please open a [private security advisory](../../security/advisories/new)
instead of a public issue.
