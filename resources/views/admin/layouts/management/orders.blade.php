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
    box-shadow: 0 4px 6px rgba(0,0,0,0.1), 0 8px 20px rgba(0,0,0,0.07);
    transition: box-shadow 0.3s ease;
  }

  .admin-management-container:hover {
    box-shadow: 0 8px 12px rgba(0,0,0,0.15), 0 12px 30px rgba(0,0,0,0.1);
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


  .tab-filter-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    flex-wrap: wrap;
  }

  .date-filter {
    max-width: 280px;
    position: relative;
  }

  .date-filter input {
    cursor: pointer;
    background-color: #f0f4ff;
    border: 1px solid #2563eb;
    border-radius: 0.375rem;
    padding: 0.375rem 0.75rem;
    width: 100%;
    font-weight: 500;
    color: #1e40af;
  }

  .date-filter .clear-btn {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    color: #2563eb;
    font-size: 1.1rem;
    cursor: pointer;
    display: none;
  }

  .date-filter input:not(:placeholder-shown) + .clear-btn {
    display: block;
  }

  @media (max-width: 575.98px) {
    .tab-filter-container {
      flex-direction: column;
      gap: 0.5rem;
    }
    .date-filter {
      max-width: 100%;
    }
  }

.toast-notification {
  position: fixed;
  top: 20px;
  right: 20px;
  min-width: 320px;
  max-width: 400px;
  padding: 1rem 1.25rem;
  border-radius: 8px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.15);
  color: #fff;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  font-size: 1rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  opacity: 0;
  transform: translateX(120%);
  animation: slideIn 0.4s forwards;
  z-index: 1080;
  user-select: none;
}

.toast-success {
  background-color: #28a745;
}

.toast-error {
  background-color: #dc3545;
}

.toast-icon svg {
  stroke: #fff;
  flex-shrink: 0;
}

.toast-message {
  flex: 1;
  line-height: 1.3;
}

.toast-close {
  background: transparent;
  border: none;
  color: rgba(255,255,255,0.8);
  font-size: 1.25rem;
  font-weight: bold;
  cursor: pointer;
  padding: 0;
  line-height: 1;
  transition: color 0.2s ease;
  flex-shrink: 0;
}

.toast-close:hover {
  color: #fff;
}


@keyframes slideIn {
  to {
    opacity: 1;
    transform: translateX(0);
  }
}


@keyframes slideOut {
  to {
    opacity: 0;
    transform: translateX(120%);
  }
}



</style>

<div class="admin-management-container">
  <h2 class="mb-4 text-primary fw-bold text-center">Order Management</h2>

	{{-- Flash Messages --}}
@if (session('success'))
  <div class="toast-notification toast-success" role="alert" aria-live="polite" aria-atomic="true">
    <div class="toast-icon">
      <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M20 6L9 17l-5-5"/>
      </svg>
    </div>
    <div class="toast-message">
      <strong>Success:</strong> {{ session('success') }}
    </div>
    <button class="toast-close" aria-label="Close notification">&times;</button>
  </div>
@endif

@if (session('error'))
  <div class="toast-notification toast-error" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-icon">
      <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12" y2="16"/>
      </svg>
    </div>
    <div class="toast-message">
      <strong>Error:</strong> {{ session('error') }}
    </div>
    <button class="toast-close" aria-label="Close notification">&times;</button>
  </div>
