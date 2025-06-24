@extends('layouts.app')

@section('title', '.LK Domain Reservation | SriLanka Hosting')

@section('head')
<style>
  body {
    background: #f3f6fa;
  }

  .reservation-card {
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

  .secure-badge {
    font-size: 0.9rem;
    color: #198754;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    margin-top: 0.25rem;
  }

  .secure-badge img {
    width: 20px;
    height: 20px;
  }

  .headline {
    text-align: center;
    font-weight: 500;
    font-size: 1rem;
    color: #343a40;
  }

  hr {
    margin-top: 1.5rem;
    margin-bottom: 1.5rem;
  }

  @media (max-width: 480px) {
    .reservation-card {
      margin: 1rem;
      padding: 1.25rem;
    }
  }
</style>
@endsection

@section('content')
<div class="reservation-card mx-auto mt-4" style="max-width: 500px;">

  <div class="brand-logo">
    <img src="https://www.srilankahosting.lk/logo.svg" alt="SriLanka Hosting Logo" />
  </div>

  <div class="mb-3 text-center">
    <div class="headline">Sri Lanka's Best Web Hosting Company</div>
    <div class="secure-badge">
      <img src="https://img.icons8.com/ios-filled/24/lock-2.png" alt="Lock Icon" />
      <span>100% Secure SSL Protected Checkout</span>
    </div>
  </div>

  <hr>

  @include('partials.price')
  @include('partials.trust')
  @include('partials.whyUs')

</div>
@endsection

@section('scripts')
<script src="https://widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js" async></script>
@endsection
