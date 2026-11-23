-- CreateEnum
CREATE TYPE "sync_status" AS ENUM ('RUNNING', 'SUCCEEDED', 'NO_DATA', 'FAILED');

-- CreateTable
CREATE TABLE "currencies" (
    "code" CHAR(3) NOT NULL,
    "name" TEXT NOT NULL,

    CONSTRAINT "currencies_pkey" PRIMARY KEY ("code")
);

-- CreateTable
CREATE TABLE "rate_tables" (
    "id" SERIAL NOT NULL,
    "table_no" TEXT NOT NULL,
    "effective_date" DATE NOT NULL,
    "fetched_at" TIMESTAMPTZ(3) NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT "rate_tables_pkey" PRIMARY KEY ("id")
);

-- CreateTable
CREATE TABLE "exchange_rates" (
    "id" SERIAL NOT NULL,
    "currency_code" CHAR(3) NOT NULL,
    "rate_table_id" INTEGER NOT NULL,
    "mid" DECIMAL(18,6) NOT NULL,

    CONSTRAINT "exchange_rates_pkey" PRIMARY KEY ("id")
);

-- CreateTable
CREATE TABLE "sync_runs" (
    "id" SERIAL NOT NULL,
    "started_at" TIMESTAMPTZ(3) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    "finished_at" TIMESTAMPTZ(3),
    "status" "sync_status" NOT NULL DEFAULT 'RUNNING',
    "rates_upserted" INTEGER NOT NULL DEFAULT 0,
    "error" TEXT,

    CONSTRAINT "sync_runs_pkey" PRIMARY KEY ("id")
);

-- CreateIndex
CREATE UNIQUE INDEX "rate_tables_table_no_key" ON "rate_tables"("table_no");

-- CreateIndex
CREATE UNIQUE INDEX "rate_tables_effective_date_key" ON "rate_tables"("effective_date");

-- CreateIndex
CREATE INDEX "exchange_rates_rate_table_id_idx" ON "exchange_rates"("rate_table_id");

-- CreateIndex
CREATE UNIQUE INDEX "exchange_rates_currency_code_rate_table_id_key" ON "exchange_rates"("currency_code", "rate_table_id");

-- AddForeignKey
ALTER TABLE "exchange_rates" ADD CONSTRAINT "exchange_rates_currency_code_fkey" FOREIGN KEY ("currency_code") REFERENCES "currencies"("code") ON DELETE RESTRICT ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE "exchange_rates" ADD CONSTRAINT "exchange_rates_rate_table_id_fkey" FOREIGN KEY ("rate_table_id") REFERENCES "rate_tables"("id") ON DELETE CASCADE ON UPDATE CASCADE;
