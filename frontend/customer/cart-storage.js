/**
 * DineFlow AI - Customer Cart Local Storage & Persistence Handler
 * Ensures offline basket preservation and seamless tab synchronization.
 *
 * @author veer prajapati <veerprajapati2009@gmail.com>
 * @version 1.0.0
 */
class CartStorageHandler {
  static STORAGE_KEY = 'dineflow_customer_cart';

  static getCart() {
    try {
      const data = localStorage.getItem(this.STORAGE_KEY);
      return data ? JSON.parse(data) : [];
    } catch (e) {
      console.error('[CartStorageHandler] Read error:', e);
      return [];
    }
  }

  static saveCart(items) {
    try {
      localStorage.setItem(this.STORAGE_KEY, JSON.stringify(items || []));
      window.dispatchEvent(new CustomEvent('dineflow:cart_updated', { detail: { count: (items || []).length } }));
    } catch (e) {
      console.error('[CartStorageHandler] Save error:', e);
    }
  }

  static clearCart() {
    try {
      localStorage.removeItem(this.STORAGE_KEY);
      window.dispatchEvent(new CustomEvent('dineflow:cart_cleared'));
    } catch (e) {
      console.error('[CartStorageHandler] Clear error:', e);
    }
  }
}

if (typeof module !== 'undefined' && module.exports) {
  module.exports = CartStorageHandler;
}