@endif





  <div class="tab-filter-container">
    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-0" id="ordersTab" role="tablist" style="flex: 1;">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">
          All Orders ({{ $orders->count() }})
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="paid-tab" data-bs-toggle="tab" data-bs-target="#paid" type="button" role="tab" aria-controls="paid" aria-selected="false">
          Paid Orders ({{ $paidOrders->count() }})
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="false">
          Pending Orders ({{ $pendingOrders->count() }})
        </button>
      </li>
    </ul>

    {{-- Date Range Filter --}}
    <div class="date-filter">
      <input
        type="text"
        id="dateRangeInput"
        placeholder="Filter by date range"
        autocomplete="off"
        value="{{ request('date_range') ?? '' }}"
        readonly
        aria-label="Date Range Filter"
      />
      <button type="button" class="clear-btn" aria-label="Clear date filter">&times;</button>
    </div>
  </div>

  {{-- Tab contents --}}
  <div class="tab-content" id="ordersTabContent">
    {{-- All Orders --}}
    <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
      <div class="table-responsive">
        <table id="ordersTable" class="table table-bordered table-hover table-striped" style="width:100%">
          <thead class="bg-primary text-white text-center align-middle">
            <tr>
              <th>#</th>
              <th>Customer Name</th>
              <th>Mobile</th>
              <th>Domain</th>
              <th>Category</th>
              <th>Price (Rs.)</th>
              <th>Payment</th>
              <th>Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($orders as $order)
            <tr>
              <td class="text-center">{{ $order->id }}</td>
              <td>{{ $order->first_name }}</td>
              <td>{{ $order->mobile }}</td>
              <td>{{ $order->domain_name }}</td>
              <td>{{ $order->category ?? '-' }}</td>
              <td class="text-end">{{ number_format($order->price, 2) }}</td>
              <td class="text-center">
                <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">
                  {{ ucfirst($order->payment_status) }}
                </span>
              </td>
              <td class="text-center">{{ $order->created_at->format('Y-m-d') }}</td>
              <td class="text-center">
                <button class="btn btn-sm btn-info btn-view-order" data-order='@json($order)'>View</button>
                <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure to delete this order?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger ms-1">Delete</button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    {{-- Paid Orders --}}
    <div class="tab-pane fade" id="paid" role="tabpanel" aria-labelledby="paid-tab">
      <div class="table-responsive">
        <table id="paidOrdersTable" class="table table-bordered table-hover table-striped" style="width:100%">
          <thead class="bg-primary text-white text-center align-middle">
            <tr>
              <th>#</th>
              <th>Customer Name</th>
              <th>Mobile</th>
              <th>Domain</th>
              <th>Category</th>
              <th>Price (Rs.)</th>
              <th>Payment</th>
              <th>Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($paidOrders as $order)
            <tr>
              <td class="text-center">{{ $order->id }}</td>
              <td>{{ $order->first_name }}</td>
              <td>{{ $order->mobile }}</td>
              <td>{{ $order->domain_name }}</td>
              <td>{{ $order->category ?? '-' }}</td>
              <td class="text-end">{{ number_format($order->price, 2) }}</td>
              <td class="text-center">
                <span class="badge bg-success">Paid</span>
              </td>
              <td class="text-center">{{ $order->created_at->format('Y-m-d') }}</td>
              <td class="text-center">
                <button class="btn btn-sm btn-info btn-view-order" data-order='@json($order)'>View</button>
                <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure to delete this order?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger ms-1">Delete</button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    {{-- Pending Orders --}}
    <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
      <div class="table-responsive">
        <table id="pendingOrdersTable" class="table table-bordered table-hover table-striped" style="width:100%">
          <thead class="bg-primary text-white text-center align-middle">
            <tr>
              <th>#</th>
              <th>Customer Name</th>
              <th>Mobile</th>
              <th>Domain</th>
              <th>Category</th>
              <th>Price (Rs.)</th>
              <th>Payment</th>
              <th>Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($pendingOrders as $order)
            <tr>
              <td class="text-center">{{ $order->id }}</td>
              <td>{{ $order->first_name }}</td>
              <td>{{ $order->mobile }}</td>
              <td>{{ $order->domain_name }}</td>
              <td>{{ $order->category ?? '-' }}</td>
              <td class="text-end">{{ number_format($order->price, 2) }}</td>
              <td class="text-center">
                <span class="badge bg-warning">Pending</span>
              </td>
              <td class="text-center">{{ $order->created_at->format('Y-m-d') }}</td>
              <td class="text-center">
                <button class="btn btn-sm btn-info btn-view-order" data-order='@json($order)'>View</button>
                <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure to delete this order?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger ms-1">Delete</button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- Order Details Modal --}}
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><strong>Order ID:</strong> <span id="modal-order-id"></span></p>
        <p><strong>Customer Name:</strong> <span id="modal-customer-name"></span></p>
        <p><strong>Email:</strong> <span id="modal-email"></span></p>
        <p><strong>Phone:</strong> <span id="modal-phone"></span></p>
        <p><strong>Domain:</strong> <span id="modal-domain"></span></p>
        <p><strong>Category:</strong> <span id="modal-category"></span></p>
        <p><strong>Price:</strong> <span id="modal-price"></span></p>
        <p><strong>Order Date:</strong> <span id="modal-order-date"></span></p>
        <p><strong>Payment Status:</strong> <span id="modal-payment-status"></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@endsection




