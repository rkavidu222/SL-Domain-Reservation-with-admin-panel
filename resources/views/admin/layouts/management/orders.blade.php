@extends('admin.layouts.app')

@section('title', 'Order Management')

@section('content')
<style>
    .admin-management-container {
        max-width: 1100px;
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
</style>

<div class="admin-management-container">
    <h2 class="mb-4 d-flex justify-content-center align-items-center gap-2 text-primary text-center fw-bold">
        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-cart-fill" viewBox="0 0 16 16" width="24" height="24">
            <path d="M0 1.5A.5.5 0 0 1 .5 1h1a.5.5 0 0 1 .485.379L2.89 6H14.5a.5.5 0 0 1 .49.598l-1.5 7A.5.5 0 0 1 13 14H4a.5.5 0 0 1-.491-.408L1.01 2H.5a.5.5 0 0 1-.5-.5z"/>
            <path d="M5 12a2 2 0 1 0 4 0H5z"/>
        </svg>
        All Orders
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
    <form method="GET" action="{{ route('admin.orders.index') }}" class="mb-3 d-flex justify-content-end">
        <input type="text" name="search" class="form-control w-auto" placeholder="Search orders..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-primary ms-2">Search</button>
        @if(request('search'))
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary ms-2">Clear</a>
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
                        <th>Order Date</th>
                        <th style="min-width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td class="text-start text-truncate" style="max-width: 150px;">{{ $order->first_name }} {{ $order->last_name }}</td>
                        <td>{{ $order->email }}</td>
                        <td>{{ $order->mobile ?? '-' }}</td>
                        <td class="text-start text-truncate" style="max-width: 150px;">{{ $order->domain_name ?? 'N/A' }}</td>
                        <td>{{ $order->category ?? '-' }}</td>
                        <td>Rs.{{ number_format($order->price, 2) }}</td>
                        <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info mb-1">
                                <i class="bi bi-eye"></i> View
                            </a>
                            <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this order?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger mb-1" type="submit">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $orders->appends(['search' => request('search')])->links() }}
        </div>
    @else
        <div class="alert alert-info text-center">
            No orders found.
        </div>
    @endif
</div>
@endsection
