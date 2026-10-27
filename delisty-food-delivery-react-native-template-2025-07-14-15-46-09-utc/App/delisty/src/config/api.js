/**
 * DineFlow AI Mobile App API Configuration
 * Connects the React Native mobile application to the DineFlow AI Cloud Backend
 */

export const API_CONFIG = {
    // Production Cloud Instance
    BASE_URL: "https://dineflowai.duckdns.org/api",
    
    // Local Development Fallbacks
    LOCAL_ANDROID_EMULATOR: "http://10.0.2.2:8000/api",
    LOCAL_WEB_IOS: "http://localhost:8000/api",
    
    // Endpoints
    ENDPOINTS: {
        MENU_CATEGORIES: "/categories",
        MENU_ITEMS: "/items",
        RESTAURANT_DETAILS: "/restaurant",
        TABLE_STATUS: "/tables",
        SUBMIT_ORDER: "/orders",
        TRACK_ORDER: "/orders/track",
    },
    
    // Request Timeout
    TIMEOUT_MS: 10000,
};

export default API_CONFIG;
