"""
DineFlow AI Enterprise Python SDK Client
Official Python client library for analytics automation, machine learning forecasting, and POS integration.

Author: Shubh Patel <shubhmpatel77@gmail.com>
Version: 1.0.0
"""

import requests
from typing import Dict, List, Optional, Any


class DineFlowClient:
    """Enterprise client for DineFlow AI REST APIs."""

    def __init__(self, base_url: str = "http://127.0.0.1:8000", api_key: Optional[str] = None, restaurant_id: str = "1", timeout: int = 15):
        self.base_url = base_url.rstrip("/")
        self.api_key = api_key
        self.restaurant_id = str(restaurant_id)
        self.timeout = timeout
        self.session = requests.Session()
        self.session.headers.update({
            "Accept": "application/json",
            "Content-Type": "application/json"
        })
        if self.api_key:
            self.session.headers["Authorization"] = f"Bearer {self.api_key}"

    def _request(self, method: str, endpoint: str, **kwargs) -> Dict[str, Any]:
        url = f"{self.base_url}/api/{endpoint.lstrip('/')}"
        response = self.session.request(method, url, timeout=self.timeout, **kwargs)
        response.raise_for_status()
        return response.json()

    # ----------------------------------------
    # Menu Endpoints
    # ----------------------------------------

    def get_menu(self, category: Optional[str] = None) -> List[Dict[str, Any]]:
        """Retrieve menu catalogue."""
        params = {"restaurantId": self.restaurant_id}
        if category:
            params["category"] = category
        res = self._request("GET", "menu", params=params)
        return res.get("data", [])

    def create_menu_item(self, item_data: Dict[str, Any]) -> Dict[str, Any]:
        """Create new menu item."""
        item_data["restaurant_id"] = self.restaurant_id
        return self._request("POST", "menu", json=item_data)

    def update_menu_item(self, item_id: int, updates: Dict[str, Any]) -> Dict[str, Any]:
        """Update existing menu item."""
        return self._request("PUT", f"menu/{item_id}", json=updates)

    def delete_menu_item(self, item_id: int) -> Dict[str, Any]:
        """Delete menu item."""
        return self._request("DELETE", f"menu/{item_id}")

    # ----------------------------------------
    # Order Endpoints
    # ----------------------------------------

    def place_order(self, table_id: str, items: List[Dict[str, Any]], payment_method: str = "cash", notes: str = "") -> Dict[str, Any]:
        """Submit new customer or POS order."""
        payload = {
            "restaurant_id": self.restaurant_id,
            "table_id": str(table_id),
            "payment_method": payment_method,
            "special_instructions": notes,
            "items": items
        }
        return self._request("POST", "orders", json=payload)

    def get_order_status(self, order_id: int) -> Dict[str, Any]:
        """Poll real-time order status."""
        return self._request("GET", f"orders/{order_id}/status")

    # ----------------------------------------
    # Analytics Endpoints
    # ----------------------------------------

    def get_sales_analytics(self) -> Dict[str, Any]:
        """Fetch high-level sales and revenue summary."""
        return self._request("GET", f"analytics/sales/{self.restaurant_id}")
