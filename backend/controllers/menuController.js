const MenuItem = require('../models/MenuItem');
const { mockMenuItems } = require('../config/mockDb');

// @desc    Add a menu item
// @route   POST /api/menu
// @access  Private/Admin
const createMenuItem = async (req, res) => {
  try {
    const { restaurantId, name, description, price, category, image, isAvailable, isVeg } = req.body;

    if (!restaurantId || !name || !price || !category) {
      return res.status(400).json({ success: false, message: 'Please enter all required fields' });
    }

    if (process.env.USE_MOCK_DB === 'true') {
      const newItem = {
        _id: 'menu_' + Date.now().toString(),
        restaurantId,
        name,
        description: description || '',
        price: Number(price),
        category,
        image: image || '',
        isAvailable: isAvailable !== undefined ? isAvailable : true,
        isVeg: isVeg !== undefined ? isVeg : true,
        createdAt: new Date(),
        updatedAt: new Date()
      };
      mockMenuItems.push(newItem);
      return res.status(201).json({ success: true, data: newItem });
    } else {
      const menuItem = await MenuItem.create({
        restaurantId,
        name,
        description: description || '',
        price: Number(price),
        category,
        image: image || '',
        isAvailable: isAvailable !== undefined ? isAvailable : true,
        isVeg: isVeg !== undefined ? isVeg : true
      });
      return res.status(201).json({ success: true, data: menuItem });
    }
  } catch (error) {
    console.error(error);
    return res.status(500).json({ success: false, message: 'Server error' });
  }
};

// @desc    Update a menu item
// @route   PUT /api/menu/:id
// @access  Private/Admin
const updateMenuItem = async (req, res) => {
  try {
    const { name, description, price, category, image, isAvailable, isVeg } = req.body;

    if (process.env.USE_MOCK_DB === 'true') {
      const itemIndex = mockMenuItems.findIndex(item => item._id === req.params.id);
      if (itemIndex === -1) {
        return res.status(404).json({ success: false, message: 'Menu item not found' });
      }

      const updatedItem = {
        ...mockMenuItems[itemIndex],
        name: name !== undefined ? name : mockMenuItems[itemIndex].name,
        description: description !== undefined ? description : mockMenuItems[itemIndex].description,
        price: price !== undefined ? Number(price) : mockMenuItems[itemIndex].price,
        category: category !== undefined ? category : mockMenuItems[itemIndex].category,
        image: image !== undefined ? image : mockMenuItems[itemIndex].image,
        isAvailable: isAvailable !== undefined ? isAvailable : mockMenuItems[itemIndex].isAvailable,
        isVeg: isVeg !== undefined ? isVeg : mockMenuItems[itemIndex].isVeg,
        updatedAt: new Date()
      };

      mockMenuItems[itemIndex] = updatedItem;
      return res.status(200).json({ success: true, data: updatedItem });
    } else {
      const menuItem = await MenuItem.findByIdAndUpdate(
        req.params.id,
        { name, description, price, category, image, isAvailable, isVeg },
        { new: true, runValidators: true }
      );

      if (!menuItem) {
        return res.status(404).json({ success: false, message: 'Menu item not found' });
      }

      return res.status(200).json({ success: true, data: menuItem });
    }
  } catch (error) {
    console.error(error);
    return res.status(500).json({ success: false, message: 'Server error' });
  }
};

// @desc    Delete a menu item
// @route   DELETE /api/menu/:id
// @access  Private/Admin
const deleteMenuItem = async (req, res) => {
  try {
    if (process.env.USE_MOCK_DB === 'true') {
      const itemIndex = mockMenuItems.findIndex(item => item._id === req.params.id);
      if (itemIndex === -1) {
        return res.status(404).json({ success: false, message: 'Menu item not found' });
      }
      mockMenuItems.splice(itemIndex, 1);
      return res.status(200).json({ success: true, message: 'Menu item deleted' });
    } else {
      const menuItem = await MenuItem.findByIdAndDelete(req.params.id);
      if (!menuItem) {
        return res.status(404).json({ success: false, message: 'Menu item not found' });
      }
      return res.status(200).json({ success: true, message: 'Menu item deleted' });
    }
  } catch (error) {
    console.error(error);
    return res.status(500).json({ success: false, message: 'Server error' });
  }
};

// @desc    Get menu items for a restaurant
// @route   GET /api/menu/restaurant/:restaurantId
// @access  Public
const getRestaurantMenu = async (req, res) => {
  try {
    const { restaurantId } = req.params;

    if (process.env.USE_MOCK_DB === 'true') {
      const items = mockMenuItems.filter(item => item.restaurantId === restaurantId);
      return res.status(200).json({ success: true, data: items });
    } else {
      const items = await MenuItem.find({ restaurantId });
      return res.status(200).json({ success: true, data: items });
    }
  } catch (error) {
    console.error(error);
    return res.status(500).json({ success: false, message: 'Server error' });
  }
};

module.exports = {
  createMenuItem,
  updateMenuItem,
  deleteMenuItem,
  getRestaurantMenu
};
