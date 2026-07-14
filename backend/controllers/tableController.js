const Table = require('../models/Table');
const { mockTables } = require('../config/mockDb');
const QRCode = require('qrcode');
const mongoose = require('mongoose');

// @desc    Add a new table
// @route   POST /api/tables
// @access  Private/Admin
const createTable = async (req, res) => {
  try {
    const { restaurantId, tableNumber } = req.body;

    if (!restaurantId || !tableNumber) {
      return res.status(400).json({ success: false, message: 'Please enter restaurant ID and table number' });
    }

    if (process.env.USE_MOCK_DB === 'true') {
      const tableId = 'table_' + Date.now().toString();
      const qrData = `http://localhost:5000/menu.html?restaurantId=${restaurantId}&tableId=${tableId}`;
      const qrBase64 = await QRCode.toDataURL(qrData);

      const newTable = {
        _id: tableId,
        restaurantId,
        tableNumber,
        qrCodeUrl: qrBase64,
        status: 'active',
        createdAt: new Date(),
        updatedAt: new Date()
      };
      
      mockTables.push(newTable);
      return res.status(201).json({ success: true, data: newTable });
    } else {
      const tableId = new mongoose.Types.ObjectId();
      const qrData = `http://localhost:5000/menu.html?restaurantId=${restaurantId}&tableId=${tableId}`;
      const qrBase64 = await QRCode.toDataURL(qrData);

      const table = await Table.create({
        _id: tableId,
        restaurantId,
        tableNumber,
        qrCodeUrl: qrBase64,
        status: 'active'
      });

      return res.status(201).json({ success: true, data: table });
    }
  } catch (error) {
    console.error(error);
    return res.status(500).json({ success: false, message: 'Server error' });
  }
};

// @desc    Get all tables for a restaurant
// @route   GET /api/tables/restaurant/:restaurantId
// @access  Private
const getRestaurantTables = async (req, res) => {
  try {
    const { restaurantId } = req.params;

    if (process.env.USE_MOCK_DB === 'true') {
      const tables = mockTables.filter(t => t.restaurantId === restaurantId);
      return res.status(200).json({ success: true, data: tables });
    } else {
      const tables = await Table.find({ restaurantId });
      return res.status(200).json({ success: true, data: tables });
    }
  } catch (error) {
    console.error(error);
    return res.status(500).json({ success: false, message: 'Server error' });
  }
};

// @desc    Get table details and QR
// @route   GET /api/tables/:id/qr
// @access  Public
const getTableQR = async (req, res) => {
  try {
    if (process.env.USE_MOCK_DB === 'true') {
      const table = mockTables.find(t => t._id === req.params.id);
      if (!table) {
        return res.status(404).json({ success: false, message: 'Table not found' });
      }
      return res.status(200).json({ success: true, data: table });
    } else {
      const table = await Table.findById(req.params.id);
      if (!table) {
        return res.status(404).json({ success: false, message: 'Table not found' });
      }
      return res.status(200).json({ success: true, data: table });
    }
  } catch (error) {
    console.error(error);
    return res.status(500).json({ success: false, message: 'Server error' });
  }
};

module.exports = {
  createTable,
  getRestaurantTables,
  getTableQR
};