@section('scripts')
<script>
  $(function () {
    // Initialize DataTables
    ['#ordersTable', '#paidOrdersTable', '#pendingOrdersTable'].forEach(tableId => {
	  $(tableId).DataTable({
		responsive: true,
		paging: true,
		lengthChange: true,
		searching: true,
		ordering: true,
		info: true,
		autoWidth: false,
	  });
	});


    const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));

    // Delegated click handler to support dynamic/responsive elements
    $(document).on('click', '.btn-view-order', function () {
      const order = $(this).data('order');

      $('#modal-order-id').text(order.id ?? '-');
      $('#modal-customer-name').text(`${order.first_name ?? ''} ${order.last_name ?? ''}`.trim() || '-');
      $('#modal-email').text(order.email ?? '-');
      $('#modal-phone').text(order.mobile ?? '-');
      $('#modal-domain').text(order.domain_name ?? '-');
      $('#modal-category').text(order.category ?? '-');
      $('#modal-price').text(order.price !== undefined ? 'Rs.' + parseFloat(order.price).toFixed(2) : '-');
      $('#modal-order-date').text(order.created_at ? new Date(order.created_at).toLocaleDateString() : '-');
      $('#modal-payment-status').text(order.payment_status ? order.payment_status.charAt(0).toUpperCase() + order.payment_status.slice(1) : '-');

      modal.show();
    });

    // Date range filter
    $('#dateRangeInput').daterangepicker({
      autoUpdateInput: false,
      opens: 'left',
      locale: {
        format: 'YYYY-MM-DD',
        cancelLabel: 'Clear'
      },
    });

    $('#dateRangeInput').on('apply.daterangepicker', function(ev, picker) {
      const start = picker.startDate.format('YYYY-MM-DD');
      const end = picker.endDate.format('YYYY-MM-DD');
      const dateRange = `${start} - ${end}`;
      $(this).val(dateRange);

      const url = new URL(window.location.href);
      url.searchParams.set('date_range', dateRange);
      window.location.href = url.toString();
    });

    $('#dateRangeInput').on('cancel.daterangepicker', function(ev, picker) {
      $(this).val('');
      const url = new URL(window.location.href);
      url.searchParams.delete('date_range');
      window.location.href = url.toString();
    });

    $('.clear-btn').on('click', function() {
      $('#dateRangeInput').val('').trigger('cancel.daterangepicker');
    });

    @if(request('date_range'))
      $('#dateRangeInput').val("{{ request('date_range') }}");
    @endif
  });

  // Toast auto-hide and close
  document.addEventListener('DOMContentLoaded', () => {
    const toasts = document.querySelectorAll('.toast-notification');

    toasts.forEach(toast => {
      const timeoutId = setTimeout(() => hideToast(toast), 4000);

      const closeBtn = toast.querySelector('.toast-close');
      closeBtn.addEventListener('click', () => {
        clearTimeout(timeoutId);
        hideToast(toast);
      });
    });

    function hideToast(toast) {
      toast.style.animation = 'slideOut 0.4s forwards';
      toast.addEventListener('animationend', () => toast.remove());
    }
  });
</script>
@endsection
