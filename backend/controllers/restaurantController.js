const Restaurant = require('../models/Restaurant');
const { mockRestaurants } = require('../config/mockDb');

// @desc    Create a new restaurant
// @route   POST /api/restaurants
// @access  Private/Admin
const createRestaurant = async (req, res) => {
  try {
    const { name, address, phone, logo } = req.body;

    if (!name || !address || !phone) {
      return res.status(400).json({ success: false, message: 'Please enter all required fields' });
    }

    if (process.env.USE_MOCK_DB === 'true') {
      const newRestaurant = {
        _id: 'rest_' + Date.now().toString(),
        name,
        address,
        phone,
        logo: logo || '',
        createdAt: new Date(),
        updatedAt: new Date()
      };
      mockRestaurants.push(newRestaurant);
      return res.status(201).json({ success: true, data: newRestaurant });
    } else {
      const restaurant = await Restaurant.create({
        name,
        address,
        phone,
        logo: logo || ''
      });
      return res.status(201).json({ success: true, data: restaurant });
    }
  } catch (error) {
    console.error(error);
    return res.status(500).json({ success: false, message: 'Server error' });
  }
};

// @desc    Get restaurant profile by ID
// @route   GET /api/restaurants/:id
// @access  Public
const getRestaurant = async (req, res) => {
  try {
    if (process.env.USE_MOCK_DB === 'true') {
      const restaurant = mockRestaurants.find(r => r._id === req.params.id);
      if (!restaurant) {
        return res.status(404).json({ success: false, message: 'Restaurant not found' });
      }
      return res.status(200).json({ success: true, data: restaurant });
    } else {
      const restaurant = await Restaurant.findById(req.params.id);
      if (!restaurant) {
        return res.status(404).json({ success: false, message: 'Restaurant not found' });
      }
      return res.status(200).json({ success: true, data: restaurant });
    }
  } catch (error) {
    console.error(error);
    return res.status(500).json({ success: false, message: 'Server error' });
  }
};

module.exports = {
  createRestaurant,
  getRestaurant
};
