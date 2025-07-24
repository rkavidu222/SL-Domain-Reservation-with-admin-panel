@extends('admin.layouts.app')

@section('title', 'Invoice Details')

@push('styles')
  <link rel="stylesheet" href="{{ asset('admin/css/order.css') }}">
@endpush

@section('content')
<div class="admin-management-container p-4">
  <h2 class="mb-4 text-primary fw-bold">Invoice #{{ $order->id }}</h2>
  <hr>

  <div class="row gy-3">
    <div class="col-md-6 d-flex">
      <strong class="me-2" style="min-width: 120px;">Domain:</strong>
      <span>{{ $order->domain_name }}</span>
    </div>
    <div class="col-md-6 d-flex">
      <strong class="me-2" style="min-width: 120px;">Customer:</strong>
      <span>{{ $order->first_name }} {{ $order->last_name }}</span>
    </div>
    <div class="col-md-6 d-flex">
      <strong class="me-2" style="min-width: 120px;">Email:</strong>
      <span>{{ $order->email }}</span>
    </div>
    <div class="col-md-6 d-flex">
      <strong class="me-2" style="min-width: 120px;">Mobile:</strong>
      <span>{{ $order->mobile }}</span>
    </div>
    <div class="col-md-6 d-flex align-items-center">
      <strong class="me-2" style="min-width: 120px;">Status:</strong>
      <span>
        <span class="badge
		  @if($order->payment_status === 'paid') bg-success
		  @elseif($order->payment_status === 'pending') bg-warning text-dark
		  @else bg-secondary @endif"
		  style="padding: 0.5em 0.75em; font-size: 1rem;">
		  {{ ucfirst($order->payment_status ?? 'Unknown') }}
		</span>

      </span>
    </div>
    <div class="col-md-6 d-flex">
      <strong class="me-2" style="min-width: 120px;">Price:</strong>
      <span>Rs. {{ number_format($order->price, 2) }}</span>
    </div>
    <div class="col-md-6 d-flex">
      <strong class="me-2" style="min-width: 120px;">Date:</strong>
      <span>{{ $order->created_at->format('Y-m-d H:i') }}</span>
    </div>
  </div>

	<div class="mt-4 text-end">
	  <a href="{{ route('admin.invoices.index') }}" class="btn btn-primary px-4">Back</a>
	</div>


</div>
@endsection
