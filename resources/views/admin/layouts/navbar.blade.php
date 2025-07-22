<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Navbar</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous" />
  <!-- Bootstrap Icons CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
    .navbar-custom-bg {
      background-color: #3a5068;
      z-index: 1050;
    }

    .navbar-custom-bg .nav-link,
    .navbar-custom-bg .nav-link i,
    .navbar-custom-bg .user-menu > a {
      color: #f0f4f8 !important;
      font-weight: 500;
      transition: color 0.3s ease;
    }

    .navbar-custom-bg .nav-link:hover,
    .navbar-custom-bg .user-menu > a:hover {
      color: #ffffff !important;
      text-decoration: none;
    }

    .navbar-custom-bg .navbar-badge {
      background-color: #f1c40f;
      color: #2c3e50;
      font-weight: 700;
    }

    .user-header.text-bg-primary {
      background-color: #2c3e50 !important;
      color: #f0f4f8 !important;
      text-align: center;
      padding: 1rem;
    }

    .user-header.text-bg-primary p,
    .user-header.text-bg-primary small {
      color: #f0f4f8 !important;
      margin: 0;
    }

    .user-header img {
      width: 80px;
      height: 80px;
      object-fit: cover;
      margin-bottom: 0.5rem;
    }

    .btn-flat {
      background: transparent;
      border: none;
      padding: 0.5rem 1rem;
      color: #2c3e50;
      cursor: pointer;
      font-weight: 600;
      transition: color 0.3s ease;
    }

    .btn-flat:hover {
      color: #1a252f;
      text-decoration: underline;
    }

    body {
      padding-top: 56px;
    }


    .dropdown-menu {
      background-color: #ffffff;
      border: 1px solid #2c3e50;
      z-index: 1000;
    }

    .dropdown-menu-end {
      right: 0;
      left: auto;
    }
  </style>
</head>
<body>
  <nav class="app-header navbar navbar-expand fixed-top navbar-custom-bg">
    <div class="container-fluid">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button" aria-label="Toggle sidebar">
            <i class="bi bi-list"></i>
          </a>
        </li>
      </ul>

      <ul class="navbar-nav ms-auto">

        <!-- User Menu Dropdown -->
        <li class="nav-item dropdown user-menu">
          <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
            <img src="{{ asset(auth()->guard('admin')->user()->profile_image ?? 'admin/assets/img/user.png') }}" class="user-image rounded-circle shadow" alt="{{ auth()->guard('admin')->user()->name }} Profile Image" onerror="this.src='https://via.placeholder.com/80';" />
            <span>{{ auth()->guard('admin')->user()->name }}</span>
          </a>

          <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end" aria-labelledby="userDropdown">
            <li class="user-header text-bg-primary">
              <img src="{{ asset(auth()->guard('admin')->user()->profile_image ?? 'admin/assets/img/user.png') }}" class="rounded-circle shadow" alt="{{ auth()->guard('admin')->user()->name }} Profile Image" onerror="this.src='https://via.placeholder.com/80';" />
              <p>
                {{ auth()->guard('admin')->user()->name }} – Admin
                <small>Member since {{ auth()->guard('admin')->user()->created_at->format('M. Y') }}</small>
              </p>
            </li>
            <li class="user-footer d-flex justify-content-end p-3">
              <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-flat">
                  Sign out
                </button>
              </form>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>

  <!-- Bootstrap JS and Popper.js -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js" integrity="sha384-zYPOMqeu1DAVkHiLqWBUTcbYkn6J9Kr3q5t2f0w58Gihl7fO1e5B6z5fO1e5B6z5" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js" integrity="sha384-fbbOQedDUMZZ5KreZpsbe1LCU7U5n3j6u5z6u5z6u5z6u5z6u5z6u5z6u5z6u5z" crossorigin="anonymous"></script>

  <script>
    // Ensure Bootstrap dropdowns are initialized
    document.addEventListener('DOMContentLoaded', function () {
      var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
      dropdownElementList.forEach(function (dropdownToggleEl) {
        new bootstrap.Dropdown(dropdownToggleEl);
      });
    });
  </script>
</body>
</html>
