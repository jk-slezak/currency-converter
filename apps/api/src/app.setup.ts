import type { INestApplication } from '@nestjs/common';

export const API_GLOBAL_PREFIX = 'api';

export function configureApp(app: INestApplication): INestApplication {
  app.setGlobalPrefix(API_GLOBAL_PREFIX);
  app.enableShutdownHooks();
  return app;
}
