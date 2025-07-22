@extends('admin.layouts.app')

@section('title', 'Trashed Admins')

@push('styles')
  <link rel="stylesheet" href="{{ asset('admin/css/admin-management.css') }}">
@endpush


@section('content')


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
