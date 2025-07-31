@extends('admin.layouts.app')

@section('title', 'Invoices')

@push('styles')
  <link rel="stylesheet" href="{{ asset('admin/css/order.css') }}">
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@section('content')
<div class="admin-management-container">
    <h2 class="mb-4 d-flex justify-content-center align-items-center gap-2 text-primary fw-bold">
        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-receipt" viewBox="0 0 16 16" width="24" height="24">
          <path d="M1 2a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v13l-2-1-2 1-2-1-2 1-2-1-2 1V2z"/>
          <path d="M3 4h10v2H3V4zm0 3h10v2H3V7zm0 3h10v2H3v-2z"/>
        </svg>
        All Invoices
    </h2>

    {{-- Flash messages --}}
    @if (session('success'))
    <div class="toast-notification toast-success" role="alert">
        <div class="toast-icon"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg></div>
        <div class="toast-message"><strong>Success:</strong> {{ session('success') }}</div>
        <button class="toast-close" aria-label="Close notification">&times;</button>
    </div>
    @endif

    @if (session('error'))
    <div class="toast-notification toast-error" role="alert">
        <div class="toast-icon"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12" y2="16"/></svg></div>
        <div class="toast-message"><strong>Error:</strong> {{ session('error') }}</div>
        <button class="toast-close" aria-label="Close notification">&times;</button>
    </div>
    @endif

    {{-- Tabs + Date Filter --}}
    <div class="tab-filter-container d-flex justify-content-between align-items-center mb-3 flex-wrap">
      @php
        $paymentStatus = request('payment_status', 'all');
        $dateRange = request('date_range', '');

        $countAll = $allOrders->count();
        $countPaid = $allOrders->where('payment_status', 'paid')->count();
        $countPending = $allOrders->where('payment_status', 'pending')->count();
        $countAwait = $allOrders->where('payment_status', 'awaiting_proof')->count();
        $countClientAccCreated = $allOrders->where('payment_status', 'client_acc_created')->count();
        $countActived = $allOrders->where('payment_status', 'actived')->count();

        $tabs = [
          ['id' => 'all', 'label' => 'All Invoices', 'count' => $countAll],
          ['id' => 'paid', 'label' => 'Paid Invoices', 'count' => $countPaid],
          ['id' => 'pending', 'label' => 'Pending Invoices', 'count' => $countPending],
          ['id' => 'awaiting_proof', 'label' => 'Awaiting Proof', 'count' => $countAwait],
          ['id' => 'client_acc_created', 'label' => 'Client Acc Created', 'count' => $countClientAccCreated],
          ['id' => 'actived', 'label' => 'Actived', 'count' => $countActived],
        ];
      @endphp

      <ul class="nav nav-tabs mb-0" id="ordersTab" style="flex: 1;">

        @foreach ($tabs as $tab)
          <li class="nav-item">
            <a href="{{ url()->current() . '?payment_status=' . $tab['id'] . ($dateRange ? '&date_range=' . urlencode($dateRange) : '') }}"
               class="nav-link {{ $paymentStatus === $tab['id'] ? 'active' : '' }}">
               {{ $tab['label'] }} ({{ $tab['count'] }})
            </a>
          </li>
        @endforeach
      </ul>

      <div class="date-filter" style="max-width: 280px; position: relative; margin-left: 1rem;">
        <input type="text" id="dateRangeInput" placeholder="Filter by date range" autocomplete="off" value="{{ $dateRange }}"
          readonly class="form-control" style="cursor: pointer; background-color: #f0f4ff; border: 1px solid #2563eb;
          border-radius: 0.375rem; padding: 0.375rem 0.75rem; font-weight: 500; color: #1e40af;" />
        <button type="button" class="clear-btn" style="position: absolute; right: 8px; top: 50%;
          transform: translateY(-50%); background: transparent; border: none; color: #2563eb; font-size: 1.1rem;
          cursor: pointer; {{ $dateRange ? '' : 'display: none;' }}">&times;</button>
      </div>
    </div>

    <div class="table-responsive">
        <table id="invoicesTable" class="table table-bordered table-hover table-striped align-middle text-center">
            <thead class="bg-primary text-white">
                <tr>
                    <th>#ID</th>
                    <th>Domain</th>
                    <th class="text-start">Customer</th>
                    <th>Payment Status</th>
                    <th>SMS Status</th>
                    <th class="text-end">Price (Rs.)</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    @php
                        $statusLabels = [
                            'paid' => ['label' => 'Paid', 'class' => 'success'],
                            'pending' => ['label' => 'Pending', 'class' => 'warning text-dark'],
                            'awaiting_proof' => ['label' => 'Awaiting Proof', 'class' => 'info text-dark'],
                            'client_acc_created' => ['label' => 'Client Acc Created', 'class' => 'secondary'],
                            'actived' => ['label' => 'Actived', 'class' => 'primary'],
                        ];
                        $status = $order->payment_status;
                        $label = $statusLabels[$status]['label'] ?? 'Unknown';
                        $class = $statusLabels[$status]['class'] ?? 'dark';

                        $latestSms = $order->smsLogs->first();
                        $smsStatus = $latestSms->status ?? 'No SMS Sent';
                        $smsStatusClass = match(strtolower($smsStatus)) {
                            'success' => 'success',
                            'pending' => 'warning text-dark',
                            'failed' => 'danger',
                            default => 'secondary',
                        };
                    @endphp
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->domain_name }}</td>
                        <td class="text-start">{{ $order->first_name }} {{ $order->last_name }}</td>
                        <td><span class="badge bg-{{ $class }}">{{ $label }}</span></td>
                        <td><span class="badge bg-{{ $smsStatusClass }}">{{ ucfirst($smsStatus) }}</span></td>
                        <td class="text-end">{{ number_format($order->price, 2) }}</td>
                        <td>{{ $order->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('admin.invoices.show', $order->id) }}" class="btn btn-sm btn-primary mb-1">
                                <i class="bi bi-eye"></i> View
                            </a>
                            <button class="btn btn-sm btn-success mb-1" onclick="sendSms({{ $order->id }})">
                                <i class="bi bi-send"></i> Send SMS
                            </button>
                            <form id="smsForm-{{ $order->id }}" action="{{ route('admin.invoices.sendSms', $order->id) }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Send SMS Confirmation Modal -->
