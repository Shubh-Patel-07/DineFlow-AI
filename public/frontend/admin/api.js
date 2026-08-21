/**
 * DineFlow AI - API Service
 */
const API_BASE = window.DINEFLOW_API_BASE || `${window.location.origin}/api`;

function getCSRFToken() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

const APIService = {
  async getTables() {
    try {
      const response = await fetch(`${API_BASE}/tables/restaurant/1`);
      if (!response.ok) throw new Error('Failed to fetch tables');
      return await response.json();
    } catch (err) {
      return { success: false, error: err.message };
    }
  },

  async addTable(tableNumber) {
    try {
      const response = await fetch(`${API_BASE}/tables`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': getCSRFToken()
        },
        body: JSON.stringify({
          restaurant_id: '1',
          table_number: tableNumber
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

  async getFeedback() {
    try {
      const response = await fetch(`${API_BASE}/feedback/restaurant/1`);
      if (!response.ok) throw new Error('Failed to fetch feedback');
      return await response.json();
    } catch (err) {
      return { success: false, error: err.message };
    }
  }
};
