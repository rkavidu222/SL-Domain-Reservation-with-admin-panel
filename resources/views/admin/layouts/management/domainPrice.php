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
	<h2 class="mb-4 d-flex justify-content-center align-items-center gap-2 text-primary fw-bold flex-wrap text-center">
		<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-tag-fill" viewBox="0 0 16 16" width="24" height="24">
			<path d="M2 1a1 1 0 0 0-1 1v4.586a1 1 0 0 0 .293.707l7.414 7.414a2 2 0 0 0 2.828 0l4.586-4.586a2 2 0 0 0 0-2.828L7.121 1.293A1 1 0 0 0 6.414 1H2zm3.5 3a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
		</svg>
		 Domain Price Management
	</h2>


    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @foreach($prices as $price)
        <form method="POST" action="{{ route('admin.domain_prices.update.single', $price->id) }}" class="mb-4">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-header fw-bold">{{ $price->category }}</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Old Price</label>
                            <input type="number" step="0.01" class="form-control" name="old_price" value="{{ $price->old_price }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">New Price</label>
                            <input type="number" step="0.01" class="form-control" name="new_price" value="{{ $price->new_price }}" required>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-success">Update {{ $price->category }}</button>
                </div>
            </div>
        </form>
    @endforeach
</div>
@endsection
