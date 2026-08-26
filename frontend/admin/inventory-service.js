/**
 * DineFlow AI - Admin Inventory Stock Manager Service
 * Handles real-time stock deduction, reorder threshold alerts, and supplier logging.
 *
 * @author Fiyans Patel <patelfiyans315@gmail.com>
 * @version 1.0.0
 */
class InventoryService {
  constructor(apiBase = '/api/inventory') {
    this.apiBase = apiBase;
  }

  async fetchAllStock(restaurantId = null) {
    const rId = restaurantId || window.AUTH_USER_RESTAURANT_ID || '1';
    try {
      const response = await fetch(`${this.apiBase}?restaurantId=${rId}`);
      if (!response.ok) throw new Error('Failed to fetch stock records');
      return await response.json();
    } catch (err) {
      console.error('[InventoryService] fetchAllStock error:', err);
      return { success: false, data: [] };
    }
  }

  calculateValuation(stockItems) {
    return (stockItems || []).reduce((acc, item) => {
      const qty = parseFloat(item.current_stock || item.quantity || 0);
      const cost = parseFloat(item.unit_cost || item.cost_per_unit || 0);
      return acc + (qty * cost);
    }, 0);
  }

  filterLowStock(stockItems) {
    return (stockItems || []).filter(item => {
      const current = parseFloat(item.current_stock || 0);
      const threshold = parseFloat(item.min_threshold || 5);
      return current <= threshold;
    });
  }
}

if (typeof module !== 'undefined' && module.exports) {
  module.exports = InventoryService;
}
