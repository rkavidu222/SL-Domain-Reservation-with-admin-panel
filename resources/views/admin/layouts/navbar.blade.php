<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Navbar Friendly Colors</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Bootstrap Icons CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

  <style>
    .navbar-custom-bg {
      background-color: #3a5068;
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
      color: #f0f4f8;
      cursor: pointer;
      font-weight: 600;
      transition: color 0.3s ease;
    }
    .btn-flat:hover {
      color: #ffffff;
      text-decoration: underline;
    }


    body {
      padding-top: 56px;
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
        <!-- Notifications Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link" data-bs-toggle="dropdown" href="#" aria-haspopup="true" aria-expanded="false" aria-label="Notifications">
            <i class="bi bi-bell-fill"></i>
            <span class="navbar-badge badge text-bg-warning">15</span>
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
            <span class="dropdown-item dropdown-header">15 Notifications</span>
            <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
          </div>
        </li>

        <!-- User Menu Dropdown -->
        <li class="nav-item dropdown user-menu">
          <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <img src="{{ asset('admin/assets/img/user2-160x160.jpg') }}" class="user-image rounded-circle shadow" alt="User Image" />
            {{ auth()->guard('admin')->user()->name }}
          </a>

          <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
            <li class="user-header text-bg-primary">
              <img src="{{ asset('admin/assets/img/user2-160x160.jpg') }}" class="rounded-circle shadow" alt="User Image" />
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
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
