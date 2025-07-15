@extends('admin.layouts.app')

@section('title', 'Order Management')

@section('content')
<style>
    .admin-management-container {
        max-width: 1400px;
        margin: 1rem auto 3rem auto;
        padding: 2rem;
        background: #fff;
        border-radius: 1rem;
        border: 1px solid #dee2e6;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 8px 20px rgba(0, 0, 0, 0.07);
        transition: box-shadow 0.3s ease;
    }

    .admin-management-container:hover {
        box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15), 0 12px 30px rgba(0, 0, 0, 0.1);
    }

    .table-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 0.5rem;
        align-items: flex-end;
    }

    .table-filters > div {
        flex: 1 1 200px;
    }

    .table-filters label {
        font-size: 0.85rem;
        font-weight: 500;
        margin-bottom: 0.25rem;
        display: block;
        color: #495057;
    }

    .table-filters input[type="text"],
    .table-filters select,
    .table-filters input[type="date"] {
        padding: 0.4rem 0.6rem;
        font-size: 0.9rem;
        border-radius: 0.375rem;
        border: 1px solid #ced4da;
        transition: border-color 0.3s ease;
        width: 100%;
    }

    .table-filters input:focus,
    .table-filters select:focus {
        outline: none;
        border-color: #5c7cfa;
        box-shadow: 0 0 0 0.2rem rgba(92, 124, 250, 0.25);
    }

    .filter-buttons {
        display: flex;
        gap: 0.75rem;
        flex: 1 1 100%;
        justify-content: flex-end;
    }

    .filter-buttons button,
    .filter-buttons a.btn {
        padding: 0.4rem 0.9rem;
        font-weight: 600;
        font-size: 0.9rem;
        border-radius: 0.375rem;
        min-width: 100px;
    }

    /* Responsive horizontal scroll for small devices */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    table.table {
        width: 100%;
        border-collapse: collapse;
        min-width: 600px; /* Ensure enough width for mobile scroll */
    }

    table.table thead {
        background-color: #f8f9fa;
    }

    table.table th,
    table.table td {
        padding: 0.75rem 1rem;
        border: 1px solid #dee2e6;
        text-align: center;
        font-size: 0.9rem;
        white-space: nowrap;
    }

    table.table th.text-start,
    table.table td.text-start {
        text-align: left;
        max-width: 150px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    table.table tbody tr:hover {
        background-color: #f1f5fb;
    }

    .btn-sm {
        font-size: 0.8rem;
        padding: 0.3rem 0.6rem;
        border-radius: 0.35rem;
    }

    table.table thead th {
        position: sticky;
        top: 0;
        background: #f8f9fa;
        z-index: 10;
    }

    table.table tbody td:first-child,
    table.table thead th:first-child {
        position: sticky;
        left: 0;
        background: #fff;
        z-index: 11;
        box-shadow: 2px 0 5px -2px rgba(0, 0, 0, 0.1);
    }

    #ordersTab .nav-link {
        color: #1e40af;
        background-color: #dbeafe;
        border: 1px solid transparent;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    #ordersTab .nav-link:hover {
        background-color: #bfdbfe;
        color: #1e3a8a;
    }

    #ordersTab .nav-link.active {
        color: #ffffff !important;
        background-color: #2563eb !important;
        border-color: #2563eb !important;
        font-weight: 600;
    }
</style>

