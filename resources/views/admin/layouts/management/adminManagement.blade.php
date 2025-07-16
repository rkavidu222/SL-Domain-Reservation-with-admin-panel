@extends('admin.layouts.app')

@section('title', 'Admin Management')

@section('content')
<style>
    /* Keep your existing container styles */
    .admin-management-container {
        max-width: 1400px;
        margin: 1rem auto 3rem auto;
        padding: 2rem;
        background: #fff;
        border-radius: 1rem;
        border: 1px solid #dee2e6;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 8px 20px rgba(0, 0, 0, 0.07);
        transition: box-shadow 0.3s ease;
    }
    .admin-management-container:hover {
        box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15), 0 12px 30px rgba(0, 0, 0, 0.1);
    }

    /* Smaller, friendlier Add Admin Button */
    .btn-add-admin {
        background-color: #28a745;       /* Bootstrap Success Green */
        color: white !important;
        font-weight: 600;
        padding: 0.375rem 1rem;           /* Smaller padding */
        border-radius: 0.375rem;          /* Slightly rounded corners */
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.9rem;                /* Slightly smaller font */
        user-select: none;
        transition: background-color 0.2s ease;
        text-decoration: none;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .btn-add-admin i {
        font-size: 1rem;                  /* Slightly smaller icon */
    }
    .btn-add-admin:hover,
    .btn-add-admin:focus {
        background-color: #218838;        /* Darker green on hover */
        color: white !important;
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        outline: none;
        text-decoration: none;
    }
</style>

<div class="admin-management-container">
    <h2 class="mb-4 d-flex justify-content-center align-items-center gap-2 text-primary fw-bold">
        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16" width="24" height="24">
            <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
        </svg>
        All Admins
    </h2>

    @php $current = auth()->guard('admin')->user(); @endphp
    @if($current->role === 'super_admin')
        <a href="{{ route('admin.users.create') }}" class="btn-add-admin">
            <i class="bi bi-person-plus-fill"></i> Add Admin
        </a>
    @endif

    <div class="table-responsive">
        <table id="adminsTable" class="table table-bordered table-hover align-middle text-center" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th class="text-start">Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th style="min-width: 180px;">Actions</th>
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
                                <form action="{{ route('admin.users.destroy', $admin->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this admin?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger mb-1" type="submit">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                                <form action="{{ route('admin.users.suspend', $admin->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ $admin->is_suspended ? 'Activate this admin?' : 'Suspend this admin?' }}');">
                                    @csrf
                                    @method('PUT')
                                    <button class="btn btn-sm {{ $admin->is_suspended ? 'btn-success' : 'btn-warning' }}" type="submit">
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
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#adminsTable').DataTable({
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
