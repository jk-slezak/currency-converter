import { DEFAULT_PORT, validateEnv } from './env.js';

const validEnv = {
  DATABASE_URL: 'postgresql://app:app@localhost:5432/currency_converter',
};

describe('validateEnv', () => {
  it('applies defaults for optional variables', () => {
    expect(validateEnv(validEnv)).toEqual({
      NODE_ENV: 'development',
      PORT: DEFAULT_PORT,
      DATABASE_URL: validEnv.DATABASE_URL,
    });
  });

  it('coerces PORT from string, as it always arrives from process.env', () => {
    expect(validateEnv({ ...validEnv, PORT: '8080' }).PORT).toBe(8080);
  });

  it('accepts the short postgres:// scheme', () => {
    const url = 'postgres://app:app@db:5432/app';
    expect(validateEnv({ DATABASE_URL: url }).DATABASE_URL).toBe(url);
  });

  it('rejects a missing DATABASE_URL', () => {
    expect(() => validateEnv({})).toThrow(/DATABASE_URL/);
  });

  it('rejects a non-PostgreSQL connection string', () => {
    expect(() =>
      validateEnv({ DATABASE_URL: 'mysql://root@localhost/test_db' }),
    ).toThrow(/DATABASE_URL/);
  });

  it('rejects an out-of-range PORT', () => {
    expect(() => validateEnv({ ...validEnv, PORT: '70000' })).toThrow(/PORT/);
  });

  it('does not leak the connection string in the error message', () => {
    let message = '';
    try {
      validateEnv({ DATABASE_URL: 'mysql://admin:s3cret@prod-host/db' });
    } catch (error) {
      message = (error as Error).message;
    }
    expect(message).toContain('DATABASE_URL');
    expect(message).not.toContain('s3cret');
  });
});