<div class="admin-management-container">
    <h2 class="mb-4 d-flex justify-content-center align-items-center gap-2 text-primary fw-bold flex-wrap text-center">
        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-cart-fill" viewBox="0 0 16 16" width="24" height="24">
            <path d="M0 1.5A.5.5 0 0 1 .5 1h1a.5.5 0 0 1 .485.379L2.89 6H14.5a.5.5 0 0 1 .49.598l-1.5 7A.5.5 0 0 1 13 14H4a.5.5 0 0 1-.491-.408L1.01 2H.5a.5.5 0 0 1-.5-.5z"/>
            <path d="M5 12a2 2 0 1 0 4 0H5z"/>
        </svg>
        Order Management
    </h2>

    {{-- Filter UI --}}
    <form method="GET" action="{{ route('admin.orders.index') }}" class="table-filters" autocomplete="off">
        <div>
            <label for="category">Category</label>
            <select id="category" name="category">
                <option value="">All Categories</option>
                @foreach(['Cat1', 'Cat2', 'Cat3', 'Cat4', 'Cat5'] as $cat)
                    <option value="{{ $cat }}" {{ (isset($category) && $category == $cat) ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="date_from">Date From</label>
            <input type="date" id="date_from" name="date_from" value="{{ old('date_from', $dateFrom ?? '') }}">
        </div>

        <div>
            <label for="date_to">Date To</label>
            <input type="date" id="date_to" name="date_to" value="{{ old('date_to', $dateTo ?? '') }}">
        </div>

        <div>
            <label for="search">Search</label>
            <input type="text" id="search" name="search" placeholder="Customer, email, domain..." value="{{ old('search', $search ?? '') }}">
        </div>

        <div class="filter-buttons">
            <button type="submit" class="btn btn-primary">Search</button>
            @if(request()->hasAny(['search', 'category', 'date_from', 'date_to']))
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Clear</a>
            @endif
        </div>
    </form>

    {{-- Tabs navigation --}}
    <ul class="nav nav-tabs mb-4" id="ordersTab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">
          All Orders ({{ $orders->total() }})
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="paid-tab" data-bs-toggle="tab" data-bs-target="#paid" type="button" role="tab" aria-controls="paid" aria-selected="false">
          Paid Orders ({{ $paidOrders->total() }})
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="false">
          Pending Orders ({{ $pendingOrders->total() }})
        </button>
      </li>
    </ul>

    <div class="tab-content" id="ordersTabContent">
      {{-- All Orders Tab --}}
      <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
        @include('admin.layouts.management.partials.orders_table', ['orders' => $orders])
        @if ($orders->hasPages())
        <div class="d-flex justify-content-end mt-3">
          {{ $orders->appends(request()->query())->links() }}
        </div>
        @endif
      </div>

      {{-- Paid Orders Tab --}}
      <div class="tab-pane fade" id="paid" role="tabpanel" aria-labelledby="paid-tab">
        @include('admin.layouts.management.partials.orders_table', ['orders' => $paidOrders])
        @if ($paidOrders->hasPages())
        <div class="d-flex justify-content-end mt-3">
          {{ $paidOrders->appends(request()->query())->links() }}
        </div>
        @endif
      </div>

      {{-- Pending Orders Tab --}}
      <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
        @include('admin.layouts.management.partials.orders_table', ['orders' => $pendingOrders])
        @if ($pendingOrders->hasPages())
        <div class="d-flex justify-content-end mt-3">
          {{ $pendingOrders->appends(request()->query())->links() }}
        </div>
        @endif
      </div>
    </div>
</div>

{{-- Order Details Modal --}}
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <dl class="row mb-0">
          <dt class="col-sm-4">Order ID</dt>
          <dd class="col-sm-8" id="modal-order-id"></dd>

          <dt class="col-sm-4">Customer Name</dt>
          <dd class="col-sm-8" id="modal-customer-name"></dd>

          <dt class="col-sm-4">Email</dt>
          <dd class="col-sm-8" id="modal-email"></dd>

          <dt class="col-sm-4">Phone</dt>
          <dd class="col-sm-8" id="modal-phone"></dd>

          <dt class="col-sm-4">Domain</dt>
          <dd class="col-sm-8" id="modal-domain"></dd>

          <dt class="col-sm-4">Category</dt>
          <dd class="col-sm-8" id="modal-category"></dd>

          <dt class="col-sm-4">Price</dt>
          <dd class="col-sm-8" id="modal-price"></dd>

          <dt class="col-sm-4">Order Date</dt>
          <dd class="col-sm-8" id="modal-order-date"></dd>

          <dt class="col-sm-4">Payment Status</dt>
          <dd class="col-sm-8" id="modal-payment-status"></dd>
        </dl>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterForm = document.querySelector('.table-filters');

        document.getElementById('category').addEventListener('change', () => filterForm.submit());
        document.getElementById('date_from').addEventListener('change', () => filterForm.submit());
        document.getElementById('date_to').addEventListener('change', () => filterForm.submit());

        const searchInput = document.getElementById('search');
        let timeout = null;
        searchInput.addEventListener('input', function () {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                if (searchInput.value.length >= 2 || searchInput.value.length === 0) {
                    filterForm.submit();
                }
            }, 600);
        });

        // Modal logic for viewing order details
        const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
        document.querySelectorAll('.btn-view-order').forEach(button => {
          button.addEventListener('click', function () {
            const order = JSON.parse(this.dataset.order);

            document.getElementById('modal-order-id').textContent = order.id || '-';
            document.getElementById('modal-customer-name').textContent = `${order.first_name} ${order.last_name ?? ''}`.trim();
            document.getElementById('modal-email').textContent = order.email || '-';
            document.getElementById('modal-phone').textContent = order.mobile || '-';
            document.getElementById('modal-domain').textContent = order.domain_name || '-';
            document.getElementById('modal-category').textContent = order.category || '-';
            document.getElementById('modal-price').textContent = order.price ? 'Rs.' + parseFloat(order.price).toFixed(2) : '-';
            document.getElementById('modal-order-date').textContent = order.created_at ? new Date(order.created_at).toLocaleDateString() : '-';
            document.getElementById('modal-payment-status').textContent = order.payment_status ? order.payment_status.charAt(0).toUpperCase() + order.payment_status.slice(1) : '-';

            modal.show();
          });
        });

        // Remember last active tab on page reload using URL hash
        const urlHash = window.location.hash;
        if (urlHash) {
          const triggerEl = document.querySelector(`button[data-bs-target="${urlHash}"]`);
          if (triggerEl) {
            const tab = new bootstrap.Tab(triggerEl);
            tab.show();
          }
        }

        // Update URL hash on tab change
        const tabButtons = document.querySelectorAll('#ordersTab button[data-bs-toggle="tab"]');
        tabButtons.forEach(btn => {
          btn.addEventListener('shown.bs.tab', function (event) {
            history.replaceState(null, null, event.target.getAttribute('data-bs-target'));
          });
        });
    });
</script>

@endsection
