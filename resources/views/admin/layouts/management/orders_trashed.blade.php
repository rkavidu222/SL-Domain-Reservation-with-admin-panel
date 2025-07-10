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
    .table-filters {
        display: flex;
        flex-wrap: nowrap;
        gap: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 0.5rem;
        align-items: center;
    }
    .table-filters > * {
        font-size: 0.9rem;
        color: #495057;
    }
    .table-filters input[type="text"],
    .table-filters select,
    .table-filters input[type="date"] {
        padding: 0.3rem 0.5rem;
        font-size: 0.9rem;
        border-radius: 0.375rem;
        border: 1px solid #ced4da;
        transition: border-color 0.3s ease;
        min-width: 150px;
    }
    .table-filters input:focus,
    .table-filters select:focus {
        outline: none;
        border-color: #5c7cfa;
        box-shadow: 0 0 0 0.2rem rgba(92, 124, 250, 0.25);
    }
    .filter-buttons {
        margin-left: auto;
        display: flex;
        gap: 0.75rem;
    }
    .filter-buttons button,
    .filter-buttons a.btn {
        padding: 0.35rem 0.9rem;
        font-weight: 600;
        font-size: 0.9rem;
        border-radius: 0.375rem;
        min-width: 100px;
    }
    .filter-buttons button.btn-primary:hover {
        background-color: #4e68e7;
    }
    .filter-buttons a.btn-secondary:hover {
        background-color: #cdd3db;
        color: #343a40;
    }
    .table-responsive {
        overflow-x: auto;
    }
    table.table {
        width: 100%;
        border-collapse: collapse;
    }
    table.table thead {
        background-color: #f8f9fa;
    }
    table.table th,
    table.table td {
        padding: 0.75rem 1rem;
        border: 1px solid #dee2e6;
        text-align: center;
        font-size: 0.9rem;
        white-space: nowrap;
    }
    table.table th.text-start,
    table.table td.text-start {
        text-align: left;
        max-width: 150px;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    table.table tbody tr:hover {
        background-color: #f1f5fb;
    }
    .btn-sm {
        font-size: 0.8rem;
        padding: 0.3rem 0.6rem;
        border-radius: 0.35rem;
    }
    @media (max-width: 576px) {
        .table-filters {
            flex-wrap: wrap;
            gap: 0.8rem;
        }
        .table-filters > * {
            min-width: 150px;
            flex: 1 1 150px;
        }
        .filter-buttons {
            width: 100%;
            justify-content: flex-end;
            margin-top: 0.5rem;
        }
    }
    table.table thead th {
        position: sticky;
        top: 0;
        background: #f8f9fa;
        z-index: 10;
    }
    table.table tbody td:first-child,
    table.table thead th:first-child {
        position: sticky;
        left: 0;
        background: #fff;
        z-index: 11;
        box-shadow: 2px 0 5px -2px rgba(0,0,0,0.1);
    }
</style>

<div class="admin-management-container">
    <h2 class="mb-4 d-flex justify-content-center align-items-center gap-2 text-primary fw-bold">
        <i class="bi bi-trash3-fill"></i> Trashed Orders
    </h2>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('admin.orders.trash') }}" class="table-filters d-flex align-items-center justify-content-between" autocomplete="off">
        <div class="d-flex gap-2 align-items-center">
            <label for="category" class="visually-hidden">Category</label>
            <select id="category" name="category" aria-label="Filter by category">
                <option value="">All Categories</option>
                @foreach(['Cat1', 'Cat2', 'Cat3', 'Cat4', 'Cat5'] as $cat)
                    <option value="{{ $cat }}" {{ (isset($category) && $category == $cat) ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>

            <label for="date_from" class="visually-hidden">Date From</label>
            <input type="date" id="date_from" name="date_from" value="{{ old('date_from', $dateFrom ?? '') }}" aria-label="Date from">

            <label for="date_to" class="visually-hidden">Date To</label>
            <input type="date" id="date_to" name="date_to" value="{{ old('date_to', $dateTo ?? '') }}" aria-label="Date to">
        </div>

        <div class="d-flex gap-2 align-items-center">
            <label for="search" class="visually-hidden">Search</label>
            <input type="text" id="search" name="search" placeholder="Search customer, email, domain..." value="{{ old('search', $search ?? '') }}" aria-label="Search orders" style="min-width: 220px;">

            <div class="filter-buttons d-flex gap-2">
                <button type="submit" class="btn btn-primary">Search</button>
                @if(request()->hasAny(['search', 'category', 'date_from', 'date_to']))
                    <a href="{{ route('admin.orders.trash') }}" class="btn btn-secondary">Clear</a>
                @endif
            </div>
        </div>
    </form>

    {{-- Orders Table --}}
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
                        <td class="text-start">{{ $order->first_name }} {{ $order->last_name }}</td>
                        <td>{{ $order->email }}</td>
                        <td>{{ $order->mobile ?? '-' }}</td>
                        <td class="text-start">{{ $order->domain_name ?? 'N/A' }}</td>
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

        {{-- Pagination --}}
        <div class="d-flex justify-content-end mt-4">
            {{ $orders->appends(request()->query())->links() }}
        </div>
    @else
        <div class="alert alert-info text-center mt-4">
            No trashed orders found.
        </div>
    @endif
</div>

{{-- Auto-submit filters JS with debounce on search --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.querySelector('form.table-filters');
    if (!filterForm) return;

    ['category', 'date_from', 'date_to'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', () => filterForm.submit());
    });

    const search = document.getElementById('search');
    if (search) {
        let debounceTimer;
        search.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                if (search.value.length >= 2 || search.value.length === 0) {
                    filterForm.submit();
                }
            }, 600);
        });
    }
});
</script>
@endsection
