@extends('admin.layouts.app')

@section('title', 'Order Management')

@push('styles')
  <link rel="stylesheet" href="{{ asset('admin/css/order.css') }}">
  <style>
    @keyframes slideOut {
      0% {opacity: 1; transform: translateX(0);}
      100% {opacity: 0; transform: translateX(100%);}
    }
  </style>
@endpush

@section('content')
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

  {{-- Tabs & Date Filter --}}
  <div class="tab-filter-container d-flex align-items-center justify-content-between mb-3">
    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-0 flex-grow-1" id="ordersTab" role="tablist">
      @foreach ([
        ['id' => 'all', 'label' => 'All Orders', 'count' => $orders->count(), 'active' => true],
        ['id' => 'paid', 'label' => 'Paid Orders', 'count' => $paidOrders->count()],
        ['id' => 'pending', 'label' => 'Pending Orders', 'count' => $pendingOrders->count()],
        ['id' => 'await', 'label' => 'Awaiting Proof', 'count' => $awaitingProofOrders->count()],
        ['id' => 'client-acc-created', 'label' => 'Client Acc Created', 'count' => $clientAccCreatedOrders->count()],
        ['id' => 'actived', 'label' => 'Actived', 'count' => $activedOrders->count()]
      ] as $tab)
        <li class="nav-item" role="presentation">
          <button
            class="nav-link {{ $tab['active'] ?? false ? 'active' : '' }}"
            id="{{ $tab['id'] }}-tab"
            data-bs-toggle="tab"
            data-bs-target="#{{ $tab['id'] }}"
            type="button"
            role="tab"
            aria-controls="{{ $tab['id'] }}"
            aria-selected="{{ $tab['active'] ?? false ? 'true' : 'false' }}"
          >
            {{ $tab['label'] }} ({{ $tab['count'] }})
          </button>
        </li>
      @endforeach
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


  {{-- Tab Contents --}}
  <div class="tab-content" id="ordersTabContent">
    @foreach ([
      'all' => ['data' => $orders, 'tableId' => 'ordersTable'],
      'paid' => ['data' => $paidOrders, 'tableId' => 'paidOrdersTable'],
      'pending' => ['data' => $pendingOrders, 'tableId' => 'pendingOrdersTable'],
      'await' => ['data' => $awaitingProofOrders, 'tableId' => 'awaitOrdersTable'],
      'client-acc-created' => ['data' => $clientAccCreatedOrders, 'tableId' => 'clientAccCreatedOrdersTable'],
      'actived' => ['data' => $activedOrders, 'tableId' => 'activedOrdersTable']
    ] as $tabId => $tab)
      <div class="tab-pane fade {{ $tabId === 'all' ? 'show active' : '' }}" id="{{ $tabId }}" role="tabpanel" aria-labelledby="{{ $tabId }}-tab">
        <div class="table-responsive">
          <table id="{{ $tab['tableId'] }}" class="table table-bordered table-hover table-striped" style="width:100%">
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
              @foreach ($tab['data'] as $order)
                <tr>
                  <td class="text-center">{{ $order->id }}</td>
                  <td>{{ $order->first_name }} {{ $order->last_name }}</td>
                  <td>
                    <a href="https://wa.me/{{ ltrim($order->mobile, '0') }}" target="_blank" rel="noopener">
                      {{ $order->mobile }}
                    </a>
                  </td>
                  <td>{{ $order->domain_name }}</td>
                  <td>{{ $order->category ?? '-' }}</td>
                  <td class="text-end">{{ number_format($order->price, 2) }}</td>
                  <td class="text-center">
                    @php
                      $status = $order->payment_status;
                      $statusClass = match ($status) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'awaiting_proof' => 'info',
                        'client_acc_created' => 'primary',
                        'actived' => 'success',
                        default => 'secondary',
                      };
                    @endphp
                    <span class="badge bg-{{ $statusClass }}">
                      {{ ucwords(str_replace('_', ' ', $status)) }}
                    </span>
                  </td>
                  <td class="text-center">{{ $order->created_at->format('Y-m-d') }}</td>
                  <td class="text-center">
                    {{-- Submit Verification Button --}}
                    <button
                      type="button"
                      class="btn btn-sm btn-success btn-submit-verification"
                      data-order='@json($order)'
                      data-bs-toggle="modal"
                      data-bs-target="#verificationModal"
                    >
                      Submit Verification
                    </button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    @endforeach
  </div>

  {{-- Submit Verification Modal --}}
  <div class="modal fade" id="verificationModal" tabindex="-1" aria-labelledby="verificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form id="verificationForm" method="POST" enctype="multipart/form-data" action="{{ route('admin.verification.store') }}">
        @csrf
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title" id="verificationModalLabel">Submit Payment Verification</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="domain_order_id" id="verification-order-id" required>

            <div class="mb-3 row">
              <label for="reference_number" class="col-sm-4 col-form-label">Reference Number <span class="text-danger">*</span></label>
              <div class="col-sm-8">
                <input type="text" class="form-control" name="reference_number" id="reference_number" required maxlength="255" value="{{ old('reference_number') }}">
              </div>
            </div>

            <div class="mb-3 row">
              <label for="receipt" class="col-sm-4 col-form-label">Upload Receipt (jpeg, png, jpg, pdf) <span class="text-danger">*</span></label>
              <div class="col-sm-8">
                <input type="file" class="form-control" name="receipt" id="receipt" accept=".jpeg,.png,.jpg,.pdf" required>
              </div>
            </div>

            <div class="mb-3 row">
              <label for="description" class="col-sm-4 col-form-label">Description (optional)</label>
              <div class="col-sm-8">
                <textarea class="form-control" name="description" id="description" rows="3">{{ old('description') }}</textarea>
              </div>
            </div>

            <div class="border p-3 mb-3 bg-light rounded">
              <strong>Order Details:</strong>
              <p id="verification-customer-name" class="mb-1"></p>
              <p id="verification-domain-name" class="mb-1"></p>
              <p id="verification-price" class="mb-0"></p>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success">Submit Verification</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </div>
      </form>
    </div>
  </div>

</div>
@endsection

@section('scripts')
<script>
  $(function () {
    // Initialize DataTables
    ['#ordersTable', '#paidOrdersTable', '#pendingOrdersTable', '#awaitOrdersTable', '#clientAccCreatedOrdersTable', '#activedOrdersTable'].forEach(tableId => {
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

    // Fill verification modal on Submit Verification button click
    $(document).on('click', '.btn-submit-verification', function () {
      const order = $(this).data('order');

      $('#verification-order-id').val(order.id ?? '');
      $('#verification-customer-name').text(`Customer: ${order.first_name ?? ''} ${order.last_name ?? ''}`);
      $('#verification-domain-name').text(`Domain: ${order.domain_name ?? '-'}`);
      $('#verification-price').text(`Price: Rs. ${order.price !== undefined ? parseFloat(order.price).toFixed(2) : '-'}`);

      // Reset the form fields except hidden order id
      $('#verificationForm')[0].reset();
    });

    // Date range filter
    if ($('#dateRangeInput').length) {
      $('#dateRangeInput').daterangepicker({
        autoUpdateInput: false,
        opens: 'left',
        locale: { format: 'YYYY-MM-DD', cancelLabel: 'Clear' }
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

      $('#dateRangeInput').on('cancel.daterangepicker', function() {
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
    }

    // Toast auto-hide and close
    document.querySelectorAll('.toast-notification').forEach(toast => {
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
