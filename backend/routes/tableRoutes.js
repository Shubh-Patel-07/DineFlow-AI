const express = require('express');
const router = express.Router();
const { createTable, getRestaurantTables, getTableQR } = require('../controllers/tableController');
const { protect } = require('../middleware/authMiddleware');

router.post('/', protect, createTable);
router.get('/restaurant/:restaurantId', protect, getRestaurantTables);
router.get('/:id/qr', getTableQR);

module.exports = router;
