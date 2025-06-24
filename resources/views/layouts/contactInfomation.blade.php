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
</style>
@endsection

@section('content')

<div class="form-card mx-auto mt-4" style="max-width: 500px;">
    <div class="brand-logo">
        <img src="https://www.srilankahosting.lk/logo.svg" alt="SriLanka Hosting Logo" />
    </div><br>

    <h3 class="form-title">
        <i class="bi bi-person-lines-fill"></i>Enter Your Contact Information
    </h3>

    <form action="{{ route('contact.submit') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="first_name" class="form-label">
                <i class="bi bi-person"></i>First Name
            </label>
            <input type="text" id="first_name" name="first_name" required
                class="form-control" placeholder="e.g. John" />
        </div>

        <div class="mb-3">
            <label for="last_name" class="form-label">
                <i class="bi bi-person"></i>Last Name
            </label>
            <input type="text" id="last_name" name="last_name" required
                class="form-control" placeholder="e.g. Doe" />
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">
                <i class="bi bi-envelope-at"></i>Email Address
            </label>
            <input type="email" id="email" name="email" required
                class="form-control" placeholder="e.g. john@example.com" />
        </div>

        <div class="mb-4">
            <label for="mobile" class="form-label">
                <i class="bi bi-telephone"></i>Mobile Number
            </label>
            <input type="tel" id="mobile" name="mobile" required
                class="form-control" placeholder="e.g. +94 77 123 4567" />
        </div>

        <button type="submit" class="submit-btn">
            <i class="bi bi-check-circle me-2"></i>Continue
        </button>
    </form>
</div>
@endsection
