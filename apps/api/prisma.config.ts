import { existsSync } from 'node:fs';
import { resolve } from 'node:path';
import { defineConfig } from 'prisma/config';

const repoRootEnvFile = resolve(import.meta.dirname, '../../.env');
if (existsSync(repoRootEnvFile)) {
  process.loadEnvFile(repoRootEnvFile);
}

export default defineConfig({
  schema: 'prisma/schema.prisma',
  migrations: {
    path: 'prisma/migrations',
  },
  datasource: {
    url: process.env.DATABASE_URL,
  },
});
