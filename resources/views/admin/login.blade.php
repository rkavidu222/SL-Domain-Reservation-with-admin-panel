<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Login | LK Domain Reservation</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />

  <style>
    body {
      background: linear-gradient(to right, #eef2f7, #f4f7fb);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .card-login {
      background: #fff;
      border-radius: 1rem;
      padding: 2rem 2.5rem;
      max-width: 420px;
      width: 100%;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.05);
      animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(15px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .card-login h3 {
      text-align: center;
      color: #0d6efd;
      font-weight: 600;
      margin-bottom: 1.5rem;
    }

    .form-label i {
      color: #0d6efd;
      margin-right: 6px;
    }

    .form-control:focus {
      border-color: #0d6efd;
      box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.2);
    }

    .btn-login {
      background-color: #2563eb;
      color: white;
      font-weight: 500;
      width: 100%;
      padding: 0.5rem 1rem;
      border-radius: 0.5rem;
      transition: background-color 0.3s ease, color 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .btn-login:hover {
      background-color: #1e40af;
      color: white;
    }

    .form-footer {
      text-align: center;
      margin-top: 1rem;
      font-size: 0.9rem;
    }

    .form-footer a {
      color: #0d6efd;
      text-decoration: none;
    }

    .form-footer a:hover {
      text-decoration: underline;
    }

    .alert {
      font-size: 0.9rem;
    }

    @media (max-width: 480px) {
      .card-login {
        padding: 1.5rem;
        margin: 1rem;
      }
    }
  </style>
</head>
<body>
  <div class="card-login">
    <h3><i class="bi bi-shield-lock-fill me-1"></i>Admin Login</h3>

    {{-- Flash Messages --}}
    @if(Session::has('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ Session::get('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    @if(Session::has('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ Session::get('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <form method="POST" action="{{ route('admin.login.submit') }}" id="loginForm">
      @csrf

      <div class="mb-3">
        <label for="email" class="form-label"><i class="bi bi-envelope-fill"></i>Email</label>
        <input type="email" name="email" id="email" class="form-control" required autofocus />
      </div>

      <div class="mb-4">
        <label for="password" class="form-label"><i class="bi bi-lock-fill"></i>Password</label>
        <div class="input-group">
            <input type="password" name="password" id="password" class="form-control" required />
            <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
            <i class="bi bi-eye-fill" id="eyeIcon"></i>
            </span>
        </div>
        </div>


      <button type="submit" class="btn btn-login" id="loginBtn">
        <span id="btnText">
          <i class="bi bi-box-arrow-in-right me-1"></i>Login
        </span>
        <span
          id="btnSpinner"
          class="spinner-border spinner-border-sm ms-2 d-none"
          role="status"
          aria-hidden="true"
        ></span>
      </button>
    </form>

    {{--
	  <div class="form-footer">
		Don't have an account? <a href="{{ route('admin.register') }}">Register here</a>
	  </div>
	--}}


  <!-- Bootstrap 5 JS Bundle (for alert dismiss) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    const form = document.getElementById('loginForm');
    const loginBtn = document.getElementById('loginBtn');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');

    form.addEventListener('submit', function () {
        loginBtn.disabled = true;
        btnText.textContent = 'Logging in...';
        btnSpinner.classList.remove('d-none');
    });

    // Password toggle logic
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    togglePassword.addEventListener('click', function () {
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        eyeIcon.classList.toggle('bi-eye-fill');
        eyeIcon.classList.toggle('bi-eye-slash-fill');
    });
</script>

</body>
</html>
