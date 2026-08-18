import * as database from './database.js';
import * as menuRepo from './repositories/menuRepository.js';
import * as tableRepo from './repositories/tableRepository.js';
import * as customerRepo from './repositories/customerRepository.js';
import * as inventoryRepo from './repositories/inventoryRepository.js';
import * as orderRepo from './repositories/orderRepository.js';

let db;

export async function initialize() {
  if (!db) {
    db = await database.initializeDatabase();
  }
  return db;
}

export function getDatabasePath() {
  return database.getDatabasePath();
}

export async function getAllMenuItems() {
  return menuRepo.getAllMenuItems(await initialize());
}

export async function getMenuItemById(localId) {
  return menuRepo.getMenuItemById(await initialize(), localId);
}

export async function getAllTables() {
  return tableRepo.getAllTables(await initialize());
}

export async function getTableById(localId) {
  return tableRepo.getTableById(await initialize(), localId);
}

export async function getAllCustomers() {
  return customerRepo.getAllCustomers(await initialize());
}

export async function getCustomerById(localId) {
  return customerRepo.getCustomerById(await initialize(), localId);
}

export async function getInventory() {
  return inventoryRepo.getInventory(await initialize());
}

export async function getInventoryItemById(localId) {
  return inventoryRepo.getInventoryItemById(await initialize(), localId);
}

export async function createLocalOrder(order) {
  return orderRepo.createLocalOrder(await initialize(), order);
}

export async function getLocalOrderById(localId) {
  return orderRepo.getLocalOrderById(await initialize(), localId);
}

export async function getLocalOrders() {
  return orderRepo.getLocalOrders(await initialize());
}

export function save() {
  return database.save();
}
