@extends('admin.layouts.app')

@section('title', 'Admin Management')

@section('content')
<style>
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


    .actions-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        gap: 1rem;
    }


    .btn-add-admin {
		background-color: #28a745;
		color: white;
		font-weight: 700;
		padding: 0.6rem 1.5rem;
		border-radius: 50px;
		border: none;
		cursor: pointer;
		display: flex;
		align-items: center;
		gap: 0.5rem;
		font-size: 1rem;
		text-decoration: none;
		user-select: none;
		transition: none;
	}

	.btn-add-admin:hover,
	.btn-add-admin:focus {
		background-color: #28a745;
		color: white;
		box-shadow: none;
		outline: none;
		text-decoration: none;
	}


    form.search-form {
        display: flex;
        gap: 0.75rem;
        flex-wrap: nowrap;
        flex: 1 1 auto;
        min-width: 250px;
        max-width: 500px;
    }
    form.search-form input[type="text"] {
        flex-grow: 1;
        min-width: 0;
    }
    form.search-form button,
    form.search-form a.btn {
        flex-shrink: 0;
        min-width: 100px;
    }

    /* Responsive */
    @media (max-width: 576px) {
        .actions-row {
            flex-direction: column;
            align-items: stretch;
        }
        form.search-form {
            max-width: 100%;
        }
        .btn-add-admin {
            justify-content: center;
        }
    }
</style>

<div class="admin-management-container">
    <h2 class="mb-4 d-flex justify-content-center align-items-center gap-2 text-primary fw-bold">
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

    @php $current = auth()->guard('admin')->user(); @endphp

    {{-- Actions row: Add Admin Button on left + Search on right --}}
    <div class="actions-row">
        @if($current->role === 'super_admin')
            <a href="{{ route('admin.users.create') }}" class="btn-add-admin">
                <i class="bi bi-person-plus-fill"></i> Add Admin
            </a>
        @endif

        <form method="GET" action="{{ route('admin.users.index') }}" class="search-form">
            <input
                type="text"
                name="search"
                class="form-control"
                placeholder="Search name, email or role..."
                value="{{ request('search') }}"
                aria-label="Search admins"
            >
            <button type="submit" class="btn btn-primary">Search</button>
            @if(request('search'))
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Clear</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-center">
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
                @forelse($admins as $admin)
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
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No admin records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-center mt-3">
        {{ $admins->appends(['search' => request('search')])->links() }}
    </div>
</div>
@endsection
