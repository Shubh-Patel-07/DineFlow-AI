const Order = require('../models/Order');
const MenuItem = require('../models/MenuItem');
const Inventory = require('../models/Inventory');
const { mockOrders, mockMenuItems, mockInventory } = require('../config/mockDb');

// Helper to auto-deduct stock
const deductInventoryStock = async (restaurantId, itemName, quantity) => {
  try {
    if (process.env.USE_MOCK_DB === 'true') {
      const item = mockInventory.find(i => 
        i.restaurantId === restaurantId && 
        i.name.toLowerCase() === itemName.toLowerCase()
      );
      if (item) {
        item.quantity = Math.max(0, item.quantity - quantity);
        console.log(`[Inventory Mock] Deducted ${quantity} from ${itemName}. New stock: ${item.quantity}`);
      }
    } else {
      const item = await Inventory.findOne({
        restaurantId,
        name: { $regex: new RegExp(`^${itemName}$`, 'i') }
      });
      if (item) {
        item.quantity = Math.max(0, item.quantity - quantity);
        await item.save();
        console.log(`[Inventory DB] Deducted ${quantity} from ${itemName}. New stock: ${item.quantity}`);
      }
    }
  } catch (err) {
    console.error(`Error auto-deducting inventory stock for ${itemName}:`, err.message);
  }
};

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

      await deductInventoryStock(restaurantId, name, Number(quantity));
    }

    let savedOrder;

    if (process.env.USE_MOCK_DB === 'true') {
      savedOrder = {
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
      mockOrders.push(savedOrder);
    } else {
      savedOrder = await Order.create({
        restaurantId,
        tableId,
        items: validatedItems,
        totalAmount: calculatedTotal,
        status: 'pending',
        paymentStatus: 'pending',
        paymentMethod: paymentMethod || 'cash'
      });
    }

    const io = req.app.get('socketio');
    if (io) {
      io.emit('newOrder', savedOrder);
      console.log(`[Socket] Broadcasted newOrder event for order ${savedOrder._id}`);
    }

    return res.status(201).json({ success: true, data: savedOrder });
  } catch (error) {
    console.error(error);
    return res.status(500).json({ success: false, message: 'Server error' });
  }
};

// @desc    Update order status
// @route   PUT /api/orders/:id/status
// @access  Private/Staff
const updateOrderStatus = async (req, res) => {
  try {
    const { status } = req.body;
    const allowedStatuses = ['pending', 'preparing', 'served', 'completed', 'cancelled'];

    if (!status || !allowedStatuses.includes(status)) {
      return res.status(400).json({ success: false, message: 'Please specify a valid status' });
    }

    let updatedOrder;

    if (process.env.USE_MOCK_DB === 'true') {
      const orderIndex = mockOrders.findIndex(o => o._id === req.params.id);
      if (orderIndex === -1) {
        return res.status(404).json({ success: false, message: 'Order not found' });
      }

      mockOrders[orderIndex].status = status;
      mockOrders[orderIndex].updatedAt = new Date();
      updatedOrder = mockOrders[orderIndex];
    } else {
      updatedOrder = await Order.findByIdAndUpdate(
        req.params.id,
        { status },
        { new: true, runValidators: true }
      );

      if (!updatedOrder) {
        return res.status(404).json({ success: false, message: 'Order not found' });
      }
    }

    const io = req.app.get('socketio');
    if (io) {
      io.emit('orderStatusUpdated', { orderId: req.params.id, status });
      console.log(`[Socket] Broadcasted orderStatusUpdated for order ${req.params.id} -> ${status}`);
    }

    return res.status(200).json({ success: true, data: updatedOrder });
  } catch (error) {
    console.error(error);
    return res.status(500).json({ success: false, message: 'Server error' });
  }
};

// @desc    Process mock order payment
// @route   POST /api/orders/:id/pay
// @access  Public
const payOrder = async (req, res) => {
  try {
    const { paymentMethod } = req.body;
    let updatedOrder;

    if (process.env.USE_MOCK_DB === 'true') {
      const orderIndex = mockOrders.findIndex(o => o._id === req.params.id);
      if (orderIndex === -1) {
        return res.status(404).json({ success: false, message: 'Order not found' });
      }

      mockOrders[orderIndex].paymentStatus = 'paid';
      mockOrders[orderIndex].paymentMethod = paymentMethod || 'online';
      mockOrders[orderIndex].updatedAt = new Date();
      updatedOrder = mockOrders[orderIndex];
    } else {
      updatedOrder = await Order.findByIdAndUpdate(
        req.params.id,
        { paymentStatus: 'paid', paymentMethod: paymentMethod || 'online' },
        { new: true }
      );

      if (!updatedOrder) {
        return res.status(404).json({ success: false, message: 'Order not found' });
      }
    }

    const io = req.app.get('socketio');
    if (io) {
      io.emit('orderPaid', updatedOrder);
      console.log(`[Socket] Broadcasted orderPaid for order ${req.params.id}`);
    }

    return res.status(200).json({ success: true, data: updatedOrder });
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
  updateOrderStatus,
  payOrder,
  getRestaurantOrders,
  getOrder
};
