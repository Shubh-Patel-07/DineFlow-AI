const Inventory = require('../models/Inventory');
const { mockInventory } = require('../config/mockDb');

// @desc    Get all inventory items for a restaurant
// @route   GET /api/inventory
// @access  Private/Admin
const getInventory = async (req, res) => {
  try {
    const restaurantId = req.user.restaurantId || req.query.restaurantId;

    if (!restaurantId) {
      return res.status(400).json({ success: false, message: 'Please specify restaurant ID' });
    }

    if (process.env.USE_MOCK_DB === 'true') {
      const items = mockInventory.filter(item => item.restaurantId === restaurantId);
      return res.status(200).json({ success: true, data: items });
    } else {
      const items = await Inventory.find({ restaurantId });
      return res.status(200).json({ success: true, data: items });
    }
  } catch (error) {
    console.error(error);
    return res.status(500).json({ success: false, message: 'Server error' });
  }
};

// @desc    Add new item to inventory
// @route   POST /api/inventory
// @access  Private/Admin
const addInventoryItem = async (req, res) => {
  try {
    const { name, quantity, unit, threshold } = req.body;
    const restaurantId = req.user.restaurantId || req.body.restaurantId;

    if (!restaurantId || !name || quantity === undefined) {
      return res.status(400).json({ success: false, message: 'Please enter all required fields' });
    }

    if (process.env.USE_MOCK_DB === 'true') {
      const newItem = {
        _id: 'inv_' + Date.now().toString(),
        restaurantId,
        name,
        quantity: Number(quantity),
        unit: unit || 'units',
        threshold: threshold !== undefined ? Number(threshold) : 10,
        createdAt: new Date(),
        updatedAt: new Date()
      };
      mockInventory.push(newItem);
      return res.status(201).json({ success: true, data: newItem });
    } else {
      const item = await Inventory.create({
        restaurantId,
        name,
        quantity: Number(quantity),
        unit: unit || 'units',
        threshold: threshold !== undefined ? Number(threshold) : 10
      });
      return res.status(201).json({ success: true, data: item });
    }
  } catch (error) {
    console.error(error);
    return res.status(500).json({ success: false, message: 'Server error' });
  }
};

// @desc    Update stock of an item
// @route   PUT /api/inventory/:id
// @access  Private/Admin
const updateStock = async (req, res) => {
  try {
    const { quantity } = req.body;

    if (quantity === undefined) {
      return res.status(400).json({ success: false, message: 'Please specify quantity' });
    }

    if (process.env.USE_MOCK_DB === 'true') {
      const itemIndex = mockInventory.findIndex(item => item._id === req.params.id);
      if (itemIndex === -1) {
        return res.status(404).json({ success: false, message: 'Inventory item not found' });
      }

      mockInventory[itemIndex].quantity = Number(quantity);
      mockInventory[itemIndex].updatedAt = new Date();

      return res.status(200).json({ success: true, data: mockInventory[itemIndex] });
    } else {
      const item = await Inventory.findByIdAndUpdate(
        req.params.id,
        { quantity: Number(quantity) },
        { new: true, runValidators: true }
      );

      if (!item) {
        return res.status(404).json({ success: false, message: 'Inventory item not found' });
      }

      return res.status(200).json({ success: true, data: item });
    }
  } catch (error) {
    console.error(error);
    return res.status(500).json({ success: false, message: 'Server error' });
  }
};

module.exports = {
  getInventory,
  addInventoryItem,
  updateStock
};
