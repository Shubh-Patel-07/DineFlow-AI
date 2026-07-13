const mongoose = require('mongoose');

const RestaurantSchema = new mongoose.Schema(
  {
    name: {
      type: String,
      required: [true, 'Please add a restaurant name'],
      trim: true,
    },
    address: {
      type: String,
      required: [true, 'Please add an address'],
    },
    phone: {
      type: String,
      required: [true, 'Please add a phone number'],
    },
    logo: {
      type: String,
      default: '',
    },
  },
  {
    timestamps: true,
  }
);

module.exports = mongoose.model('Restaurant', RestaurantSchema);
