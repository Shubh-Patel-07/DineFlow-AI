const User = require('../models/User');
const jwt = require('jsonwebtoken');
const bcrypt = require('bcryptjs');
const { mockUsers } = require('../config/mockDb');

const generateToken = (id) => {
  return jwt.sign({ id }, process.env.JWT_SECRET || 'secret123', {
    expiresIn: '30d',
  });
};

const signupUser = async (req, res) => {
  try {
    const { name, email, password, role, restaurantId } = req.body;

    if (!name || !email || !password) {
      return res.status(400).json({ success: false, message: 'Please enter all fields' });
    }

    if (process.env.USE_MOCK_DB === 'true') {
      const userExists = mockUsers.find(u => u.email === email);
      if (userExists) {
        return res.status(400).json({ success: false, message: 'User already exists (Mock DB)' });
      }

      const salt = await bcrypt.genSalt(10);
      const hashedPassword = await bcrypt.hash(password, salt);

      const user = {
        _id: 'mock_' + Date.now().toString(),
        name,
        email,
        password: hashedPassword,
        role: role || 'customer',
        restaurantId: restaurantId || null,
        createdAt: new Date(),
        updatedAt: new Date()
      };

      mockUsers.push(user);

      return res.status(201).json({
        success: true,
        data: {
          _id: user._id,
          name: user.name,
          email: user.email,
          role: user.role,
          restaurantId: user.restaurantId,
          token: generateToken(user._id),
        },
      });
    } else {
      const userExists = await User.findOne({ email });

      if (userExists) {
        return res.status(400).json({ success: false, message: 'User already exists' });
      }

      const user = await User.create({
        name,
        email,
        password,
        role: role || 'customer',
        restaurantId: restaurantId || null,
      });

      if (user) {
        return res.status(201).json({
          success: true,
          data: {
            _id: user._id,
            name: user.name,
            email: user.email,
            role: user.role,
            restaurantId: user.restaurantId,
            token: generateToken(user._id),
          },
        });
      } else {
        return res.status(400).json({ success: false, message: 'Invalid user data' });
      }
    }
  } catch (error) {
    console.error(error);
    return res.status(500).json({ success: false, message: 'Server error' });
  }
};

const loginUser = async (req, res) => {
  try {
    const { email, password } = req.body;

    if (!email || !password) {
      return res.status(400).json({ success: false, message: 'Please enter all fields' });
    }

    if (process.env.USE_MOCK_DB === 'true') {
      const user = mockUsers.find(u => u.email === email);
      if (user && (await bcrypt.compare(password, user.password))) {
        return res.status(200).json({
          success: true,
          data: {
            _id: user._id,
            name: user.name,
            email: user.email,
            role: user.role,
            restaurantId: user.restaurantId,
            token: generateToken(user._id),
          },
        });
      } else {
        return res.status(401).json({ success: false, message: 'Invalid credentials (Mock DB)' });
      }
    } else {
      const user = await User.findOne({ email }).select('+password');

      if (user && (await user.matchPassword(password))) {
        return res.status(200).json({
          success: true,
          data: {
            _id: user._id,
            name: user.name,
            email: user.email,
            role: user.role,
            restaurantId: user.restaurantId,
            token: generateToken(user._id),
          },
        });
      } else {
        return res.status(401).json({ success: false, message: 'Invalid credentials' });
      }
    }
  } catch (error) {
    console.error(error);
    return res.status(500).json({ success: false, message: 'Server error' });
  }
};

const getMe = async (req, res) => {
  try {
    if (process.env.USE_MOCK_DB === 'true') {
      const user = mockUsers.find(u => u._id === req.user._id);
      if (!user) {
        return res.status(404).json({ success: false, message: 'User not found in mock DB' });
      }
      const { password, ...userWithoutPassword } = user;
      return res.status(200).json({
        success: true,
        data: userWithoutPassword,
      });
    } else {
      const user = await User.findById(req.user.id);
      return res.status(200).json({
        success: true,
        data: user,
      });
    }
  } catch (error) {
    console.error(error);
    return res.status(500).json({ success: false, message: 'Server error' });
  }
};

module.exports = {
  signupUser,
  loginUser,
  getMe,
};
