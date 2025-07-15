@extends('layouts.app')

@section('title', 'Payment Details | SriLanka Hosting')

@section('head')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
  body {
    background: #f9fafb;
    font-family: 'Inter', sans-serif;
  }
  .mobile-card {
    background: #fff;
    border-radius: 1rem;
    padding: 1.5rem;
    box-shadow: 0 10px 24px rgba(0, 0, 0, 0.05);
    animation: fadeIn 0.3s ease-in-out;
    max-width: 500px;
    margin: 2rem auto;
  }
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }
  .brand-logo {
    display: flex;
    justify-content: center;
    margin-bottom: 1rem;
  }
  .brand-logo img {
    width: 150px;
  }
  .section-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #1e3a8a;
    text-align: center;
    margin-bottom: 1.2rem;
  }
  label {
    display: block;
    font-weight: 600;
    color: #334155;
    margin-top: 1rem;
    margin-bottom: 0.4rem;
    font-size: 0.95rem;
  }
  .input {
    width: 100%;
    padding: 0.7rem 0.9rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.6rem;
    font-size: 0.95rem;
    background-color: #f8fafc;
    box-sizing: border-box;
    font-weight: 500;
    color: #1f2937;
  }
  .select {
    width: 100%;
    padding: 0.7rem 0.9rem 0.7rem 2.5rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.6rem;
    font-size: 0.95rem;
    background-color: #f8fafc;
    box-sizing: border-box;
    background-image: url('data:image/svg+xml;utf8,<svg fill="%234b5563" height="20" viewBox="0 0 20 20" width="20" xmlns="http://www.w3.org/2000/svg"><path d="M5.5 7l4.5 4.5L14.5 7"/></svg>');
    background-repeat: no-repeat;
    background-position: right 0.9rem center;
    background-size: 1rem;
    appearance: none;
    font-weight: 500;
    color: #1f2937;
  }
  .select:focus {
    border-color: #60a5fa;
    box-shadow: 0 0 0 2px rgba(96, 165, 250, 0.4);
    outline: none;
  }
  .select-icon {
    position: relative;
  }
  .select-icon i {
    position: absolute;
    left: 0.9rem;
    top: 50%;
    transform: translateY(-50%);
    color: #475569;
    font-size: 1rem;
  }
  .btn-row {
    margin-top: 2rem;
    display: flex;
    flex-direction: column;
    gap: 0.9rem;
  }
  .btn-action {
    padding: 0.75rem;
    border: none;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 0.6rem;
    transition: 0.3s ease;
    text-align: center;
    text-decoration: none;
    cursor: pointer;
  }
  .btn-primary {
    background-color: #2563eb;
    color: #fff;
  }
  .btn-primary:hover {
    background-color: #1e40af;
  }
  .btn-secondary {
    background-color: #f1f5f9;
    color: #1e3a8a;
    border: 1px solid #cbd5e1;
  }
  .btn-secondary:hover {
    background-color: #e2e8f0;
  }
  .payment-icons {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin-top: 1.5rem;
  }
  .payment-icons img {
    height: 48px;
  }
</style>
@endsection

@section('content')
<div class="mobile-card">
  <div class="brand-logo">
    <img src="https://www.srilankahosting.lk/logo.svg" alt="SriLanka Hosting Logo" />
  </div>

  <div class="section-title">Payment Details</div>

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

      <button type="button" class="btn-action btn-secondary" id="skipBtn" onclick="handleSkip()">
        <i class="fa-solid fa-forward me-1"></i> Skip Payment
      </button>
    </div>
  </form>
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

    const orderId = "{{ $order->id }}";
    setTimeout(() => {
      window.location.href = "{{ route('payment.skip') }}" + "?order_id=" + orderId;
    }, 500);
  }
</script>
@endsection
