const Order = require('../models/Order');
const { mockOrders } = require('../config/mockDb');
const mongoose = require('mongoose');

// @desc    Get sales analytics for a restaurant
// @route   GET /api/analytics/restaurant/:restaurantId
// @access  Private/Admin
const getSalesAnalytics = async (req, res) => {
  try {
    const { restaurantId } = req.params;

    if (process.env.USE_MOCK_DB === 'true') {
      const orders = mockOrders.filter(o => o.restaurantId === restaurantId);
      
      const totalOrders = orders.length;
      const totalSales = orders.reduce((sum, o) => sum + o.totalAmount, 0);
      const averageOrderValue = totalOrders > 0 ? Number((totalSales / totalOrders).toFixed(2)) : 0;

      // Calculate popular items
      const itemCounts = {};
      orders.forEach(o => {
        o.items.forEach(item => {
          if (!itemCounts[item.name]) {
            itemCounts[item.name] = 0;
          }
          itemCounts[item.name] += item.quantity;
        });
      });

      const popularItems = Object.keys(itemCounts).map(name => ({
        name,
        salesCount: itemCounts[name]
      })).sort((a, b) => b.salesCount - a.salesCount).slice(0, 5);

      return res.status(200).json({
        success: true,
        data: {
          totalOrders,
          totalSales,
          averageOrderValue,
          popularItems
        }
      });
    } else {
      const rId = new mongoose.Types.ObjectId(restaurantId);

      const salesStats = await Order.aggregate([
        { $match: { restaurantId: rId } },
        {
          $group: {
            _id: null,
            totalOrders: { $sum: 1 },
            totalSales: { $sum: '$totalAmount' }
          }
        }
      ]);

      const totalOrders = salesStats.length > 0 ? salesStats[0].totalOrders : 0;
      const totalSales = salesStats.length > 0 ? salesStats[0].totalSales : 0;
      const averageOrderValue = totalOrders > 0 ? Number((totalSales / totalOrders).toFixed(2)) : 0;

      const topItems = await Order.aggregate([
        { $match: { restaurantId: rId } },
        { $unwind: '$items' },
        {
          $group: {
            _id: '$items.name',
            salesCount: { $sum: '$items.quantity' }
          }
        },
        { $sort: { salesCount: -1 } },
        { $limit: 5 },
        {
          $project: {
            _id: 0,
            name: '$_id',
            salesCount: 1
          }
        }
      ]);

      return res.status(200).json({
        success: true,
        data: {
          totalOrders,
          totalSales,
          averageOrderValue,
          popularItems: topItems
        }
      });
    }
  } catch (error) {
    console.error(error);
    return res.status(500).json({ success: false, message: 'Server error' });
  }
};

module.exports = {
  getSalesAnalytics
};
