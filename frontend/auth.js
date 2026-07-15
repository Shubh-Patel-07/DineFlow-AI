const API_URL = 'http://localhost:5000/api/auth';

// Helper to determine if we should run in local simulated mock mode
const isLocalMock = () => {
  if (window.location.protocol === 'file:') return true;
  return false; 
};

// Local storage simulated DB
const getLocalUsers = () => JSON.parse(localStorage.getItem('sim_users') || '[]');
const saveLocalUsers = (users) => localStorage.setItem('sim_users', JSON.stringify(users));

// Elements
const signupForm = document.getElementById('signupForm');
const loginForm = document.getElementById('loginForm');
const errorMessage = document.getElementById('errorMessage');
const submitBtn = document.getElementById('submitBtn');
const spinner = document.getElementById('spinner');
const btnText = document.getElementById('btnText');

const showError = (message) => {
  if (errorMessage) {
    errorMessage.textContent = message;
    errorMessage.style.display = 'block';
  }
};

const hideError = () => {
  if (errorMessage) {
    errorMessage.style.display = 'none';
  }
};

const setSubmitting = (isSubmitting, text) => {
  if (submitBtn) {
    submitBtn.disabled = isSubmitting;
  }
  if (spinner) {
    spinner.style.display = isSubmitting ? 'inline-block' : 'none';
  }
  if (btnText) {
    btnText.textContent = text;
  }
};

// Signup execution
if (signupForm) {
  signupForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    hideError();

    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const role = document.getElementById('role').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    if (password !== confirmPassword) {
      showError('Passwords do not match');
      return;
    }

    if (password.length < 6) {
      showError('Password must be at least 6 characters long');
      return;
    }

    setSubmitting(true, 'Creating account...');

    // A. Mock simulation if running offline (file://)
    if (isLocalMock()) {
      setTimeout(() => {
        const users = getLocalUsers();
        if (users.find(u => u.email === email)) {
          showError('User already exists (Offline Mock)');
          setSubmitting(false, 'Sign Up');
          return;
        }

        const newUser = { name, email, role, password };
        users.push(newUser);
        saveLocalUsers(users);

        localStorage.setItem('token', 'mock_token_' + Date.now());
        localStorage.setItem('user', JSON.stringify({ name, email, role }));

        alert(`Welcome, ${name}! Registration successful (Offline Simulation).`);
        window.location.href = 'dashboard.html';
      }, 800);
      return;
    }

    // B. Call Real Server API
    try {
      const response = await fetch(`${API_URL}/signup`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name, email, role, password })
      });

      const result = await response.json();

      if (!response.ok) {
        throw new Error(result.message || 'Signup failed');
      }

      localStorage.setItem('token', result.data.token);
      localStorage.setItem('user', JSON.stringify({
        name: result.data.name,
        email: result.data.email,
        role: result.data.role
      }));

      alert(`Welcome, ${result.data.name}! Signup successful.`);
      window.location.href = 'dashboard.html';
    } catch (err) {
      if (err.message.includes('Failed to fetch')) {
        console.log('Server unreachable. Running offline simulation...');
        const users = getLocalUsers();
        if (users.find(u => u.email === email)) {
          showError('User already exists (Offline Mock)');
          setSubmitting(false, 'Sign Up');
          return;
        }
        users.push({ name, email, role, password });
        saveLocalUsers(users);
        localStorage.setItem('token', 'mock_token_' + Date.now());
        localStorage.setItem('user', JSON.stringify({ name, email, role }));
        alert(`Welcome, ${name}! Registration successful (Offline Fallback).`);
        window.location.href = 'dashboard.html';
      } else {
        showError(err.message);
        setSubmitting(false, 'Sign Up');
      }
    }
  });
}

// Login execution
if (loginForm) {
  loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    hideError();

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    setSubmitting(true, 'Logging in...');

    // A. Mock simulation if running offline (file://)
    if (isLocalMock()) {
      setTimeout(() => {
        const users = getLocalUsers();
        const user = users.find(u => u.email === email && u.password === password);
        if (!user) {
          showError('Invalid credentials (Offline Mock)');
          setSubmitting(false, 'Log In');
          return;
        }

        localStorage.setItem('token', 'mock_token_' + Date.now());
        localStorage.setItem('user', JSON.stringify({ name: user.name, email: user.email, role: user.role }));

        alert(`Welcome back, ${user.name}! Login successful (Offline Simulation).`);
        window.location.href = 'dashboard.html';
      }, 800);
      return;
    }

    // B. Call Real Server API
    try {
      const response = await fetch(`${API_URL}/login`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password })
      });

      const result = await response.json();

      if (!response.ok) {
        throw new Error(result.message || 'Login failed');
      }

      localStorage.setItem('token', result.data.token);
      localStorage.setItem('user', JSON.stringify({
        name: result.data.name,
        email: result.data.email,
        role: result.data.role
      }));

      alert(`Welcome back, ${result.data.name}! Login successful.`);
      window.location.href = 'dashboard.html';
    } catch (err) {
      if (err.message.includes('Failed to fetch')) {
        console.log('Server unreachable. Running offline simulation...');
        const users = getLocalUsers();
        const user = users.find(u => u.email === email && u.password === password);
        if (!user) {
          showError('Invalid credentials (Offline Mock)');
          setSubmitting(false, 'Log In');
          return;
        }
        localStorage.setItem('token', 'mock_token_' + Date.now());
        localStorage.setItem('user', JSON.stringify({ name: user.name, email: user.email, role: user.role }));
        alert(`Welcome back, ${user.name}! Login successful (Offline Fallback).`);
        window.location.href = 'dashboard.html';
      } else {
        showError(err.message);
        setSubmitting(false, 'Log In');
      }
    }
  });
}
