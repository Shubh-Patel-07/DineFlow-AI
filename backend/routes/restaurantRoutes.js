const express = require('express');
const router = express.Router();
const { createRestaurant, getRestaurant } = require('../controllers/restaurantController');
const { protect } = require('../middleware/authMiddleware');

router.post('/', protect, createRestaurant);
router.get('/:id', getRestaurant);

module.exports = router;