<div class="modal fade" id="sendSmsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4">
      <div class="modal-body">
        <div class="text-warning mb-3" style="font-size: 48px;">
          <i class="bi bi-exclamation-circle-fill"></i>
        </div>
        <h2 class="mb-3">Send SMS Confirmation</h2>
        <p>Are you sure you want to send this invoice via SMS?</p>
        <div class="d-flex justify-content-center gap-3 mt-4">
          <button type="button" class="btn btn-success" id="confirmSendSmsBtn">Yes, Send</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
  let currentOrderId = null;

  function sendSms(orderId) {
    currentOrderId = orderId;
    const modal = new bootstrap.Modal(document.getElementById('sendSmsModal'));
    modal.show();
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('confirmSendSmsBtn').addEventListener('click', () => {
      if (currentOrderId) {
        document.getElementById(`smsForm-${currentOrderId}`).submit();
      }
    });

    $('#invoicesTable').DataTable();

    $('#dateRangeInput').daterangepicker({
      autoUpdateInput: false,
      opens: 'left',
      locale: { format: 'YYYY-MM-DD', cancelLabel: 'Clear' }
    });

    @if($dateRange)
      $('#dateRangeInput').val("{{ $dateRange }}");
      $('.clear-btn').show();
    @endif

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
      $(this).hide();
    });

    const toasts = document.querySelectorAll('.toast-notification');
    toasts.forEach(toast => {
      const timeoutId = setTimeout(() => toast.remove(), 4000);
      toast.querySelector('.toast-close').addEventListener('click', () => {
        clearTimeout(timeoutId);
        toast.remove();
      });
    });
  });
</script>
@endsection
