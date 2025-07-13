@extends('layouts.app')

@section('title', 'Domain Contact Information | SriLanka Hosting')

@section('head')
<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    body {
        background: #f3f6fa;
    }

    .form-card {
        background: #fff;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.05);
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .brand-logo {
        display: flex;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .brand-logo img {
        width: 150px;
    }

    .form-title {
        text-align: center;
        color: #0d6efd;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    label i {
        margin-right: 6px;
        color: #0d6efd;
    }

    .form-control:focus {
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        border-color: #0d6efd;
    }

    .submit-btn {
        background-color: #2563eb;
        color: #fff;
        font-weight: 600;
        border-radius: 0.5rem;
        padding: 0.6rem 1.2rem;
        transition: 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        border: none;
    }

    .submit-btn:hover {
        background-color: #1e40af;
    }

    @media (max-width: 480px) {
        .form-card {
            margin: 1rem;
            padding: 1.25rem;
        }
    }

    .error-text {
        color: #dc3545;
        font-size: 0.875em;
        margin-top: 0.25rem;
    }

    .info-box {
        background-color: #e9ecef;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
        user-select: none;
    }

    .info-box h5 {
        margin-bottom: 0.25rem;
        color: #495057;
        font-weight: 600;
    }

    .info-box p {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .info-box .price {
        color: #d6336c;
    }

    .info-box .category {
        color: #0d6efd;
        font-style: italic;
    }
</style>
@endsection

@section('content')

@php
    // Only for display purpose
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
                placeholder="e.g. John" value="{{ old('first_name') }}" />
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
                placeholder="e.g. Doe" value="{{ old('last_name') }}" />
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
                placeholder="e.g. john@example.com" value="{{ old('email') }}" />
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
                placeholder="e.g. +94 77 123 4567" value="{{ old('mobile') }}" />
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
