const mongoose = require('mongoose');

const InventorySchema = new mongoose.Schema(
  {
    restaurantId: {
      type: mongoose.Schema.Types.ObjectId,
      ref: 'Restaurant',
      required: true,
    },
    name: {
      type: String,
      required: [true, 'Please add stock item name'],
      trim: true,
    },
    quantity: {
      type: Number,
      required: [true, 'Please add stock quantity'],
      default: 0,
    },
    unit: {
      type: String,
      required: [true, 'Please add quantity unit (e.g. kg, units, liters)'],
      default: 'units',
    },
    threshold: {
      type: Number,
      required: [true, 'Please add stock warning threshold'],
      default: 10,
    },
  },
  {
    timestamps: true,
  }
);

module.exports = mongoose.model('Inventory', InventorySchema);
