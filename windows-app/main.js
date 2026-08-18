import { app, BrowserWindow, ipcMain } from 'electron';
import { spawn } from 'child_process';
import path from 'path';
import { fileURLToPath } from 'url';
import * as localDbManager from './database/manager.js';
import * as syncEngine from './sync/syncEngine.js';
import { setOnline } from './sync/networkMonitor.js';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

let mainWindow;
let laravelProcess = null;

// Electron cache ko project ke dedicated folder mein rakho
app.setPath('cache', path.join(__dirname, 'cache'));

function createWindow() {
    mainWindow = new BrowserWindow({
        width: 1400,
        height: 900,
        minWidth: 1000,
        minHeight: 700,
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true,
            preload: path.join(__dirname, 'preload.js')
        },
        show: true
    });

    // Laravel application
    mainWindow.loadURL('http://127.0.0.1:8000');

    // DevTools abhi debugging ke liye useful hain
    // mainWindow.webContents.openDevTools();

    mainWindow.on('closed', () => {
        mainWindow = null;
    });
}

app.whenReady().then(async () => {
    try {
        await localDbManager.initialize();
    } catch (error) {
        console.error('Failed to initialize local SQLite database:', error);
    }

    setOnline(true);
    const localDb = await localDbManager.initialize();
    syncEngine.processQueue(localDb).catch(err => console.error('Initial sync engine error:', err));
    setInterval(() => syncEngine.processQueue(localDb), 10000);

    laravelProcess = spawn(
        'node',
        [path.join(__dirname, 'start-laravel.js')],
        {
            cwd: path.join(__dirname, '..'),
            shell: true,
            stdio: 'inherit'
        }
    );

    setTimeout(() => {
        createWindow();
    }, 3000);

    app.on('activate', () => {
        if (BrowserWindow.getAllWindows().length === 0) {
            createWindow();
        }
    });
});

ipcMain.handle('local-db-initialize', async () => {
  try {
    await localDbManager.initialize();
    return { success: true, path: localDbManager.getDatabasePath() };
  } catch (error) {
    console.error('IPC local-db-initialize error:', error);
    return { success: false, error: error.message };
  }
});

ipcMain.handle('local-db-path', () => {
  return localDbManager.getDatabasePath();
});

ipcMain.handle('local-db-get-all-menu-items', () => {
  return localDbManager.getAllMenuItems();
});

ipcMain.handle('local-db-get-menu-item-by-id', (_, id) => {
  return localDbManager.getMenuItemById(id);
});

ipcMain.handle('local-db-get-all-tables', () => {
  return localDbManager.getAllTables();
});

ipcMain.handle('local-db-get-table-by-id', (_, id) => {
  return localDbManager.getTableById(id);
});

ipcMain.handle('local-db-get-all-customers', () => {
  return localDbManager.getAllCustomers();
});

ipcMain.handle('local-db-get-customer-by-id', (_, id) => {
  return localDbManager.getCustomerById(id);
});

ipcMain.handle('local-db-get-inventory', () => {
  return localDbManager.getInventory();
});

ipcMain.handle('local-db-get-inventory-item-by-id', (_, id) => {
  return localDbManager.getInventoryItemById(id);
});

ipcMain.handle('local-db-create-local-order', (_, order) => {
  return localDbManager.createLocalOrder(order);
});

ipcMain.handle('local-db-get-local-order-by-id', (_, id) => {
  return localDbManager.getLocalOrderById(id);
});

ipcMain.handle('local-db-get-local-orders', () => {
  return localDbManager.getLocalOrders();
});

app.on('window-all-closed', () => {
    if (laravelProcess) {
        laravelProcess.kill();
        laravelProcess = null;
    }

    if (process.platform !== 'darwin') {
        app.quit();
    }
});