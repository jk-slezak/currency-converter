# 0010. Representing money and exchange rates

- **Status:** Proposed
- **Date:** 2026-11-23

## Context and problem

Currency conversion is the core feature, and the prototype got it wrong in
three ways: an inverted formula, floating-point arithmetic, and a `DECIMAL(10, 4)`
column too narrow for NBP rates (see [0002](0002-rewrite-php-prototype.md)).

Facts from the NBP API that shape the decision:

- Table A publishes **mid** rates as **PLN per 1 unit** of a foreign currency.
  PLN itself is not in the table.
- Rates have between 4 and **8** decimal places. In table 227/A/NBP/2026,
  25 of 32 rates had 4 decimals, and IDR had 8 (`0.00022317`).
- Tables are published on business days only. Weekends and public holidays
  have no table.

Money values cross three boundaries: PostgreSQL, the API (TypeScript), and
JSON to the browser. A JavaScript `number` is an IEEE 754 float:
`0.1 + 0.2 !== 0.3`, and precision is silently lost at every step.

## Decision drivers

- Correct results, reproducible by hand from the published NBP table.
- No floating-point arithmetic on money or rates anywhere.
- A user can see which official table a result is based on.
- One place where conversion is computed.

## Considered options

1. JavaScript `number` everywhere
2. Integer minor units (e.g. grosze, cents) for amounts
3. Arbitrary-precision decimals end to end, serialized as strings in JSON

## Decision

Chosen option: **arbitrary-precision decimals end to end, strings in JSON**.

**Calculation**

- PLN is the implicit base currency with a rate of exactly `1`.
- `result = amount × mid(source) ÷ mid(target)` (a cross rate via PLN).
  PLN → X and X → PLN are the same formula with one side equal to `1`.
- Computed in the API only, with `Prisma.Decimal` (decimal.js, already part of
  Prisma, so no new dependency) at full precision. Rounding happens **once**,
  on the final result, to the target currency's ISO 4217 minor units
  (e.g. 2 for EUR, 0 for JPY), using half-up rounding.

**Storage:** `NUMERIC(18, 8)` for rates (see [0005](0005-postgresql-with-prisma.md)).

**Transport:** amounts and rates are JSON **strings** (`"4.25120000"`), never
numbers. Request schemas (Zod) accept only positive decimal strings with a
bounded number of digits. Responses include the `effectiveDate` and NBP
`tableNo` used, plus the applied cross rate.

**Dates:** for a requested date without a table (weekend, holiday), the latest
table published **on or before** that date is used, and its date is returned.
A date before the earliest stored table is a `404`, not a guess.

**Frontend:** never does arithmetic on money. It displays API strings with
`Intl.NumberFormat`, which formats string input without converting it to a
float.

Option 1 is the bug class this rewrite exists to remove. Option 2 is a good
fit for storing amounts (ledgers, payments), but exchange rates are not amounts:
they need up to 8 fractional digits. Mixing integer amounts with fractional
rates would still require decimal arithmetic for every conversion.

## Consequences

**Positive**

- Every result can be checked by hand against the official NBP table cited in
  the response.
- A single implementation of the formula, covered by unit tests with known NBP
  values (including an 8-decimal currency and PLN on either side).

**Negative / trade-offs**

- Strings in JSON are less convenient for API consumers than numbers. They
  must parse them with a decimal library if they want to calculate.
- The frontend cannot convert instantly while the user types. Every
  conversion is an API call, mitigated by debouncing and caching in TanStack Query.
- Minor-unit metadata per currency must be maintained (for the ~32 table A
  currencies).

## Links

- [NBP Web API](https://api.nbp.pl/)
- [ISO 4217 currency codes and minor units](https://www.iso.org/iso-4217-currency-codes.html)
