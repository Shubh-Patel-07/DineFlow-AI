// DineFlow AI - Complete Utility Functions
// Production-ready utility library with 50+ functions

/**
 * STORAGE UTILITIES
 */

// Get data from localStorage
function getFromStorage(key, defaultValue = null) {
    try {
        const data = localStorage.getItem(key);
        return data ? JSON.parse(data) : defaultValue;
    } catch (error) {
        console.error(`Error retrieving ${key} from storage:`, error);
        return defaultValue;
    }
}

// Set data to localStorage
function setToStorage(key, value) {
    try {
        localStorage.setItem(key, JSON.stringify(value));
        return true;
    } catch (error) {
        console.error(`Error saving ${key} to storage:`, error);
        return false;
    }
}

// Remove data from localStorage
function removeFromStorage(key) {
    try {
        localStorage.removeItem(key);
        return true;
    } catch (error) {
        console.error(`Error removing ${key} from storage:`, error);
        return false;
    }
}

// Clear all localStorage
function clearAllStorage() {
    try {
        localStorage.clear();
        return true;
    } catch (error) {
        console.error('Error clearing storage:', error);
        return false;
    }
}

/**
 * DATE & TIME UTILITIES
 */

// Format date to readable format
function formatDate(date, format = 'MM/DD/YYYY') {
    if (!date) return '';
    const d = new Date(date);
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    const year = d.getFullYear();
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    const seconds = String(d.getSeconds()).padStart(2, '0');

    return format
        .replace('MM', month)
        .replace('DD', day)
        .replace('YYYY', year)
        .replace('HH', hours)
        .replace('mm', minutes)
        .replace('ss', seconds);
}

// Get time ago (e.g., "5 minutes ago")
function getTimeAgo(date) {
    const now = new Date();
    const diff = Math.floor((now - new Date(date)) / 1000);

    if (diff < 60) return `${diff}s ago`;
    if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
    if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
    return `${Math.floor(diff / 86400)}d ago`;
}

// Add days to date
function addDays(date, days) {
    const result = new Date(date);
    result.setDate(result.getDate() + days);
    return result;
}

// Get start of day
function getStartOfDay(date = new Date()) {
    const d = new Date(date);
    d.setHours(0, 0, 0, 0);
    return d;
}

// Get end of day
function getEndOfDay(date = new Date()) {
    const d = new Date(date);
    d.setHours(23, 59, 59, 999);
    return d;
}

/**
 * VALIDATION UTILITIES
 */

// Validate email
function isValidEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

// Validate phone number
function isValidPhone(phone) {
    const regex = /^\+?[\d\s\-()]{10,}$/;
    return regex.test(phone);
}

// Validate URL
function isValidURL(url) {
    try {
        new URL(url);
        return true;
    } catch (error) {
        return false;
    }
}

// Check if value is empty
function isEmpty(value) {
    return value === null || value === undefined || value === '' || 
           (Array.isArray(value) && value.length === 0) ||
           (typeof value === 'object' && Object.keys(value).length === 0);
}

// Validate form data
function validateFormData(formData, rules) {
    const errors = {};

    Object.keys(rules).forEach(field => {
        const value = formData[field];
        const rule = rules[field];

        if (rule.required && isEmpty(value)) {
            errors[field] = `${field} is required`;
        }

        if (rule.email && !isEmpty(value) && !isValidEmail(value)) {
            errors[field] = `${field} must be a valid email`;
        }

        if (rule.minLength && value && value.length < rule.minLength) {
            errors[field] = `${field} must be at least ${rule.minLength} characters`;
        }

        if (rule.maxLength && value && value.length > rule.maxLength) {
            errors[field] = `${field} must be at most ${rule.maxLength} characters`;
        }

        if (rule.pattern && value && !rule.pattern.test(value)) {
            errors[field] = `${field} format is invalid`;
        }
    });

    return errors;
}

/**
 * STRING UTILITIES
 */

// Capitalize first letter
function capitalize(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}

// Capitalize all words
function capitalizeWords(str) {
    if (!str) return '';
    return str.split(' ').map(word => capitalize(word)).join(' ');
}

// Truncate string
function truncateString(str, maxLength, suffix = '...') {
    if (!str || str.length <= maxLength) return str;
    return str.substring(0, maxLength - suffix.length) + suffix;
}

// Generate random string
function generateRandomString(length = 10) {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let result = '';
    for (let i = 0; i < length; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return result;
}

// Slug format
function slugify(str) {
    return str
        .toLowerCase()
        .trim()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');
}

/**
 * NUMBER UTILITIES
 */

// Format currency
function formatCurrency(amount, currency = 'USD') {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency
    }).format(amount);
}

// Format number with commas
function formatNumber(num) {
    return num.toLocaleString();
}

// Round to decimal places
function roundTo(num, decimals = 2) {
    return Math.round(num * Math.pow(10, decimals)) / Math.pow(10, decimals);
}

// Percentage calculation
function calculatePercentage(value, total) {
    return roundTo((value / total) * 100, 2);
}

/**
 * ARRAY UTILITIES
 */

// Remove duplicates
function removeDuplicates(arr) {
    return [...new Set(arr)];
}

// Group array by property
function groupBy(arr, property) {
    return arr.reduce((groups, item) => {
        const key = item[property];
        if (!groups[key]) groups[key] = [];
        groups[key].push(item);
        return groups;
    }, {});
}

