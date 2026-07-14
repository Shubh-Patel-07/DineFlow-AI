const express = require('express');
const router = express.Router();
const { createOrder, updateOrderStatus, payOrder, getRestaurantOrders, getOrder } = require('../controllers/orderController');
const { protect, restrictTo, verifyTenant } = require('../middleware/authMiddleware');

router.post('/', createOrder);
router.get('/restaurant/:restaurantId', protect, restrictTo('admin', 'staff', 'kitchen'), verifyTenant, getRestaurantOrders);
router.get('/:id', getOrder);
router.put('/:id/status', protect, restrictTo('admin', 'staff', 'kitchen'), updateOrderStatus);
router.post('/:id/pay', payOrder);

module.exports = router;
