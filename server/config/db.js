const mongoose = require('mongoose');

const connectDB = async () => {
  try {
    const conn = await mongoose.connect(process.env.MONGO_URI || 'mongodb://localhost:27017/dineflow', {
      serverSelectionTimeoutMS: 3000
    });
    console.log(`MongoDB Connected: ${conn.connection.host}`);
    process.env.USE_MOCK_DB = 'false';
  } catch (error) {
    console.error(`Error connecting to MongoDB: ${error.message}`);
    console.log('[WARNING] Fallback: Starting backend in MOCK DB mode (In-Memory storage).');
    process.env.USE_MOCK_DB = 'true';
  }
};

module.exports = connectDB;