// Sort array by property
function sortBy(arr, property, ascending = true) {
    return [...arr].sort((a, b) => {
        if (a[property] < b[property]) return ascending ? -1 : 1;
        if (a[property] > b[property]) return ascending ? 1 : -1;
        return 0;
    });
}

// Find item in array
function findItem(arr, property, value) {
    return arr.find(item => item[property] === value);
}

// Filter array
function filterArray(arr, property, value) {
    return arr.filter(item => item[property] === value);
}

// Chunk array
function chunkArray(arr, size) {
    const chunks = [];
    for (let i = 0; i < arr.length; i += size) {
        chunks.push(arr.slice(i, i + size));
    }
    return chunks;
}

/**
 * OBJECT UTILITIES
 */

// Deep clone object
function deepClone(obj) {
    return JSON.parse(JSON.stringify(obj));
}

// Merge objects
function mergeObjects(obj1, obj2) {
    return { ...obj1, ...obj2 };
}

// Check if key exists
function hasKey(obj, key) {
    return key in obj;
}

// Get object keys
function getObjectKeys(obj) {
    return Object.keys(obj);
}

// Get object values
function getObjectValues(obj) {
    return Object.values(obj);
}

/**
 * API UTILITIES
 */

// Fetch with error handling
async function fetchData(url, options = {}) {
    try {
        const response = await fetch(url, {
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            },
            ...options
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error('Fetch error:', error);
        throw error;
    }
}

// POST request
async function postData(url, data, options = {}) {
    return fetchData(url, {
        method: 'POST',
        body: JSON.stringify(data),
        ...options
    });
}

// PUT request
async function putData(url, data, options = {}) {
    return fetchData(url, {
        method: 'PUT',
        body: JSON.stringify(data),
        ...options
    });
}

// DELETE request
async function deleteData(url, options = {}) {
    return fetchData(url, {
        method: 'DELETE',
        ...options
    });
}

/**
 * DOM UTILITIES
 */

// Get element by ID
function getById(id) {
    return document.getElementById(id);
}

// Get elements by class
function getByClass(className) {
    return document.querySelectorAll(`.${className}`);
}

// Create element
function createElement(tag, attributes = {}, content = '') {
    const element = document.createElement(tag);
    Object.keys(attributes).forEach(key => {
        element.setAttribute(key, attributes[key]);
    });
    if (content) element.innerHTML = content;
    return element;
}

// Add class
function addClass(element, className) {
    element.classList.add(className);
}

// Remove class
function removeClass(element, className) {
    element.classList.remove(className);
}

// Toggle class
function toggleClass(element, className) {
    element.classList.toggle(className);
}

// Show element
function show(element) {
    element.style.display = '';
}

// Hide element
function hide(element) {
    element.style.display = 'none';
}

// Check if element is visible
function isVisible(element) {
    return element.offsetParent !== null;
}

/**
 * NOTIFICATION UTILITIES
 */

// Show toast notification
function showToast(message, type = 'info', duration = 3000) {
    const colors = {
        'success': '#27ae60',
        'error': '#e74c3c',
        'warning': '#f39c12',
        'info': '#3498db'
    };

    const toast = createElement('div', {
        style: `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: ${colors[type] || colors['info']};
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            z-index: 10000;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            font-weight: 500;
        `
    }, message);

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, duration);
}

// Confirm dialog
function confirmDialog(message) {
    return confirm(message);
}

// Alert dialog
function alertDialog(message, title = 'Alert') {
    alert(message);
}

/**
 * ERROR HANDLING
 */

// Log error with context
function logError(error, context = '') {
    console.error(`[${context}]`, error);
    console.error('Stack:', error.stack);
}

// Handle API error
function handleAPIError(error) {
    if (error.response) {
        console.error('API Error:', error.response.status, error.response.data);
        return error.response.data;
    } else if (error.request) {
        console.error('No response:', error.request);
    } else {
        console.error('Error:', error.message);
    }
}

/**
 * UTILITY HELPERS
 */

// Debounce function
function debounce(func, wait = 300) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

// Throttle function
function throttle(func, limit = 300) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Wait/Sleep
function wait(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

// Retry function
async function retry(func, maxRetries = 3, delay = 1000) {
    for (let i = 0; i < maxRetries; i++) {
        try {
            return await func();
        } catch (error) {
            if (i === maxRetries - 1) throw error;
            await wait(delay);
        }
    }
}

// Performance timer
function timer(label) {
    console.time(label);
    return () => console.timeEnd(label);
}

// Export utilities (if using modules)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        // Storage
        getFromStorage, setToStorage, removeFromStorage, clearAllStorage,
        // Date & Time
        formatDate, getTimeAgo, addDays, getStartOfDay, getEndOfDay,
        // Validation
        isValidEmail, isValidPhone, isValidURL, isEmpty, validateFormData,
        // String
        capitalize, capitalizeWords, truncateString, generateRandomString, slugify,
        // Number
        formatCurrency, formatNumber, roundTo, calculatePercentage,
        // Array
        removeDuplicates, groupBy, sortBy, findItem, filterArray, chunkArray,
        // Object
        deepClone, mergeObjects, hasKey, getObjectKeys, getObjectValues,
        // API
        fetchData, postData, putData, deleteData,
        // DOM
        getById, getByClass, createElement, addClass, removeClass, toggleClass, show, hide, isVisible,
        // Notification
        showToast, confirmDialog, alertDialog,
        // Error
        logError, handleAPIError,
        // Helpers
        debounce, throttle, wait, retry, timer
    };
    }
