/*
  Warnings:

  - You are about to alter the column `mid` on the `exchange_rates` table. The data in that column could be lost. The data in that column will be cast from `Decimal(18,6)` to `Decimal(18,8)`.

*/
-- AlterTable
ALTER TABLE "exchange_rates" ALTER COLUMN "mid" SET DATA TYPE DECIMAL(18,8);
