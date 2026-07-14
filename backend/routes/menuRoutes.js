const express = require('express');
const router = express.Router();
const { createMenuItem, updateMenuItem, deleteMenuItem, getRestaurantMenu } = require('../controllers/menuController');
const { protect } = require('../middleware/authMiddleware');

router.post('/', protect, createMenuItem);
router.put('/:id', protect, updateMenuItem);
router.delete('/:id', protect, deleteMenuItem);
router.get('/restaurant/:restaurantId', getRestaurantMenu);

module.exports = router;
