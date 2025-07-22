@extends('admin.layouts.app')

@section('title', 'SMS Report')

@section('content')
<div class="admin-management-container">
    <h2 class="mb-4 text-primary fw-bold">
        <i class="bi bi-card-text"></i> SMS Report
    </h2>

    @if(session('success'))
        <div class="toast-notification toast-success" role="alert" aria-live="polite" aria-atomic="true">
            <div class="toast-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 6L9 17l-5-5"/>
                </svg>
            </div>
            <div class="toast-message">{!! session('success') !!}</div>
            <button class="toast-close" aria-label="Close notification">&times;</button>
        </div>
    @endif

    <div class="table-responsive">
        <table id="smsTemplateTable" class="table table-bordered table-hover align-middle table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th class="text-start">Phone</th>
                    <th class="text-start">Message</th>
                    <th>Status</th>
                    <th>Sent At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-start">{{ $log->recipient }}</td>
                    <td class="text-start" style="max-width: 350px; white-space: normal;">{{ $log->message }}</td>
                    <td>
                        @if($log->status === 'success')
                            <span class="badge bg-success">Delivered</span>
                        @elseif($log->status === 'pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @else
                            <span class="badge bg-danger">Failed</span>
                        @endif
                    </td>
                    <td>{{ $log->created_at->format('Y-m-d h:i A') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">No SMS logs found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
  $(document).ready(function () {
    $('#smsTemplateTable').DataTable({
      paging: true,
      lengthChange: true,
      searching: true,
      ordering: true,
      info: true,
      autoWidth: false,
      responsive: true,
      order: [[0, 'desc']],
      language: {
        search: "_INPUT_",
        searchPlaceholder: "Search templates..."
      }
    });
  });

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
</script>
@endsection
