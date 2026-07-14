const jwt = require('jsonwebtoken');
const User = require('../models/User');
const { mockUsers } = require('../config/mockDb');

const protect = async (req, res, next) => {
  let token;

  if (
    req.headers.authorization &&
    req.headers.authorization.startsWith('Bearer')
  ) {
    try {
      token = req.headers.authorization.split(' ')[1];

      const decoded = jwt.verify(token, process.env.JWT_SECRET || 'secret123');

      if (process.env.USE_MOCK_DB === 'true') {
        const user = mockUsers.find(u => u._id === decoded.id);
        if (!user) {
          return res.status(401).json({ success: false, message: 'Not authorized, user not found in mock DB' });
        }
        const { password, ...userWithoutPassword } = user;
        req.user = userWithoutPassword;
      } else {
        req.user = await User.findById(decoded.id).select('-password');
      }

      return next();
    } catch (error) {
      console.error(error);
      return res.status(401).json({ success: false, message: 'Not authorized, token failed' });
    }
  }

  if (!token) {
    return res.status(401).json({ success: false, message: 'Not authorized, no token' });
  }
};

// Restrict access to specific roles
const restrictTo = (...roles) => {
  return (req, res, next) => {
    if (!req.user || !roles.includes(req.user.role)) {
      return res.status(403).json({
        success: false,
        message: `Forbidden: User role '${req.user ? req.user.role : 'none'}' is not authorized to access this route`
      });
    }
    return next();
  };
};

// Verify multi-tenant isolation
const verifyTenant = (req, res, next) => {
  const restaurantId = req.params.restaurantId || req.body.restaurantId || req.query.restaurantId;
  
  if (!restaurantId) {
    return next();
  }

  if (req.user && req.user.restaurantId && req.user.restaurantId.toString() !== restaurantId.toString()) {
    return res.status(403).json({
      success: false,
      message: 'Forbidden: You do not have access to this restaurant\'s data'
    });
  }
  return next();
};

module.exports = { protect, restrictTo, verifyTenant };
