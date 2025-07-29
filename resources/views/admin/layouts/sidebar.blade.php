<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
  <style>
    .app-sidebar {
      background-color: #1e293b;
      color: #cbd5e1;
      min-height: 100vh;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      display: flex;
      flex-direction: column;
    }
    .sidebar-brand a {
      color: #38bdf8;
      font-weight: 700;
      font-size: 1.15rem;
      text-decoration: none;
    }
    .sidebar-wrapper {
      flex: 1 1 auto;
      overflow-y: auto;
      padding-right: 0.5rem;
    }
    .nav-link {
      color: #cbd5e1;
      padding: 0.55rem 0.85rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-weight: 500;
      font-size: 0.88rem;
      border-radius: 0.35rem;
      transition: background-color 0.25s ease, color 0.25s ease;
      text-decoration: none;
    }
    .nav-link:hover {
      background-color: #2563eb;
      color: #ffffff;
    }
    .nav-link.active {
      background-color: #2563eb !important;
      color: #ffffff !important;
      font-weight: 600;
      box-shadow: 0 2px 6px rgba(37, 99, 235, 0.4);
    }
    .nav-link .nav-icon {
      font-size: 1rem;
      color: #60a5fa;
    }
    .nav-treeview {
      display: none;
      margin-left: 1.2rem;
      flex-direction: column;
      padding-left: 0.5rem;
      border-left: 1px dashed #3b3b3b;
      margin-top: 8px; /* gap between parent link and dropdown */
    }
    .nav-item.menu-open > .nav-treeview {
      display: flex;
    }
    .nav-link i.right {
      margin-left: auto;
      font-size: 0.9rem;
      color: #94a3b8;
      transition: transform 0.3s ease;
    }
    .nav-item.menu-open > a > i.right {
      transform: rotate(180deg);
    }
    /* gap between main nav items */
    .nav > .nav-item {
      margin-bottom: 8px;
    }
    /* gap between dropdown links */
    .nav-treeview > .nav-item {
      margin-bottom: 6px;
    }
  </style>

  @php $adminUser = auth()->guard('admin')->user(); @endphp

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
      <!-- MAIN -->
      <h6 class="text-gray-400 px-3 mt-3 mb-2" style="letter-spacing: 1px; font-weight: 600; font-size: 0.75rem;">MAIN</h6>
      <ul class="nav flex-column">

        <li class="nav-item">
          <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="nav-icon bi bi-speedometer"></i>
            <span>Dashboard</span>
          </a>
        </li>

        <li class="nav-item has-treeview {{ request()->routeIs('admin.users.*') ? 'menu-open' : '' }}">
          <a href="#" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-person-circle"></i>
            <span>{{ $adminUser && $adminUser->role === 'super_admin' ? 'Admin Management' : 'Profile Management' }}</span>
            <i class="right bi bi-caret-down-fill"></i>
          </a>
          <ul class="nav nav-treeview">
            @if($adminUser && $adminUser->role === 'super_admin')
              <li class="nav-item">
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                  <i class="bi bi-people nav-icon"></i>
                  <span>All Admins</span>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('admin.users.trash') }}" class="nav-link {{ request()->routeIs('admin.users.trash') ? 'active' : '' }}">
                  <i class="bi bi-trash3-fill nav-icon"></i>
                  <span>Trash</span>
                </a>
              </li>
            @else
              <li class="nav-item">
                <a href="{{ route('admin.users.edit', $adminUser->id) }}" class="nav-link {{ request()->routeIs('admin.users.edit') ? 'active' : '' }}">
                  <i class="bi bi-person nav-icon"></i>
                  <span>My Profile</span>
                </a>
              </li>
            @endif
          </ul>
        </li>

        <!-- MANAGEMENT -->
        <h6 class="text-gray-400 px-3 mt-3 mb-2" style="letter-spacing: 1px; font-weight: 600; font-size: 0.75rem;">MANAGEMENT</h6>

        <li class="nav-item has-treeview {{ request()->routeIs('admin.orders.*') ? 'menu-open' : '' }}">
          <a href="#" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-box-seam-fill"></i>
            <span>Order Management</span>
            <i class="right bi bi-caret-down-fill"></i>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.index') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check-fill nav-icon"></i>
                <span>All Orders</span>
              </a>
            </li>
            @if($adminUser && $adminUser->role === 'super_admin')
              <li class="nav-item">
                <a href="{{ route('admin.orders.trash') }}" class="nav-link {{ request()->routeIs('admin.orders.trash') ? 'active' : '' }}">
                  <i class="bi bi-trash3-fill nav-icon"></i>
                  <span>Trashed Orders</span>
                </a>
              </li>
            @endif
          </ul>
        </li>

        <li class="nav-item">
          <a href="{{ route('admin.domain_prices.index') }}" class="nav-link {{ request()->routeIs('admin.domain_prices.*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-currency-dollar"></i>
            <span>Domain Price Management</span>
          </a>
        </li>

        <li class="nav-item has-treeview {{ request()->routeIs('admin.invoices.*') ? 'menu-open' : '' }}">
          <a href="#" class="nav-link {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-receipt"></i>
            <span>Invoice Management</span>
            <i class="right bi bi-caret-down-fill"></i>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('admin.invoices.index') }}" class="nav-link {{ request()->routeIs('admin.invoices.index') ? 'active' : '' }}">
                <i class="bi bi-card-list nav-icon"></i>
                <span>All Invoices</span>
              </a>
            </li>
            @if($adminUser && $adminUser->role === 'super_admin')
              <li class="nav-item">
                <a href="{{ route('admin.invoices.report')}}" class="nav-link {{ request()->routeIs('admin.invoices.report') ? 'active' : '' }}">
                  <i class="bi bi-bar-chart-line nav-icon"></i>
                  <span>Invoice Report</span>
                </a>
              </li>
            @endif
          </ul>
        </li>

        <li class="nav-item has-treeview {{ request()->routeIs('admin.verification.*') ? 'menu-open' : '' }}">
          <a href="#" class="nav-link {{ request()->routeIs('admin.verification.*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-file-earmark-check-fill"></i>
            <span>Payment Verification</span>
            <i class="right bi bi-caret-down-fill"></i>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              {{-- Replace 1 with dynamic order ID as needed --}}
              <a href="{{ route('admin.verification.create') }}" class="nav-link {{ request()->routeIs('admin.verification.create') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-check-fill nav-icon"></i>
                <span>Submit Verification</span>
              </a>
            </li>

          </ul>
        </li>

        <li class="nav-item has-treeview {{ request()->routeIs('admin.sms.*') ? 'menu-open' : '' }}">
          <a href="#" class="nav-link {{ request()->routeIs('admin.sms.*') ? 'active' : '' }}">
            <i class="nav-icon bi bi-chat-dots"></i>
            <span>SMS</span>
            <i class="right bi bi-caret-down-fill"></i>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('admin.sms.template') }}" class="nav-link {{ request()->routeIs('admin.sms.template') ? 'active' : '' }}">
                <i class="bi bi-file-text nav-icon"></i>
                <span>SMS Template</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('admin.sms.send') }}" class="nav-link {{ request()->routeIs('admin.sms.send') ? 'active' : '' }}">
                <i class="bi bi-send nav-icon"></i>
                <span>Send SMS</span>
              </a>
            </li>
            @if($adminUser && $adminUser->role === 'super_admin')
              <li class="nav-item">
                <a href="{{ route('admin.sms.report') }}" class="nav-link {{ request()->routeIs('admin.sms.report') ? 'active' : '' }}">
                  <i class="bi bi-bar-chart-line nav-icon"></i>
                  <span>SMS Report</span>
                </a>
              </li>
            @endif
          </ul>
        </li>

      </ul>
    </nav>
  </div>
</aside>

<!-- JavaScript to handle sidebar dropdown -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.nav-item.has-treeview > a').forEach(link => {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        const parent = this.closest('.nav-item');
        parent.classList.toggle('menu-open');
      });
    });
  });
</script>
