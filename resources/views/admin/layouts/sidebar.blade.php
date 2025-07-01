<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
  <style>
    /* Sidebar background and font */
    .app-sidebar {
      background-color: #1e293b; /* Dark slate blue-gray */
      color: #cbd5e1; /* Light gray text */
      min-height: 100vh;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Sidebar brand */
    .sidebar-brand a {
      color: #38bdf8; /* Bright sky blue */
      font-weight: 700;
      font-size: 1.25rem;
      text-decoration: none;
    }

    .sidebar-brand a:hover {
      color: #60a5fa;
    }

    /* Nav links */
    .nav-link {
      color: #cbd5e1;
      padding: 0.65rem 1rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      font-weight: 500;
      font-size: 0.95rem;
      border-radius: 0.375rem;
      transition: background-color 0.25s ease, color 0.25s ease;
    }

    .nav-link:hover {
      background-color: #2563eb; /* Bright blue */
      color: #ffffff;
      text-decoration: none;
    }

    .nav-link .nav-icon {
      font-size: 1.2rem;
      color: #60a5fa;
      transition: color 0.25s ease;
    }

    .nav-link:hover .nav-icon {
      color: #ffffff;
    }

    /* Active nav link */
    .nav-link.active {
      background-color: #2563eb !important;
      color: #ffffff !important;
      font-weight: 700;
      box-shadow: 0 2px 8px rgb(37 99 235 / 0.5);
    }

    .nav-link.active .nav-icon {
      color: #ffffff !important;
    }

    /* Submenu */
    .nav-treeview {
      margin-top: 0.35rem;
      margin-left: 1.5rem;
      display: flex;
      flex-direction: column;
      gap: 0.4rem;
    }

    .nav-treeview .nav-link {
      padding-left: 1rem;
      font-size: 0.9rem;
      color: #94a3b8; /* lighter gray */
    }

    .nav-treeview .nav-link:hover {
      background-color: #3b82f6; /* lighter blue */
      color: #fff;
    }

    /* Caret icon */
    .nav-link i.right {
      margin-left: auto;
      font-size: 1rem;
      color: #94a3b8;
      transition: transform 0.3s ease;
    }

    .nav-item.has-treeview.menu-open > a > i.right {
      transform: rotate(180deg);
      color: #60a5fa;
    }
  </style>

  <div class="sidebar-brand d-flex justify-content-between align-items-center px-3 py-3">
    <a href="{{ route('admin.dashboard') }}" class="nav-link fw-bold">
      <span class="brand-text">SriLankaHosting</span>
    </a>
    <button class="btn btn-sm btn-link text-white d-lg-none" data-lte-toggle="sidebar" aria-label="Close Sidebar">
      <i class="bi bi-x-lg fs-4"></i>
    </button>
  </div>

  <div class="sidebar-wrapper px-2">
    <nav class="mt-3">

      <!-- Section 1: Main -->
      <h6 class="text-gray-400 px-3 mt-3 mb-2" style="letter-spacing: 1px; font-weight: 600;">MAIN</h6>
      <ul class="nav sidebar-menu flex-column mb-4" data-lte-toggle="treeview" role="menu" data-accordion="false">

        <li class="nav-item">
          <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="nav-icon bi bi-speedometer"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <li class="nav-item has-treeview {{ request()->routeIs('admin.users.*') ? 'menu-open' : '' }}">
          <a href="#" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-person-circle"></i>
            <p>
              Admin Management
            </p>
          </a>
          <ul class="nav nav-treeview flex-column">
            <li class="nav-item">
              <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                <i class="nav-icon bi bi-people"></i>
                <p>All Admins</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('admin.users.trash') }}" class="nav-link {{ request()->routeIs('admin.users.trash') ? 'active' : '' }}">
                <i class="nav-icon bi bi-trash3-fill"></i>
                <p>Trash</p>
              </a>
            </li>
          </ul>
        </li>

      </ul>

      <!-- Section 2: Management -->
      <h6 class="text-gray-400 px-3 mt-3 mb-2" style="letter-spacing: 1px; font-weight: 600;">MANAGEMENT</h6>
      <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">

        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon bi bi-people-fill"></i>
            <p>Customer Management</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon bi bi-box-seam-fill"></i>
            <p>Order Management</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon bi bi-receipt"></i>
            <p>Invoice Management</p>
          </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('admin.domain_prices.index') }}" class="nav-link">
                <i class="nav-icon bi bi-currency-dollar"></i>
                <p>Domain Price Management</p>
            </a>
        </li>



        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon bi bi-chat-dots"></i>
            <p>SMS</p>
          </a>
        </li>

      </ul>

    </nav>
  </div>
</aside>
