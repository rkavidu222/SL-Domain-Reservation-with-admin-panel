<nav class="app-header navbar navbar-expand bg-body">
  <div class="container-fluid">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
          <i class="bi bi-list"></i>
        </a>
      </li>
    </ul>

    <ul class="navbar-nav ms-auto">
      <!-- Notifications Dropdown -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-bs-toggle="dropdown" href="#">
          <i class="bi bi-bell-fill"></i>
          <span class="navbar-badge badge text-bg-warning">15</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
          <span class="dropdown-item dropdown-header">15 Notifications</span>
          <a href="#" class="dropdown-item dropdown-footer"> See All Notifications </a>
        </div>
      </li>

      <!-- User Menu Dropdown -->
      <li class="nav-item dropdown user-menu">
        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
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
          <li class="user-footer">


            <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
              @csrf
              <button type="submit" class="btn btn-default btn-flat float-end">
                Sign out
              </button>
            </form>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</nav>
