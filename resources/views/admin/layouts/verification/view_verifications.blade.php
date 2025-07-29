@extends('admin.layouts.app')

@section('title', 'View Verifications')

@push('styles')
  <link rel="stylesheet" href="{{ asset('admin/css/order.css') }}">
@endpush

@section('content')
<div class="admin-management-container">
  <h2 class="mb-4 text-primary fw-bold text-center">
    <i class="bi bi-file-earmark-check-fill"></i> View Verifications
  </h2>

  {{-- Flash Messages --}}
  @if (session('success'))
    <div class="toast-notification toast-success" role="alert">
      <div class="toast-icon">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
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
    <div class="toast-notification toast-error" role="alert">
      <div class="toast-icon">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
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
    <table id="verificationsTable" class="table table-bordered table-hover table-striped w-100">
      <thead class="bg-primary text-white text-center">
        <tr>
          <th>#</th>
          <th>Order ID</th>
          <th>Customer Name</th>
          <th>Domain</th>
          <th>Price (Rs.)</th>
          <th>Payment Slip</th>
          <th>Payment Status</th>
          <th>Uploaded At</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($verifications as $verification)
          <tr>
            <td class="text-center">{{ $verification->id }}</td>
            <td class="text-center">{{ $verification->domain_order_id }}</td>
            <td>
              {{ $verification->domainOrder->first_name ?? '-' }}
              {{ $verification->domainOrder->last_name ?? '' }}
            </td>
            <td>{{ $verification->domainOrder->domain_name ?? '-' }}</td>
            <td class="text-end">{{ number_format($verification->domainOrder->price ?? 0, 2) }}</td>
            <td class="text-center">
              @if ($verification->receipt_path)
                <a href="{{ url('public/' . $verification->receipt_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
  View Slip
</a>


              @else
                <span class="text-muted">No Slip</span>
              @endif
            </td>
            <td class="text-center">
              <form action="{{ route('admin.verification.updateStatus', $verification->id) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                  @foreach(['pending', 'verified', 'rejected'] as $status)
                    <option value="{{ $status }}" {{ $verification->status === $status ? 'selected' : '' }}>
                      {{ ucfirst($status) }}
                    </option>
                  @endforeach
                </select>
              </form>
            </td>
            <td class="text-center">{{ $verification->created_at->format('Y-m-d H:i') }}</td>
            <td class="text-center">
              <a href="{{ route('admin.verification.show', $verification->id) }}" class="btn btn-sm btn-info">Details</a>
              <form action="{{ route('admin.verification.destroy', $verification->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure to delete this verification?');">
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
@endsection

@section('scripts')
<script>
  $(function () {
    $('#verificationsTable').DataTable({
      responsive: true,
      paging: true,
      lengthChange: true,
      searching: true,
      ordering: true,
      info: true,
      autoWidth: false,
    });

    // Toast auto-hide and close
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
