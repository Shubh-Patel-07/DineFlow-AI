import { contextBridge, ipcRenderer } from 'electron';

contextBridge.exposeInMainWorld('localDb', {
  initialize: () => ipcRenderer.invoke('local-db-initialize'),
  getDatabasePath: () => ipcRenderer.invoke('local-db-path'),
  getAllMenuItems: () => ipcRenderer.invoke('local-db-get-all-menu-items'),
  getMenuItemById: (id) => ipcRenderer.invoke('local-db-get-menu-item-by-id', id),
  getAllTables: () => ipcRenderer.invoke('local-db-get-all-tables'),
  getTableById: (id) => ipcRenderer.invoke('local-db-get-table-by-id', id),
  getAllCustomers: () => ipcRenderer.invoke('local-db-get-all-customers'),
  getCustomerById: (id) => ipcRenderer.invoke('local-db-get-customer-by-id', id),
  getInventory: () => ipcRenderer.invoke('local-db-get-inventory'),
  getInventoryItemById: (id) => ipcRenderer.invoke('local-db-get-inventory-item-by-id', id),
  createLocalOrder: (order) => ipcRenderer.invoke('local-db-create-local-order', order),
  getLocalOrderById: (id) => ipcRenderer.invoke('local-db-get-local-order-by-id', id),
  getLocalOrders: () => ipcRenderer.invoke('local-db-get-local-orders'),
});
