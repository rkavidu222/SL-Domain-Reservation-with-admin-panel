@extends('admin.layouts.app')

@section('title', 'Invoices')

@push('styles')
  <link rel="stylesheet" href="{{ asset('admin/css/order.css') }}">
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

    <div class="table-responsive">
        <table id="invoicesTable" class="table table-bordered table-hover table-striped align-middle text-center" style="width:100%">
            <thead class="bg-primary text-white text-center align-middle">
                <tr>
                    <th>#ID</th>
                    <th>Domain</th>
                    <th class="text-start">Customer</th>
                    <th>Payment Status</th>
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
                    @endphp
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->domain_name }}</td>
                        <td class="text-start">{{ $order->first_name }} {{ $order->last_name }}</td>
                        <td><span class="badge bg-{{ $class }}">{{ $label }}</span></td>
                        <td class="text-end">{{ number_format($order->price, 2) }}</td>
                        <td>{{ $order->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('admin.invoices.show', $order->id) }}" class="btn btn-sm btn-primary mb-1">
                                <i class="bi bi-eye"></i> View
                            </a>

                            <!-- Send SMS Button -->
                            <button class="btn btn-sm btn-success mb-1" onclick="sendSms({{ $order->id }})">
                                <i class="bi bi-send"></i> Send SMS
                            </button>

                            <!-- Hidden form for POST -->
                            <form id="smsForm-{{ $order->id }}" action="{{ route('admin.invoices.sendSms', $order->id) }}"
                                method="POST" style="display: none;">
                                @csrf
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const toasts = document.querySelectorAll('.toast-notification');

    toasts.forEach(toast => {
      const timeoutId = setTimeout(() => {
        hideToast(toast);
      }, 4000);

      const closeBtn = toast.querySelector('.toast-close');
      closeBtn.addEventListener('click', () => {
        clearTimeout(timeoutId);
        hideToast(toast);
      });
    });

    function hideToast(toast) {
      toast.style.animation = 'slideOut 0.4s forwards';
      toast.addEventListener('animationend', () => {
        toast.remove();
      });
    }
  });

  $(document).ready(function() {
    $('#invoicesTable').DataTable({
      paging: true,
      lengthChange: true,
      searching: true,
      ordering: true,
      info: true,
      autoWidth: false,
      responsive: true,
    });
  });

  function sendSms(orderId) {
    if (confirm('Are you sure you want to send this invoice via SMS?')) {
      document.getElementById(`smsForm-${orderId}`).submit();
    }
  }
</script>
@endsection
