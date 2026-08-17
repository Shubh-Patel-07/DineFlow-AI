/**
 * DineFlow AI Enterprise JavaScript / TypeScript SDK Client
 * Universal client library supporting Browser, Node.js, and Electron runtimes.
 *
 * @version 1.0.0
 * @author Shubh Patel <shubhmpatel77@gmail.com>
 */
class DineFlowSDK {
  /**
   * @param {Object} config
   * @param {string} [config.baseUrl='http://127.0.0.1:8000']
   * @param {string} [config.restaurantId='1']
   * @param {string} [config.apiKey=null]
   * @param {number} [config.timeout=15000]
   */
  constructor(config = {}) {
    this.baseUrl = (config.baseUrl || 'http://127.0.0.1:8000').replace(/\/+$/, '');
    this.restaurantId = String(config.restaurantId || '1');
    this.apiKey = config.apiKey || null;
    this.timeout = config.timeout || 15000;
  }

  /**
   * Internal HTTP Request Wrapper
   * @private
   */
  async _request(endpoint, options = {}) {
    const url = `${this.baseUrl}/api/${endpoint.replace(/^\/+/, '')}`;
    const headers = {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      ...(options.headers || {})
    };

    if (this.apiKey) {
      headers['Authorization'] = `Bearer ${this.apiKey}`;
    }

    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), this.timeout);

    try {
      const response = await fetch(url, {
        ...options,
        headers,
        signal: controller.signal
      });

      clearTimeout(timeoutId);
      const payload = await response.json().catch(() => ({}));

      if (!response.ok) {
        throw new Error(payload.message || `DineFlow API Error: Status ${response.status}`);
      }

      return payload;
    } catch (err) {
      clearTimeout(timeoutId);
      throw err;
    }
  }

  // ==========================================
  // MENU OPERATIONS
  // ==========================================

  async getMenu(category = null) {
    let path = `menu?restaurantId=${encodeURIComponent(this.restaurantId)}`;
    if (category) path += `&category=${encodeURIComponent(category)}`;
    return this._request(path);
  }

  async createMenuItem(item) {
    return this._request('menu', {
      method: 'POST',
      body: JSON.stringify({ ...item, restaurant_id: this.restaurantId })
    });
  }

  async updateMenuItem(id, updates) {
    return this._request(`menu/${id}`, {
      method: 'PUT',
      body: JSON.stringify(updates)
    });
  }

  async deleteMenuItem(id) {
    return this._request(`menu/${id}`, { method: 'DELETE' });
  }

  // ==========================================
  // ORDER OPERATIONS
  // ==========================================

  async placeOrder(tableId, items, paymentMethod = 'cash', instructions = '') {
    return this._request('orders', {
      method: 'POST',
      body: JSON.stringify({
        restaurant_id: this.restaurantId,
        table_id: String(tableId),
        payment_method: paymentMethod,
        special_instructions: instructions,
        items
      })
    });
  }

  async getOrderStatus(orderId) {
    return this._request(`orders/${orderId}/status`);
  }

  // ==========================================
  // TABLE & SERVICE CALLS
  // ==========================================

  async getTables() {
    return this._request(`tables/restaurant/${this.restaurantId}`);
  }

  async callWaiter(tableId, type = 'service') {
    return this._request('waiter-calls', {
      method: 'POST',
      body: JSON.stringify({
        restaurant_id: this.restaurantId,
        table_id: String(tableId),
        request_type: type
      })
    });
  }

  // ==========================================
  // ANALYTICS & REPORTS
  // ==========================================

  async getSalesAnalytics() {
    return this._request(`analytics/sales/${this.restaurantId}`);
  }
}

if (typeof module !== 'undefined' && module.exports) {
  module.exports = DineFlowSDK;
}
