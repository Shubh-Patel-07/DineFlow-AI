const express = require('express');
const router = express.Router();
const { createOrder, getRestaurantOrders, getOrder } = require('../controllers/orderController');
const { protect } = require('../middleware/authMiddleware');

router.post('/', createOrder);
router.get('/restaurant/:restaurantId', protect, getRestaurantOrders);
router.get('/:id', getOrder);

module.exports = router;
