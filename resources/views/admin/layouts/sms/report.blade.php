@extends('admin.layouts.app')

@section('title', 'SMS Report')

@section('content')
<style>
    .toast-notification {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem 1rem;
        margin-bottom: 1rem;
        border-radius: 0.5rem;
        font-size: 0.95rem;
        font-weight: 500;
        color: #fff;
        background-color: #28a745;
        animation: slideIn 0.4s ease-out;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        position: relative;
    }

    .toast-notification.toast-success {
        background-color: #198754;
    }

    .toast-icon svg {
        stroke: #fff;
    }

    .toast-message {
        flex: 1;
    }

    .toast-close {
        background: none;
        border: none;
        color: #fff;
        font-size: 1.25rem;
        cursor: pointer;
        line-height: 1;
    }

    @keyframes slideIn {
        from {
            transform: translateY(-10px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        to {
            transform: translateY(-10px);
            opacity: 0;
        }
    }

    .admin-management-container {
        max-width: 1400px;
        margin: 1rem auto 3rem auto;
        padding: 2rem;
        background: #fff;
        border-radius: 1rem;
        border: 1px solid #dee2e6;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }

    .table th, .table td {
        vertical-align: middle !important;
    }
</style>

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
            <thead class="table-light">
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
