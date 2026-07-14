const express = require('express');
const router = express.Router();
const { getInventory, addInventoryItem, updateStock } = require('../controllers/inventoryController');
const { protect, restrictTo, verifyTenant } = require('../middleware/authMiddleware');

router.get('/', protect, restrictTo('admin', 'staff'), verifyTenant, getInventory);
router.post('/', protect, restrictTo('admin'), verifyTenant, addInventoryItem);
router.put('/:id', protect, restrictTo('admin'), updateStock);

module.exports = router;
