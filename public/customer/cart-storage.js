/**
 * DineFlow AI - Customer Cart Local Storage & Persistence Handler
 * Ensures offline basket preservation and seamless tab synchronization.
 *
 * @author veer prajapati <veerprajapati2009@gmail.com>
 * @version 1.0.0
 */
class CartStorageHandler {
  static getStorageKey() {
    const params = new URLSearchParams(window.location.search);
    const rId = params.get('restaurantId') || params.get('restaurant_id') || '1';
    return `dineflow_customer_cart_${rId}`;
  }

  static getCart() {
    try {
      const data = localStorage.getItem(this.getStorageKey());
      return data ? JSON.parse(data) : [];
    } catch (e) {
      console.error('[CartStorageHandler] Read error:', e);
      return [];
    }
  }

  static saveCart(items) {
    try {
      localStorage.setItem(this.getStorageKey(), JSON.stringify(items || []));
      window.dispatchEvent(new CustomEvent('dineflow:cart_updated', { detail: { count: (items || []).length } }));
    } catch (e) {
      console.error('[CartStorageHandler] Save error:', e);
    }
  }

  static clearCart() {
    try {
      localStorage.removeItem(this.getStorageKey());
      window.dispatchEvent(new CustomEvent('dineflow:cart_cleared'));
    } catch (e) {
      console.error('[CartStorageHandler] Clear error:', e);
    }
  }
}

if (typeof module !== 'undefined' && module.exports) {
  module.exports = CartStorageHandler;
}
