@extends('admin.layouts.app')

@section('title', 'Trashed Admins')

@section('content')
<style>
    .admin-management-container {
        max-width: 1100px;
        margin: 1rem auto 2rem;
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
</style>

<div class="admin-management-container">
    <h2 class="text-primary text-center mb-4 d-flex justify-content-center align-items-center gap-2">
        <i class="bi bi-trash3-fill fs-4"></i> Trashed Admins
    </h2>

    {{-- Flash messages --}}
    @if(Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ Session::get('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($trashedAdmins->isEmpty())
        <div class="alert alert-info text-center">No trashed admins found.</div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th class="text-start">Name</th>
                        <th>Email</th>
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
                        <td>{{ $admin->deleted_at->format('d M Y h:i A') }}</td>
                        <td>
                            {{-- Restore --}}
                            <form action="{{ route('admin.users.restore', $admin->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success btn-sm">
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

    <div class="d-flex justify-content-center mt-3">
        {{ $trashedAdmins->links() }}
    </div>
</div>
@endsection
