
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


  <script>

    document.addEventListener('DOMContentLoaded', function () {
      var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
      dropdownElementList.forEach(function (dropdownToggleEl) {
        new bootstrap.Dropdown(dropdownToggleEl);
      });
    });
  </script>
</body>
</html>
