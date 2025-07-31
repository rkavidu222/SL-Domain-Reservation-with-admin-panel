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

    {{-- Toast Message Toasts --}}
    @if(session('success'))
        <div class="toast-notification toast-success" role="alert">
            <div class="toast-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="toast-message">{{ session('success') }}</div>
            <button class="toast-close">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="toast-notification toast-error" role="alert">
            <div class="toast-icon">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <div class="toast-message"><strong>Error:</strong> {{ session('error') }}</div>
            <button class="toast-close">&times;</button>
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
                                <form action="{{ route('admin.users.restore', $admin->id) }}" method="POST" class="d-inline action-form">
                                    @csrf
                                    @method('PUT')
                                    <button type="button"
                                        class="btn btn-success btn-sm action-btn"
                                        data-message="Are you sure you want to restore admin: {{ $admin->name }} ({{ $admin->email }})?">
                                        <i class="bi bi-arrow-counterclockwise"></i> Restore
                                    </button>
                                </form>

                                {{-- Permanent Delete --}}
                                <form action="{{ route('admin.users.forceDelete', $admin->id) }}" method="POST" class="d-inline action-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        class="btn btn-danger btn-sm action-btn"
                                        data-message="Are you sure you want to permanently delete admin: {{ $admin->name }} ({{ $admin->email }})?">
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

<!-- Confirmation Modal (same style as your Admin Management) -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4">
      <div class="modal-body">
        <div class="text-warning mb-3" style="font-size: 48px;">
          <i class="bi bi-exclamation-circle-fill"></i>
        </div>
        <h2 class="mb-3" id="modalConfirmTitle">Action Required</h2>
        <p id="modalConfirmMessage">You need to confirm this action.</p>
        <div class="d-flex justify-content-center gap-3 mt-4">
          <button type="button" class="btn btn-danger" id="modalConfirmYes">Yes</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Toast notifications close
    document.querySelectorAll('.toast-notification').forEach(toast => {
        const timeout = setTimeout(() => toast.remove(), 4000);
        toast.querySelector('.toast-close').addEventListener('click', () => {
            clearTimeout(timeout);
            toast.remove();
        });
    });

    // DataTable initialization for trashed admins
    $('#trashedAdminsTable').DataTable({
        responsive: true,
        paging: true,
        searching: true,
        ordering: true,
        info: true
    });

    // Confirmation modal logic (shared with your admin management style)
    const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
    const modalMessage = document.getElementById('modalConfirmMessage');
    const confirmBtn = document.getElementById('modalConfirmYes');
    let currentForm = null;

    document.querySelectorAll('.action-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            currentForm = this.closest('form');
            modalMessage.textContent = this.getAttribute('data-message') || 'Confirm this action?';
            modal.show();
        });
    });

    confirmBtn.addEventListener('click', () => {
        if (currentForm) currentForm.submit();
    });
});
</script>
@endsection
