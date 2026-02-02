<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Login | Student Result System</title>

  <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

  <link rel="stylesheet" href="{{ asset('bootstrap-5.3.8-dist/css/bootstrap.min.css') }}">

  <link rel="stylesheet" href="{{ asset('fontawesome-free-6.7.2-web/css/all.min.css') }}">
  
  <link rel="stylesheet" href="{{ asset('login-css/login-style.css') }}">

  <style>
    /* Minor style adjustment to ensure the eye button matches the input */
    .input-group-text {
      background-color: white;
      cursor: pointer;
      border-left: none;
    }
    #password {
      border-right: none;
    }
    .input-group:focus-within .form-control,
    .input-group:focus-within .input-group-text {
      border-color: #679767 important!; 
    }
  </style>
</head>
<body>
  <div class="login-container">
    <section class="description-section">
      <div class="description-content">
        <h2>Streamline Student Results Compilation</h2>
        <p><span style="color: #FFD700; font-weight: bold; letter-spacing: 1px;">ResultIgniter</span> provides educators with powerful tools to efficiently compile, analyze, and distribute student results.</p>

        <div class="features">
          <div class="feature">
            <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
            <div class="feature-text">
              <h3>Performance Analytics</h3>
              <p>Gain insights with detailed analytics and visual reports.</p>
            </div>
          </div>
          <div class="feature">
            <div class="feature-icon"><i class="fas fa-lock"></i></div>
            <div class="feature-text">
              <h3>Secure Data Management</h3>
              <p>Your data is protected with enterprise-grade security.</p>
            </div>
          </div>
          <div class="feature">
            <div class="feature-icon"><i class="fas fa-bolt"></i></div>
            <div class="feature-text">
              <h3>Fast Processing</h3>
              <p>Generate results and reports in seconds.</p>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <div class="login-section">
      <div class="logo">
        <div class="logo-placeholder" style="width: 80px; height: 80px; background-color: var(--primary-color); border-radius: 50%; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center;">
          <i class="fas fa-graduation-cap" style="color: white; font-size: 36px;"></i>
        </div>
        <h1>Login to Your Account</h1>
      </div>

      @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
      @endif
      @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <div class="input-group">
            <input type="email" class="form-control" id="email" name="email"
                   placeholder="Enter your email" value="{{ old('email') }}" required autofocus>
          </div>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <div class="input-group">
            <input type="password" class="form-control" id="password" name="password"
                   placeholder="Enter password" required>
            <span class="input-group-text" id="togglePassword">
              <i class="fas fa-eye" id="eyeIcon"></i>
            </span>
          </div>
        </div>

        <div class="d-grid mt-4">
          <button type="submit" class="btn btn-primary" id="loginButton" style="background-color: #679767; border-color: #679767; color: white;">
            Login
          </button>
        </div>

        <a href="{{ route('password.request') }}" class="forgot-password">Forgot password?</a>
      </form>

      <div class="footer">
        <p>&copy; 2025 ResultIgniter. All rights reserved.</p>
      </div>
    </div>
  </div>

  <script src="{{ asset('bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js') }}"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.querySelector("form");
    const loginButton = document.getElementById("loginButton");
    const passwordInput = document.getElementById("password");
    const togglePassword = document.getElementById("togglePassword");
    const eyeIcon = document.getElementById("eyeIcon");

    // Toggle Password Visibility
    togglePassword.addEventListener("click", function () {
      const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
      passwordInput.setAttribute("type", type);
      
      // Toggle eye icons
      eyeIcon.classList.toggle("fa-eye");
      eyeIcon.classList.toggle("fa-eye-slash");
    });

    // Form Submission Loading State
    loginForm.addEventListener("submit", function () {
      loginButton.style.backgroundColor = "#679767";
      loginButton.style.borderColor = "#679767";
      loginButton.style.color = "white";

      loginButton.disabled = true;
      loginButton.innerHTML = `
        <i class="fas fa-sync-alt fa-spin me-2"></i> Redirecting...
      `;
    });
  });
</script>

</body>
</html>