@extends('admin.layouts.app')

@section('title', 'Admin Management')

@push('styles')
  <link rel="stylesheet" href="{{ asset('admin/css/admin-management.css') }}">
@endpush

@section('content')
<div class="admin-management-container">
    <h2 class="mb-4 d-flex justify-content-center align-items-center gap-2 text-primary fw-bold">
        <i class="bi bi-person-fill"></i> All Admins
    </h2>

    {{-- Toasts --}}
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

    @php $current = auth()->guard('admin')->user(); @endphp
    @if($current->role === 'super_admin')
        <a href="{{ route('admin.users.create') }}" class="btn btn-success mb-3">
            <i class="bi bi-person-plus-fill"></i> Add Admin
        </a>
    @endif

    <div class="table-responsive">
        <table id="adminsTable" class="table table-bordered table-hover align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th class="text-start">Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($admins as $admin)
                    <tr>
                        <td>{{ $admin->id }}</td>
                        <td class="text-start">{{ $admin->name }}</td>
                        <td>{{ $admin->email }}</td>
                        <td>{{ ucfirst($admin->role ?? 'admin') }}</td>
                        <td>
                            @if($admin->is_suspended)
                                <span class="badge bg-danger">Suspended</span>
                            @else
                                <span class="badge bg-success">Active</span>
                            @endif
                        </td>
                        <td>{{ $admin->created_at->format('d M Y') }}</td>
                        <td>
                            @if($current->role === 'super_admin' || $current->id === $admin->id)
                                <a href="{{ route('admin.users.edit', $admin->id) }}" class="btn btn-sm btn-primary mb-1">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                            @endif

                            @if($current->role === 'super_admin' && $current->id !== $admin->id)
                                {{-- Delete Form --}}
                                <form action="{{ route('admin.users.destroy', $admin->id) }}" method="POST" class="d-inline action-form">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger mb-1 action-btn" data-action="Delete" data-message="You need to delete this admin: {{ $admin->id }}">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>

                                {{-- Suspend/Activate --}}
                                <form action="{{ route('admin.users.suspend', $admin->id) }}" method="POST" class="d-inline action-form">
                                    @csrf @method('PUT')
                                    <button type="button"
                                        class="btn btn-sm {{ $admin->is_suspended ? 'btn-success' : 'btn-warning' }} action-btn"
                                        data-action="{{ $admin->is_suspended ? 'Activate' : 'Suspend' }}"
                                        data-message="{{ $admin->is_suspended ? 'Activate this admin: ' : 'Suspend this admin: ' }}{{ $admin->id }}">
                                        <i class="bi {{ $admin->is_suspended ? 'bi-check-circle' : 'bi-slash-circle' }}"></i>
                                        {{ $admin->is_suspended ? 'Activate' : 'Suspend' }}
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Image-Style Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4">
      <div class="modal-body">
        <div class="text-warning mb-3" style="font-size: 48px;">
          <i class="bi bi-exclamation-circle-fill"></i>
        </div>
        <h2 class="mb-3" id="modalConfirmTitle">Action Required</h5>
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
    // Toast
    document.querySelectorAll('.toast-notification').forEach(toast => {
        const timeout = setTimeout(() => toast.remove(), 4000);
        toast.querySelector('.toast-close').addEventListener('click', () => {
            clearTimeout(timeout);
            toast.remove();
        });
    });

    // DataTable
    $('#adminsTable').DataTable({
        responsive: true,
        paging: true,
        searching: true,
        ordering: true,
        info: true
    });

    // Modal Confirmation
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
