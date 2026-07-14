const express = require('express');
const router = express.Router();
const { getSalesAnalytics } = require('../controllers/analyticsController');
const { protect, restrictTo, verifyTenant } = require('../middleware/authMiddleware');

router.get('/restaurant/:restaurantId', protect, restrictTo('admin'), verifyTenant, getSalesAnalytics);

module.exports = router;
