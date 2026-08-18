import initSqlJs from 'sql.js';
import fs from 'fs';
import path from 'path';
import { app } from 'electron';
import { createRequire } from 'module';
import { migrations } from './migrations.js';
import { fileURLToPath } from 'url';

const require = createRequire(import.meta.url);
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const DB_SUBPATH = path.join('database', 'dineflow-local.sqlite');

let SQL;
let db;

function resolveDatabasePath() {
  const userDataPath = app.getPath('userData');
  return path.join(userDataPath, DB_SUBPATH);
}

function ensureDatabaseDirectory(databasePath) {
  const dir = path.dirname(databasePath);
  if (!fs.existsSync(dir)) {
    fs.mkdirSync(dir, { recursive: true });
  }
}

function locateFile(file) {
  const sqlJsPath = path.dirname(require.resolve('sql.js'));
  return path.join(sqlJsPath, file);
}

async function openDatabaseAsync() {
  if (!SQL) {
    SQL = await initSqlJs({ locateFile });
  }

  const databasePath = resolveDatabasePath();
  console.log('Local DB path:', databasePath);
  ensureDatabaseDirectory(databasePath);

  if (fs.existsSync(databasePath)) {
    console.log('Existing local DB found at:', databasePath);
    const fileBuffer = fs.readFileSync(databasePath);
    db = new SQL.Database(new Uint8Array(fileBuffer));
  } else {
    console.log('No existing local DB file found; creating a new database.');
    db = new SQL.Database();
  }

  db.run('PRAGMA foreign_keys = ON;');
  return db;
}

function persistDatabase() {
  if (!db) return;
  const databasePath = resolveDatabasePath();
  console.log('Persisting local DB to:', databasePath);
  const data = db.export();
  const buffer = Buffer.from(data);
  fs.writeFileSync(databasePath, buffer);
}

function getCurrentMigrationVersion() {
  const stmt = db.prepare(`SELECT value FROM local_meta WHERE key = 'schema_version'`);
  const row = stmt.getAsObject();
  return row.value ? Number(row.value) : 0;
}

function setCurrentMigrationVersion(version) {
  db.exec(`INSERT INTO local_meta (key, value) VALUES ('schema_version', '${version}')
    ON CONFLICT(key) DO UPDATE SET value = excluded.value;`);
}

export async function initializeDatabase() {
  await openDatabaseAsync();

  if (!db.exec(`SELECT name FROM sqlite_master WHERE type='table' AND name='local_meta';`).length) {
    db.run(`CREATE TABLE IF NOT EXISTS local_meta (
      key TEXT PRIMARY KEY,
      value TEXT NOT NULL
    );`);
  }

  let currentVersion = getCurrentMigrationVersion();
  for (const migration of migrations) {
    if (migration.version > currentVersion) {
      migration.up(db);
      setCurrentMigrationVersion(migration.version);
      currentVersion = migration.version;
    }
  }

  persistDatabase();
  return db;
}

export function getDatabasePath() {
  return resolveDatabasePath();
}

export function save() {
  persistDatabase();
}
