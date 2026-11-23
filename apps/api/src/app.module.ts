import { resolve } from 'node:path';
import { Module } from '@nestjs/common';
import { ConfigModule } from '@nestjs/config';
import { validateEnv } from './config/env.js';
import { HealthModule } from './health/health.module.js';
import { PrismaModule } from './prisma/prisma.module.js';

const repoRootEnvFile = resolve(import.meta.dirname, '../../../.env');

@Module({
  imports: [
    ConfigModule.forRoot({
      isGlobal: true,
      envFilePath: repoRootEnvFile,
      validate: validateEnv,
    }),
    PrismaModule,
    HealthModule,
  ],
})
export class AppModule {}
