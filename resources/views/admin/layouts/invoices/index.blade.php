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

    {{-- Flash messages (same) --}}

    <div class="table-responsive">
        <table id="invoicesTable" class="table table-bordered table-hover table-striped align-middle text-center" style="width:100%">
            <thead class="bg-primary text-white text-center align-middle">
                <tr>
                    <th>#ID</th>
                    <th>Domain</th>
                    <th class="text-start">Customer</th>
                    <th>Status</th>
                    <th class="text-end">Price (Rs.)</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->domain_name }}</td>
                        <td class="text-start">{{ $order->first_name }} {{ $order->last_name }}</td>
                        <td>
                            @if($order->payment_status === 'paid')
                                <span class="badge bg-success">{{ ucfirst($order->payment_status) }}</span>
                            @elseif($order->payment_status === 'pending')
                                <span class="badge bg-warning text-dark">{{ ucfirst($order->payment_status) }}</span>
                            @else
                                <span class="badge bg-secondary">Unknown</span>
                            @endif
                        </td>
                        <td class="text-end">{{ number_format($order->price, 2) }}</td>
                        <td>{{ $order->created_at->format('d M Y') }}</td>
                        <td>
							<a href="{{ route('admin.invoices.show', $order->id) }}" class="btn btn-sm btn-primary mb-1">
								<i class="bi bi-eye"></i> View
							</a>

							<a href="{{ route('admin.invoices.sendSms', $order->id) }}"
							   class="btn btn-sm btn-success mb-1"
							   onclick="return confirm('Are you sure you want to send this invoice via SMS?')">
								<i class="bi bi-send"></i> Send SMS
							</a>
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
</script>
@endsection
