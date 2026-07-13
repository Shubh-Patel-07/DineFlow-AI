const mongoose = require('mongoose');

const OrderItemSchema = new mongoose.Schema({
  menuItemId: {
    type: mongoose.Schema.Types.ObjectId,
    ref: 'MenuItem',
    required: true,
  },
  name: {
    type: String,
    required: true,
  },
  quantity: {
    type: Number,
    required: true,
    min: [1, 'Quantity must be at least 1'],
  },
  price: {
    type: Number,
    required: true,
  },
});

const OrderSchema = new mongoose.Schema(
  {
    restaurantId: {
      type: mongoose.Schema.Types.ObjectId,
      ref: 'Restaurant',
      required: true,
    },
    tableId: {
      type: mongoose.Schema.Types.ObjectId,
      ref: 'Table',
      required: true,
    },
    items: [OrderItemSchema],
    totalAmount: {
      type: Number,
      required: true,
    },
    status: {
      type: String,
      enum: ['pending', 'preparing', 'served', 'completed', 'cancelled'],
      default: 'pending',
    },
    paymentStatus: {
      type: String,
      enum: ['pending', 'paid', 'failed'],
      default: 'pending',
    },
    paymentMethod: {
      type: String,
      enum: ['cash', 'online'],
      default: 'cash',
    },
  },
  {
    timestamps: true,
  }
);

module.exports = mongoose.model('Order', OrderSchema);
