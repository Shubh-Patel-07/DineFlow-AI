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

const APIService = {
  async getTables(restaurantId = '1') {
    try {
      const response = await fetch(`${API_BASE}/tables/restaurant/${restaurantId}`, {
        headers: getAuthHeaders()
      });
      if (!response.ok) throw new Error('Failed to fetch tables');
      return await response.json();
    } catch (err) {
      return { success: false, error: err.message };
    }
  },

  async addTable(tableNumber, restaurantId = '1') {
    try {
      const response = await fetch(`${API_BASE}/tables`, {
        method: 'POST',
        headers: getAuthHeaders(),
        body: JSON.stringify({
          restaurant_id: String(restaurantId),
          restaurantId: String(restaurantId),
          table_number: String(tableNumber),
          tableNumber: String(tableNumber)
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

  async getFeedback(restaurantId = '1') {
    try {
      const response = await fetch(`${API_BASE}/feedback/restaurant/${restaurantId}`, {
        headers: getAuthHeaders()
      });
      if (!response.ok) throw new Error('Failed to fetch feedback');
      return await response.json();
    } catch (err) {
      return { success: false, error: err.message };
    }
  }
};
