@extends('admin.layouts.app')

@section('title', 'Admin Management')

@section('content')
<style>
    .admin-management-container {
        max-width: 1100px;
        margin: 1rem auto 3rem auto;
        padding: 2rem;
        background: #fff;
        border-radius: 1rem;
        border: 1px solid #dee2e6;
        box-shadow:
            0 4px 6px rgba(0, 0, 0, 0.1),
            0 8px 20px rgba(0, 0, 0, 0.07);
        transition: box-shadow 0.3s ease;
    }

    .admin-management-container:hover {
        box-shadow:
            0 8px 12px rgba(0, 0, 0, 0.15),
            0 12px 30px rgba(0, 0, 0, 0.1);
    }

    /* Slight hover effect on action buttons */
    .btn-sm:hover {
        transform: translateY(-2px);
        transition: transform 0.2s ease;
    }

    @media (max-width: 576px) {
        .admin-management-container {
            padding: 1rem;
        }

        .table td,
        .table th {
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .table-responsive {
            overflow-x: auto;
        }
    }
</style>

<div class="admin-management-container">
    <h2 class="mb-4 d-flex justify-content-center align-items-center gap-2 text-primary text-center fw-bold">
        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16" width="24" height="24">
            <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
        </svg>
        All Admins
    </h2>

    {{-- Flash Messages --}}
    @if(Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ Session::get('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(Session::has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ Session::get('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col" class="text-start">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Role</th>
                    <th scope="col">Status</th>
                    <th scope="col">Joined</th>
                    <th scope="col" style="min-width: 180px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($admins as $admin)
                <tr>
                    <td>{{ $admin->id }}</td>
                    <td class="text-start text-truncate" style="max-width: 200px;">{{ $admin->name }}</td>
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
                        @php $current = auth()->guard('admin')->user(); @endphp

                        {{-- Edit button shown if super admin or editing own profile --}}
                        @if($current->role === 'super_admin' || $current->id === $admin->id)
                            <a href="{{ route('admin.users.edit', $admin->id) }}" class="btn btn-sm btn-primary mb-1">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                        @endif

                        {{-- Suspend/Delete buttons only for super admin and not self --}}
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

    <div class="d-flex justify-content-center mt-3">
        {{ $admins->links() }}
    </div>
</div>
@endsection
