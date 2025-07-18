@extends('admin.layouts.app')

@section('title', 'Trashed Admins')

@section('content')
<style>
    .admin-management-container {
        max-width: 1400px;
        margin: 1rem auto 2rem;
        padding: 2rem;
        background: #fff;
        border-radius: 1rem;
        border: 1px solid #dee2e6;
        box-shadow:
            0 4px 6px rgba(0, 0, 0, 0.1),
            0 8px 20px rgba(0, 0, 0, 0.07);
        transition: box-shadow 0.3s ease;
        overflow-x: auto;
    }

    .admin-management-container:hover {
        box-shadow:
            0 8px 12px rgba(0, 0, 0, 0.15),
            0 12px 30px rgba(0, 0, 0, 0.1);
    }

    table {
        width: 100%;
        min-width: 600px; /* prevent table collapsing on small screens */
    }

    @media (max-width: 768px) {
        .admin-management-container {
            padding: 1rem;
            margin: 1rem;
        }
        table {
            min-width: 100%;
        }
    }

    /* Toast styles */
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
    <h2 class="text-primary text-center mb-4 d-flex justify-content-center align-items-center gap-2 flex-wrap">
        <i class="bi bi-trash3-fill fs-4"></i> Trashed Admins
    </h2>

    {{-- Flash Message Toasts --}}
    @if(session('success'))
        <div class="toast-notification toast-success" role="alert" aria-live="polite" aria-atomic="true">
            <div class="toast-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 6L9 17l-5-5"/>
                </svg>
            </div>
            <div class="toast-message">
                {!! session('success') !!}
            </div>
            <button class="toast-close" aria-label="Close notification">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="toast-notification toast-error" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12" y2="16"/>
                </svg>
            </div>
            <div class="toast-message">
                {!! session('error') !!}
            </div>
            <button class="toast-close" aria-label="Close notification">&times;</button>
        </div>
    @endif

    @if($trashedAdmins->isEmpty())
        <div class="alert alert-info text-center">No trashed admins found.</div>
    @else
        <div class="table-responsive">
            <table id="trashedAdminsTable" class="table table-bordered table-hover align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th class="text-start">Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Deleted At</th>
                        <th style="min-width: 180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trashedAdmins as $admin)
                        <tr>
                            <td>{{ $admin->id }}</td>
                            <td class="text-start">{{ $admin->name }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>{{ ucfirst($admin->role) }}</td>
                            <td>{{ $admin->deleted_at->format('d M Y') }}</td>
                            <td>
                                {{-- Restore --}}
                                <form action="{{ route('admin.users.restore', $admin->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to restore this admin?');">
									@csrf
									@method('PUT')
									<button type="submit" class="btn btn-success btn-sm mb-1">
										<i class="bi bi-arrow-counterclockwise"></i> Restore
									</button>
								</form>


                                {{-- Permanent Delete --}}
                                <form action="{{ route('admin.users.forceDelete', $admin->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this admin?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="bi bi-x-circle"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#trashedAdminsTable').DataTable({
            paging: true,
            lengthChange: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: true,
        });
    });

    // Toast notification close behavior
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
