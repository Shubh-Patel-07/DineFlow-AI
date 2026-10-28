import { API_CONFIG } from "../config/api";

const BASE_URL = API_CONFIG.BASE_URL;

// Default to SMP restaurant (ID 10) and Table 1 (ID 37)
export const DEFAULT_RESTAURANT_ID = 10;
export const DEFAULT_TABLE_ID = 37;

/**
 * Fetch Restaurant Profile
 */
export async function fetchRestaurantDetails(restaurantId = DEFAULT_RESTAURANT_ID) {
    try {
        const res = await fetch(`${BASE_URL}/restaurants/${restaurantId}`);
        const data = await res.json();
        return data.success ? data.data : null;
    } catch (err) {
        console.warn("Error fetching restaurant details:", err);
        return null;
    }
}

/**
 * Fetch Real Menu Items & Categories from DineFlow AI
 */
export async function fetchLiveMenu(restaurantId = DEFAULT_RESTAURANT_ID) {
    try {
        const res = await fetch(`${BASE_URL}/menu?restaurantId=${restaurantId}`);
        const json = await res.json();
        if (json.success && Array.isArray(json.data)) {
            return json.data.map((item, index) => ({
                id: String(item.id || index + 1),
                name: item.name || "Special Dish",
                description: item.description || "Freshly prepared by chef with premium ingredients.",
                price: parseFloat(item.price || 0).toFixed(2),
                weight: "350",
                category: item.category || "Mains",
                is_veg: item.is_veg !== undefined ? Boolean(item.is_veg) : true,
                is_available: item.is_available !== undefined ? Boolean(item.is_available) : true,
                photo_312x324: {
                    uri: item.image && item.image.startsWith("http")
                        ? item.image
                        : "https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=600&auto=format&fit=crop&q=80",
                },
                photo_477x300: {
                    uri: item.image && item.image.startsWith("http")
                        ? item.image
                        : "https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=800&auto=format&fit=crop&q=80",
                },
                addedToCart: false,
                images: [
                    {
                        id: "1",
                        uri: item.image && item.image.startsWith("http")
                            ? item.image
                            : "https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=1000&auto=format&fit=crop&q=80",
                    },
                ],
            }));
        }
        return [];
    } catch (err) {
        console.warn("Error fetching live menu from DineFlow AI:", err);
        return [];
    }
}

/**
 * Submit Live Order to DineFlow POS & Kitchen Display (KDS)
 */
export async function submitOrderToDineFlow({
    restaurantId = DEFAULT_RESTAURANT_ID,
    tableId = DEFAULT_TABLE_ID,
    customerName = "App Customer",
    customerPhone = "",
    items = [],
    orderType = "dine_in",
    notes = "",
}) {
    try {
        const payload = {
            restaurant_id: restaurantId,
            table_id: tableId,
            customer_name: customerName,
            customer_phone: customerPhone,
            order_type: orderType,
            special_instructions: notes,
            items: items.map(item => ({
                menu_item_id: item.id,
                name: item.name,
                quantity: item.quantity || 1,
                price: parseFloat(item.price || 0),
            })),
        };

        const res = await fetch(`${BASE_URL}/orders`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
            },
            body: JSON.stringify(payload),
        });

        return await res.json();
    } catch (err) {
        console.warn("Error submitting order to DineFlow AI:", err);
        return { success: false, message: err.message };
    }
}

/**
 * Call Waiter to Table
 */
export async function callWaiter({
    restaurantId = DEFAULT_RESTAURANT_ID,
    tableId = DEFAULT_TABLE_ID,
    reason = "Assistance requested from Mobile App",
}) {
    try {
        const res = await fetch(`${BASE_URL}/waiter-calls`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
            },
            body: JSON.stringify({
                restaurant_id: restaurantId,
                table_id: tableId,
                reason,
            }),
        });
        return await res.json();
    } catch (err) {
        return { success: false, message: err.message };
    }
}
