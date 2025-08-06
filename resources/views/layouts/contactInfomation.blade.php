@extends('layouts.app')

@section('title', 'Domain Contact Information | SriLanka Hosting')

@push('styles')
  <link href="{{ asset('/resources/css/contact.css') }}" rel="stylesheet">
@endpush

@section('content')

@php
    $old_price = request()->query('old_price');
@endphp

<div class="form-card mx-auto mt-4" style="max-width: 500px;">
    <div class="brand-logo">
        <img src="https://www.srilankahosting.lk/logo.svg" alt="SriLanka Hosting Logo" />
    </div><br>

    @if(!empty($domain_name) && !empty($price) && !empty($category))
        <div class="info-box text-center">
            <h5>Domain Selected:</h5>
            <p>{{ $domain_name }}</p>

            <h5>Price:</h5>
            @if($old_price)
                <p>
                    <span class="text-muted text-decoration-line-through me-2">LKR {{ number_format($old_price, 2) }}/=</span>
                    <span class="price">LKR {{ number_format($price, 2) }}/=</span>
                </p>
            @else
                <p class="price">LKR {{ number_format($price, 2) }}/=</p>
            @endif

            <h5>Category:</h5>
            <p class="category">{{ $category }}</p>
        </div>
    @endif

    <h3 class="form-title">
        <i class="bi bi-person-lines-fill"></i>Enter Your Contact Information
    </h3>

    <form action="{{ route('domain.contact.submit') }}" method="POST" novalidate>
        @csrf

        {{-- Hidden fields to submit domain info (old price not included) --}}
        <input type="hidden" name="domain_name" value="{{ old('domain_name', $domain_name ?? '') }}">
        <input type="hidden" name="price" value="{{ old('price', $price ?? '') }}">
        <input type="hidden" name="category" value="{{ old('category', $category ?? '') }}">

        <div class="mb-3">
            <label for="first_name" class="form-label">
                <i class="bi bi-person"></i>First Name
            </label>
            <input type="text" id="first_name" name="first_name" required
                class="form-control @error('first_name') is-invalid @enderror"
                placeholder="John" value="{{ old('first_name') }}" />
            @error('first_name')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="last_name" class="form-label">
                <i class="bi bi-person"></i>Last Name
            </label>
            <input type="text" id="last_name" name="last_name" required
                class="form-control @error('last_name') is-invalid @enderror"
                placeholder="Doe" value="{{ old('last_name') }}" />
            @error('last_name')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">
                <i class="bi bi-envelope-at"></i>Email Address
            </label>
            <input type="email" id="email" name="email" required
                class="form-control @error('email') is-invalid @enderror"
                placeholder="john@example.com" value="{{ old('email') }}" />
            @error('email')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="mobile" class="form-label">
                <i class="bi bi-telephone"></i>Mobile Number
            </label>
            <input type="tel" id="mobile" name="mobile" required
                class="form-control @error('mobile') is-invalid @enderror"
                placeholder="07########" value="{{ old('mobile') }}" />
            @error('mobile')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

       <button type="submit" class="submit-btn" id="submitBtn">
			<i class="bi bi-check-circle me-2"></i>
			<span class="btn-text">Continue</span>
			<span class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true" id="btnSpinner"></span>
		</button>


    </form>
</div>



<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form');
        const submitBtn = document.getElementById('submitBtn');
        const btnSpinner = document.getElementById('btnSpinner');
        const btnText = submitBtn.querySelector('.btn-text');

        form.addEventListener('submit', function () {
            // Disable the button to prevent multiple clicks
            submitBtn.disabled = true;

            // Hide button text and show spinner
            btnText.textContent = 'Please wait...';
            btnSpinner.classList.remove('d-none');
        });
    });
</script>

@endsection
