<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Register | LK Domain Reservation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <style>
        body {
            background: linear-gradient(to right, #eef2f7, #f4f7fb);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-register {
            background: #fff;
            border-radius: 1rem;
            padding: 2rem 2.5rem;
            max-width: 460px;
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
        .card-register h2 {
            text-align: center;
            color: #0d6efd;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        .form-label {
            font-weight: 500;
        }
        .form-control:focus {
            color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.2);
        }
        .btn-register {
            background-color: #2563eb;
            border-color: #2563eb;
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
        .btn-register:hover {
            background-color: #1e40af;
            color: white;
        }
        .alert {
            font-size: 0.9rem;
        }
        .form-footer {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.9rem;
        }
        .form-footer a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
        }
        .form-footer a:hover {
            text-decoration: underline;
        }
        @media (max-width: 480px) {
            .card-register {
                padding: 1.5rem;
                margin: 1rem;
            }
        }
    </style>

</head>
<body>
    <div class="card-register">
        <h2>Admin Register</h2>

        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.register.submit') }}" id="registerForm">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control"
                    value="{{ old('name') }}"
                    required
                    autofocus
                />
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    value="{{ old('email') }}"
                    required
                />
            </div>

            <!-- Auth Code Input -->
            <div class="mb-3">
                <label for="auth_code" class="form-label">Admin Authentication Code</label>
                <input
                    type="text"
                    id="auth_code"
                    name="auth_code"
                    class="form-control"
                    required
                    placeholder="Enter secret code"
                />
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control"
                    required
                />
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="form-control"
                    required
                />
            </div>

            <button type="submit" class="btn btn-register" id="registerBtn">
                <span id="btnText">Register</span>
                <span
                    id="btnSpinner"
                    class="spinner-border spinner-border-sm ms-2 d-none"
                    role="status"
                    aria-hidden="true"
                ></span>
            </button>
        </form>

        <div class="form-footer">
            Already registered? <a href="{{ route('admin.login') }}">Login here</a>
        </div>
    </div>

    <script>
        const form = document.getElementById('registerForm');
        const registerBtn = document.getElementById('registerBtn');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');

        form.addEventListener('submit', function () {
            // Disable the button to prevent multiple submits
            registerBtn.disabled = true;
            // Change button text and show spinner
            btnText.textContent = 'Registering...';
            btnSpinner.classList.remove('d-none');
        });
    </script>
</body>
</html>
