@extends('admin.layouts.app')

@section('title', 'Trashed Orders')

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

    .btn-sm:hover {
        transform: translateY(-2px);
        transition: transform 0.2s ease;
    }

    @media (max-width: 576px) {
        .admin-management-container {
            padding: 1rem;
        }

        .table th,
        .table td {
            font-size: 0.75rem;
            white-space: nowrap;
        }

        .table td.text-start {
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    }

    /* Search form style */
    form.search-form {
        max-width: 400px;
        margin-bottom: 1.5rem;
        display: flex;
        gap: 0.5rem;
        justify-content: flex-end;
    }
</style>

<div class="admin-management-container">
    <h2 class="text-primary text-center mb-4 d-flex justify-content-center align-items-center gap-2">
        <i class="bi bi-trash3-fill fs-4"></i> Trashed Orders
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



	{{-- Search Form --}}
        <form method="GET" action="{{ route('admin.orders.trash') }}" class="mb-3 d-flex justify-content-end">
            <input
                type="text"
                name="search"
                class="form-control w-auto"
                placeholder="Search name, email or role..."
                value="{{ request('search') }}"
            >
            <button type="submit" class="btn btn-primary ms-2">Search</button>
            @if(request('search'))
                <a href="{{ route('admin.orders.trash') }}" class="btn btn-secondary ms-2">Clear</a>
            @endif
        </form>


    @if($orders->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th class="text-start">Customer Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th class="text-start">Domain</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Deleted At</th>
                        <th style="min-width: 160px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td class="text-start text-truncate">{{ $order->first_name }} {{ $order->last_name }}</td>
                        <td>{{ $order->email }}</td>
                        <td>{{ $order->mobile ?? '-' }}</td>
                        <td class="text-start text-truncate">{{ $order->domain_name ?? 'N/A' }}</td>
                        <td>{{ $order->category ?? '-' }}</td>
                        <td>Rs.{{ number_format($order->price, 2) }}</td>
                        <td>{{ $order->deleted_at->format('d M Y H:i') }}</td>
                        <td>
                            <form action="{{ route('admin.orders.restore', $order->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success mb-1">
                                    <i class="bi bi-arrow-counterclockwise"></i> Restore
                                </button>
                            </form>
                            <form action="{{ route('admin.orders.forceDelete', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete permanently?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger mb-1">
                                    <i class="bi bi-x-circle"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $orders->appends(request()->except('page'))->links() }}
        </div>
    @else
        <div class="alert alert-info text-center">
            No trashed orders found.
        </div>
    @endif
</div>
@endsection
