@extends('admin.layouts.app')

@section('title', 'Manage Domain Pricing')

@section('content')
<style>
    .management-container {
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
    .management-container:hover {
        box-shadow:
            0 8px 12px rgba(0, 0, 0, 0.15),
            0 12px 30px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="management-container">
    <h2 class="text-primary text-center mb-4 d-flex justify-content-center align-items-center gap-2">
        <i class="bi bi-currency-dollar fs-4"></i> Manage Domain Pricing
    </h2>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.domain_prices.update') }}">
        @csrf
        @foreach($prices as $price)
            <div class="card mb-3">
                <div class="card-header fw-bold">{{ $price->category }}</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Old Price</label>
                            <input type="number" step="0.01" class="form-control" name="prices[{{ $price->id }}][old_price]" value="{{ $price->old_price }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">New Price</label>
                            <input type="number" step="0.01" class="form-control" name="prices[{{ $price->id }}][new_price]" value="{{ $price->new_price }}">
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <button type="submit" class="btn btn-success">Update Prices</button>
    </form>
</div>
@endsection
