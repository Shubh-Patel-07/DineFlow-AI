import { spawn } from 'child_process';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const projectRoot = path.join(__dirname, '..');

const php = spawn(
    'php',
    [
        'artisan',
        'serve',
        '--host=127.0.0.1',
        '--port=8000'
    ],
    {
        cwd: projectRoot,
        shell: true,
        stdio: 'inherit'
    }
);

php.on('error', (error) => {
    console.error('Laravel could not start:', error);
});

php.on('close', (code) => {
    console.log(`Laravel server stopped with code ${code}`);
});