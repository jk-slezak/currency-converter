import type { ConfigService } from '@nestjs/config';
import type { Env } from '../config/env.js';
import { PrismaService } from './prisma.service.js';

const UNREACHABLE_DATABASE_URL = 'postgresql://app:app@127.0.0.1:1/app';

function createService(databaseUrl: string): PrismaService {
  const config = {
    get: () => databaseUrl,
  } as unknown as ConfigService<Env, true>;
  return new PrismaService(config);
}

describe('PrismaService', () => {
  it('fails on module init when the database is unreachable', async () => {
    const service = createService(UNREACHABLE_DATABASE_URL);

    try {
      await expect(service.onModuleInit()).rejects.toThrow();
    } finally {
      await service.$disconnect();
    }
  });
});
