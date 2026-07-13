import React from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate, Link } from 'react-router-dom';
import Login from './pages/Login';
import Signup from './pages/Signup';
import './App.css';

// A simple landing page placeholder until Member 2 implements it
function LandingPlaceholder() {
  return (
    <div style={{ display: 'flex', flexDirection: 'column', justifyContent: 'center', alignItems: 'center', height: '100vh', gap: '1.5rem', background: '#0a0a0c', padding: '1rem', textAlign: 'center' }}>
      <h1 style={{ fontFamily: 'Outfit, sans-serif', fontSize: '3rem', fontWeight: 800 }}>
        Dine<span style={{ color: 'var(--color-primary)' }}>Flow</span> AI
      </h1>
      <p style={{ color: '#9ca3af', maxWidth: '500px', fontSize: '1.1rem', lineHeight: '1.6' }}>
        Smart QR-based Restaurant Ordering & Management System. Experience seamless dining at your fingertips.
      </p>
      <div style={{ display: 'flex', gap: '1rem' }}>
        <Link to="/login" className="btn-primary" style={{ textDecoration: 'none' }}>Log In</Link>
        <Link to="/signup" className="btn-primary" style={{ textDecoration: 'none', background: 'rgba(255,255,255,0.05)', border: '1px solid rgba(255,255,255,0.1)', boxShadow: 'none' }}>Sign Up</Link>
      </div>
    </div>
  );
}

// A simple dashboard placeholder until Member 4 implements it
function DashboardPlaceholder() {
  const user = JSON.parse(localStorage.getItem('user') || '{}');
  const token = localStorage.getItem('token');

  if (!token) {
    return <Navigate to="/login" replace />;
  }

  const handleLogout = () => {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    window.location.href = '/login';
  };

  return (
    <div style={{ display: 'flex', flexDirection: 'column', justifyContent: 'center', alignItems: 'center', height: '100vh', gap: '1.5rem', background: '#0a0a0c', padding: '1rem', textAlign: 'center' }}>
      <h2 style={{ fontFamily: 'Outfit, sans-serif', fontSize: '2rem' }}>
        Welcome to your Dashboard, <span style={{ color: 'var(--color-primary)' }}>{user.name || 'User'}</span>!
      </h2>
      <p style={{ color: '#9ca3af' }}>Role: <strong>{user.role}</strong> | Email: {user.email}</p>
      <button onClick={handleLogout} className="btn-primary" style={{ background: '#ef4444', boxShadow: '0 4px 14px rgba(239, 68, 68, 0.3)' }}>
        Log Out
      </button>
    </div>
  );
}

function App() {
  return (
    <Router>
      <Routes>
        <Route path="/" element={<LandingPlaceholder />} />
        <Route path="/login" element={<Login />} />
        <Route path="/signup" element={<Signup />} />
        <Route path="/dashboard" element={<DashboardPlaceholder />} />
        <Route path="*" element={<Navigate to="/" replace />} />
      </Routes>
    </Router>
  );
}

export default App;
