@extends('admin.layouts.app')

@section('title', 'Trashed Orders')

@push('styles')
  <link rel="stylesheet" href="{{ asset('admin/css/trashed-orders.css') }}">
@endpush

@section('content')
<div class="admin-management-container">
  <h2 class="mb-4 text-primary fw-bold text-center">
    <i class="bi bi-trash3-fill"></i> Trashed Orders
  </h2>

  {{-- Flash Messages as Toast Notifications --}}
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

  <div class="tab-filter-container d-flex justify-content-between align-items-center">
    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-0" id="trashedTabs" role="tablist" style="flex: 1;">
      @foreach ([
        ['id' => 'all', 'label' => 'All Orders', 'count' => isset($all) ? $all->count() : 0, 'active' => true],
        ['id' => 'paid', 'label' => 'Paid Orders', 'count' => isset($paid) ? $paid->count() : 0],
        ['id' => 'pending', 'label' => 'Pending Orders', 'count' => isset($pending) ? $pending->count() : 0],
        ['id' => 'await', 'label' => 'Awaiting Proof', 'count' => isset($awaitingProof) ? $awaitingProof->count() : 0],
        ['id' => 'client-acc-created', 'label' => 'Client Acc Created', 'count' => isset($clientAccCreated) ? $clientAccCreated->count() : 0],
        ['id' => 'actived', 'label' => 'Actived', 'count' => isset($actived) ? $actived->count() : 0]
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

  <div class="tab-content" id="trashedTabContent">
    @foreach ([
      'all' => ['data' => isset($all) ? $all : collect([]), 'tableId' => 'allTable', 'emptyMessage' => 'No trashed orders found.'],
      'paid' => ['data' => isset($paid) ? $paid : collect([]), 'tableId' => 'paidTable', 'emptyMessage' => 'No paid trashed orders found.'],
      'pending' => ['data' => isset($pending) ? $pending : collect([]), 'tableId' => 'pendingTable', 'emptyMessage' => 'No pending trashed orders found.'],
      'await' => ['data' => isset($awaitingProof) ? $awaitingProof : collect([]), 'tableId' => 'awaitOrdersTable', 'emptyMessage' => 'No awaiting proof trashed orders found.'],
      'client-acc-created' => ['data' => isset($clientAccCreated) ? $clientAccCreated : collect([]), 'tableId' => 'clientAccCreatedOrdersTable', 'emptyMessage' => 'No client account created trashed orders found.'],
      'actived' => ['data' => isset($actived) ? $actived : collect([]), 'tableId' => 'activedOrdersTable', 'emptyMessage' => 'No actived trashed orders found.']
    ] as $tabId => $tab)
      <div class="tab-pane fade {{ $tabId === 'all' ? 'show active' : '' }}" id="{{ $tabId }}" role="tabpanel" aria-labelledby="{{ $tabId }}-tab">
        @if($tab['data']->count())
          <div class="table-responsive">
            <table id="{{ $tab['tableId'] }}" class="table table-bordered table-hover table-striped" style="width:100%">
              <thead class="bg-primary text-white text-center align-middle">
                <tr>
                  <th>#</th>
                  <th class="text-start">Customer Name</th>
                  <th>Mobile</th>
                  <th class="text-start">Domain</th>
                  <th>Category</th>
                  <th>Price (Rs.)</th>
                  <th>Payment</th>
                  <th>Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($tab['data'] as $order)
                  <tr>
                    <td class="text-center">{{ $order->id }}</td>
                    <td class="text-start">{{ $order->first_name }} {{ $order->last_name }}</td>
                    <td>
                      <a href="https://wa.me/{{ ltrim($order->mobile, '0') }}" target="_blank">
                        {{ $order->mobile }}
                      </a>
                    </td>
                    <td class="text-start">{{ $order->domain_name }}</td>
                    <td>{{ $order->category ?? '-' }}</td>
                    <td class="text-end">{{ number_format($order->price, 2) }}</td>
                    <td class="text-center">
                      @php
                        $status = $order->payment_status;
                        $statusClass = match ($status) {
                          'paid' => 'success',
                          'pending' => 'warning text-dark',
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
                      <button class="btn btn-sm btn-info btn-view-order" data-order='@json($order)'>View</button>

                      {{-- Restore form/button --}}
                      <form method="POST" action="" class="d-inline restore-form">
                        @csrf
                        <button type="button"
                          class="btn btn-sm btn-success ms-1 action-btn"
                          data-action="restore"
                          data-order-id="{{ $order->id }}"
                          data-message="Are you sure you want to restore order #{{ $order->id }} for {{ $order->first_name }} {{ $order->last_name }}?"
                          title="Restore"
                          aria-label="Restore Order {{ $order->id }}"
                        >
                          <i class="bi bi-arrow-counterclockwise"></i> Restore
                        </button>
                      </form>

                      {{-- Delete form/button --}}
                      <form method="POST" action="" class="d-inline delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button"
                          class="btn btn-sm btn-danger ms-1 action-btn"
                          data-action="delete"
                          data-order-id="{{ $order->id }}"
                          data-message="Are you sure you want to permanently delete order #{{ $order->id }}? This action cannot be undone."
                          title="Delete Permanently"
                          aria-label="Delete Order {{ $order->id }} Permanently"
                        >
                          <i class="bi bi-x-circle"></i> Delete
                        </button>
                      </form>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="alert alert-info">{{ $tab['emptyMessage'] }}</div>
        @endif
      </div>
    @endforeach
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

  {{-- Shared Confirmation Modal --}}
  <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-center p-4">
        <div class="modal-body">
          <div class="text-warning mb-3" style="font-size: 48px;">
            <i class="bi bi-exclamation-circle-fill"></i>
          </div>
          <h2 class="mb-3" id="confirmationModalTitle">Confirm Action</h2>
          <p id="confirmationModalMessage">Are you sure you want to proceed?</p>
          <form id="confirmationForm" method="POST" action="">
            @csrf
            <div id="methodField"></div>
            <div class="d-flex justify-content-center gap-3 mt-4">
              <button type="submit" class="btn btn-danger" id="confirmationConfirmBtn">Yes</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@section('scripts')
<script>
  $(function () {
    // Initialize DataTables on each table
    ['#allTable', '#paidTable', '#pendingTable', '#awaitOrdersTable', '#clientAccCreatedOrdersTable', '#activedOrdersTable'].forEach(id => {
      $(id).DataTable({
        responsive: true,
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: false,
      });
    });

    // Bootstrap modal instances
    const orderDetailsModal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
    const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));

    // Show Order Details Modal
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
      $('#modal-payment-status').text(order.payment_status ? order.payment_status.charAt(0).toUpperCase() + order.payment_status.slice(1).replace(/_/g, ' ') : '-');
      orderDetailsModal.show();
    });

    // Handle Restore and Delete buttons with single confirmation modal
    $(document).on('click', '.action-btn', function () {
      const btn = $(this);
      const action = btn.data('action'); // "restore" or "delete"
      const orderId = btn.data('order-id');
      const message = btn.data('message');
      let formAction = '';
      let methodField = '';

      if (action === 'restore') {
        formAction = "{{ route('admin.orders.restore', ':id') }}".replace(':id', orderId);
        methodField = ''; // POST default
      } else if (action === 'delete') {
        formAction = "{{ route('admin.orders.forceDelete', ':id') }}".replace(':id', orderId);
        methodField = '<input type="hidden" name="_method" value="DELETE">';
      }

      $('#confirmationModalTitle').text(action === 'restore' ? 'Confirm Restore' : 'Confirm Permanent Deletion');
      $('#confirmationModalMessage').text(message);
      $('#confirmationForm').attr('action', formAction);
      $('#methodField').html(methodField);

      // Set button color
      const confirmBtn = $('#confirmationConfirmBtn');
      if (action === 'restore') {
        confirmBtn.removeClass('btn-danger').addClass('btn-success').text('Restore');
      } else {
        confirmBtn.removeClass('btn-success').addClass('btn-danger').text('Delete Permanently');
      }

      confirmationModal.show();
    });

    // Date Range Picker setup
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
  });

  // Toast notification auto-hide & close
  document.addEventListener('DOMContentLoaded', () => {
    const toasts = document.querySelectorAll('.toast-notification');
    toasts.forEach(toast => {
      const timeoutId = setTimeout(() => hideToast(toast), 4000);
      toast.querySelector('.toast-close').addEventListener('click', () => {
        clearTimeout(timeoutId);
        hideToast(toast);
      });
    });

    function hideToast(toast) {
      toast.style.animation = 'fadeOutSlide 0.5s ease forwards';
      toast.addEventListener('animationend', () => toast.remove());
    }
  });
</script>
@endsection
