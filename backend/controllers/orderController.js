const Order = require('../models/Order');
const MenuItem = require('../models/MenuItem');
const { mockOrders, mockMenuItems } = require('../config/mockDb');

// @desc    Place a new order from a table
// @route   POST /api/orders
// @access  Public
const createOrder = async (req, res) => {
  try {
    const { restaurantId, tableId, items, paymentMethod } = req.body;

    if (!restaurantId || !tableId || !items || !Array.isArray(items) || items.length === 0) {
      return res.status(400).json({ success: false, message: 'Please enter restaurant ID, table ID, and order items' });
    }

    let calculatedTotal = 0;
    const validatedItems = [];

    // Validate prices and fetch item names
    for (const item of items) {
      const { menuItemId, quantity } = item;

      if (!menuItemId || !quantity || Number(quantity) <= 0) {
        return res.status(400).json({ success: false, message: 'Invalid item parameters' });
      }

      let price = 0;
      let name = '';

      if (process.env.USE_MOCK_DB === 'true') {
        const menuItem = mockMenuItems.find(i => i._id === menuItemId);
        if (!menuItem) {
          return res.status(404).json({ success: false, message: `Menu item ${menuItemId} not found in mock DB` });
        }
        price = menuItem.price;
        name = menuItem.name;
      } else {
        const menuItem = await MenuItem.findById(menuItemId);
        if (!menuItem) {
          return res.status(404).json({ success: false, message: `Menu item ${menuItemId} not found` });
        }
        price = menuItem.price;
        name = menuItem.name;
      }

      const itemTotal = price * Number(quantity);
      calculatedTotal += itemTotal;

      validatedItems.push({
        menuItemId,
        name,
        quantity: Number(quantity),
        price
      });
    }

    if (process.env.USE_MOCK_DB === 'true') {
      const newOrder = {
        _id: 'order_' + Date.now().toString(),
        restaurantId,
        tableId,
        items: validatedItems,
        totalAmount: calculatedTotal,
        status: 'pending',
        paymentStatus: 'pending',
        paymentMethod: paymentMethod || 'cash',
        createdAt: new Date(),
        updatedAt: new Date()
      };
      mockOrders.push(newOrder);
      return res.status(201).json({ success: true, data: newOrder });
    } else {
      const order = await Order.create({
        restaurantId,
        tableId,
        items: validatedItems,
        totalAmount: calculatedTotal,
        status: 'pending',
        paymentStatus: 'pending',
        paymentMethod: paymentMethod || 'cash'
      });
      return res.status(201).json({ success: true, data: order });
    }
  } catch (error) {
    console.error(error);
    return res.status(500).json({ success: false, message: 'Server error' });
  }
};

// @desc    Get all orders for a restaurant
// @route   GET /api/orders/restaurant/:restaurantId
// @access  Private
const getRestaurantOrders = async (req, res) => {
  try {
    const { restaurantId } = req.params;

    if (process.env.USE_MOCK_DB === 'true') {
      const orders = mockOrders.filter(o => o.restaurantId === restaurantId);
      return res.status(200).json({ success: true, data: orders });
    } else {
      const orders = await Order.find({ restaurantId }).sort({ createdAt: -1 });
      return res.status(200).json({ success: true, data: orders });
    }
  } catch (error) {
    console.error(error);
    return res.status(500).json({ success: false, message: 'Server error' });
  }
};

// @desc    Get a specific order by ID
// @route   GET /api/orders/:id
// @access  Public
const getOrder = async (req, res) => {
  try {
    if (process.env.USE_MOCK_DB === 'true') {
      const order = mockOrders.find(o => o._id === req.params.id);
      if (!order) {
        return res.status(404).json({ success: false, message: 'Order not found' });
      }
      return res.status(200).json({ success: true, data: order });
    } else {
      const order = await Order.findById(req.params.id);
      if (!order) {
        return res.status(404).json({ success: false, message: 'Order not found' });
      }
      return res.status(200).json({ success: true, data: order });
    }
  } catch (error) {
    console.error(error);
    return res.status(500).json({ success: false, message: 'Server error' });
  }
};

module.exports = {
  createOrder,
  getRestaurantOrders,
  getOrder
};
