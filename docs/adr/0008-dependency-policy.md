# 0008. Dependency policy: exact versions, 7-day release age, no overrides

- **Status:** Accepted
- **Date:** 2026-11-23

## Context and problem

The npm ecosystem regularly sees compromised releases: a maintainer account is
taken over and a malicious patch version is published, then picked up
automatically by every project using a `^` range. Such releases are usually
detected and removed within days. Separately, caret ranges make
`package.json` an imprecise description of what is actually installed.

## Decision drivers

- Reproducible installs. `package.json` states exactly what runs.
- A safety window against freshly published malicious versions.
- Security fixes should come from upstream releases, not local patching of the
  dependency tree.
- Every accepted risk must be visible and explained.

## Considered options

1. Caret ranges (`^`), rely on the lockfile
2. Exact versions with a minimum release age
3. Exact versions plus `overrides` to force patched transitive dependencies

## Decision

Chosen option: **exact versions with a minimum release age, and no
`overrides`**. Configured in the root [`.npmrc`](../../.npmrc):

```ini
save-exact=true
min-release-age=7
engine-strict=true
```

- `save-exact=true`: `npm install <pkg>` writes `"1.2.3"`, never `"^1.2.3"`.
- `min-release-age=7`: only versions published at least 7 days ago are installed.
- `engine-strict=true` together with `"engines": { "npm": ">=11.20.0" }` in the
  root `package.json`: an older npm does not know `min-release-age` and would
  ignore it with only a warning, silently disabling the policy. With
  `engine-strict`, such an npm refuses to install instead. 11.20.0 is the
  version this policy was verified with.

- Applies to the whole tree: transitive dependencies are also resolved only
  from versions older than 7 days.
- Upgrades are deliberate: pick the newest version that is at least 7 days old,
  update `package.json`, run the full verification.
- npm `overrides` are not used. They silently diverge from what upstream tested
  and are easy to forget.
- Advisories that cannot be fixed under these rules are documented as accepted
  risks in [`SECURITY.md`](../../SECURITY.md), with a justification and an exit
  condition.

## Consequences

**Positive**

- Identical installs on every machine and in CI. Diffs to `package.json` show
  real upgrades.
- A 7-day buffer against compromised releases.

**Negative / trade-offs**

- Security fixes arrive at least 7 days after publication, unless a package is
  explicitly exempted with `min-release-age-exclude`.
- Some advisories stay open until upstream releases a fix. Currently
  `effect@3.20.0` (GHSA-7589-6fvf-x4pw) via the Prisma CLI, see `SECURITY.md`.
  `npm audit` reports it, so the CI audit step must allow-list this single
  advisory by ID instead of being disabled.
- Phase 4 adds Renovate, configured with the same 7-day minimum release age,
  to propose upgrades automatically.
