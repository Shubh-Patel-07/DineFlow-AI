/**
 * DineFlow AI - API Service
 */
const API_BASE = window.DINEFLOW_API_BASE || `${window.location.origin}/api`;

function getCSRFToken() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

function getAuthHeaders() {
  const token = localStorage.getItem('token') || localStorage.getItem('dineflow_token');
  const headers = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-CSRF-TOKEN': getCSRFToken()
  };
  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }
  return headers;
}

function getCurrentRestaurantId() {
  return window.AUTH_USER_RESTAURANT_ID || new URLSearchParams(window.location.search).get('restaurant_id') || new URLSearchParams(window.location.search).get('restaurantId') || '1';
}

const APIService = {
  async getTables(restaurantId = null) {
    const rId = restaurantId || getCurrentRestaurantId();
    try {
      const response = await fetch(`${API_BASE}/tables/restaurant/${rId}`, {
        headers: getAuthHeaders()
      });
      if (!response.ok) throw new Error('Failed to fetch tables');
      return await response.json();
    } catch (err) {
      return { success: false, error: err.message };
    }
  },

  async addTable(tableNumber, restaurantId = null) {
    const rId = restaurantId || getCurrentRestaurantId();
    try {
      const response = await fetch(`${API_BASE}/tables`, {
        method: 'POST',
        headers: getAuthHeaders(),
        body: JSON.stringify({
          restaurant_id: String(rId),
          table_number: String(tableNumber)
        })
      });
      if (!response.ok) throw new Error('Failed to add table');
      return await response.json();
    } catch (err) {
      return { success: false, error: err.message };
    }
  },

  async deleteTable(tableId) {
    try {
      const response = await fetch(`${API_BASE}/tables/${tableId}`, {
        method: 'DELETE',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': getCSRFToken()
        }
      });
      if (!response.ok) throw new Error('Failed to delete table');
      return await response.json();
    } catch (err) {
      return { success: false, error: err.message };
    }
  },

  async getFeedback(restaurantId = null) {
    const rId = restaurantId || getCurrentRestaurantId();
    try {
      const response = await fetch(`${API_BASE}/feedback/restaurant/${rId}`, {
        headers: getAuthHeaders()
      });
      if (!response.ok) throw new Error('Failed to fetch feedback');
      return await response.json();
    } catch (err) {
      return { success: false, error: err.message };
    }
  }
};
