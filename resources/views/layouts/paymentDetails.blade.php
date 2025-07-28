@extends('layouts.app')

@section('title', 'Payment Details | SriLanka Hosting')

@push('styles')
  <link href="{{ asset('/resources/css/payment.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="mobile-card">
  <div class="brand-logo">
    <img src="https://www.srilankahosting.lk/logo.svg" alt="SriLanka Hosting Logo" />
  </div>

  <div class="section-title">Payment Details</div>

  <!-- Payment Form -->
  <form method="POST" action="{{ route('payment.details') }}" onsubmit="showLoading()">

    @csrf
    <input type="hidden" name="order_id" value="{{ $order->id }}">

    <label for="domain">Domain Name</label>
    <input type="text" class="input" name="domain" id="domain" value="{{ $order->domain_name }}" readonly>

    <label for="price">Price</label>
    <input type="text" class="input" name="price" id="price" value="Rs. {{ number_format($order->price, 2) }}/=" readonly>

    <label for="payment_method">Payment Method</label>
    <div class="select-icon">
      <select name="payment_method" id="payment_method" class="select" required>
        <option value="" disabled selected>-- Select Payment Method --</option>
        <option value="visa">💳 Visa / MasterCard</option>
        <option value="bank">🏦 Bank Transfer</option>
        <option value="payhere">🌐 PayHere / WebXPay</option>
      </select>
    </div>

    <div class="payment-icons">
      <img src="https://img.icons8.com/color/48/visa.png" alt="Visa" />
      <img src="https://img.icons8.com/color/48/mastercard.png" alt="Mastercard" />
      <img src="https://img.icons8.com/color/48/bank-building.png" alt="Bank Transfer" />
      <img src="https://img.icons8.com/color/48/stripe.png" alt="Stripe" />
    </div>

    <div class="btn-row">
      <button id="submitBtn" type="submit" class="btn-action btn-primary">
        <i class="fa-solid fa-lock me-1"></i> Pay Securely
      </button>
    </div>
  </form>

  <form method="POST" action="{{ route('payment.skip') }}" id="skipForm" style="margin-top: 1rem;">
    @csrf
    <input type="hidden" name="order_id" value="{{ $order->id }}">
  </form>

  <div class="btn-row" style="margin-top: 1rem;">
    <button type="button" class="btn-action btn-secondary" id="skipBtn" onclick="handleSkip()">
      <i class="fa-solid fa-forward me-1"></i> Skip Payment
    </button>
  </div>
</div>

<script>
  function showLoading() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing...`;
  }

  function handleSkip() {
    const skipBtn = document.getElementById('skipBtn');
    skipBtn.disabled = true;
    skipBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Redirecting...`;

    // Submit the hidden skip payment form via POST
    document.getElementById('skipForm').submit();
  }
</script>
@endsection
