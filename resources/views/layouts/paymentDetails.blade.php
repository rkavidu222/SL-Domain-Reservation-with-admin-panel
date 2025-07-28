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
  <form method="POST" action="{{ route('payment.paysecurely') }}" onsubmit="showLoading()">
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
        <option value="bank">🏦 Bank Transfer</option>
        {{-- Add more payment methods here if needed --}}
      </select>
    </div>

    <!-- Bank Details (Shown only if "Bank Transfer" selected) -->
    <div id="bankDetails" class="bank-info" style="display: none;">
      <div class="bank-card">
        <strong>BANK 01 -</strong><br>
        Bank Name: <strong>SAMPATH BANK</strong><br>
        Branch Name and Code: <strong>BIBILE / 161</strong><br>
        Account Holder: <strong>ServerClub.LK (Private) Limited</strong><br>
        Account Number: <strong>116114023727</strong><br><br>
        <span class="text-success">
          After making the payment, please upload the proof via WhatsApp:<br>
          <a href="https://wa.me/94774233244" target="_blank" class="btn btn-sm btn-success mt-2">
            <i class="fa-brands fa-whatsapp me-1"></i> WhatsApp Us
          </a>
        </span>
      </div>
    </div>

    <!-- Payment Icons -->
    <div class="payment-icons mt-3">
      <img src="https://img.icons8.com/color/48/bank-building.png" alt="Bank Transfer" />
    </div>

    <!-- Submit Button -->
    <div class="btn-row mt-3">
      <button id="submitBtn" type="submit" class="btn-action btn-primary">
        <i class="fa-solid fa-lock me-1"></i> Pay Securely
      </button>
    </div>
  </form>

  <!-- Skip Payment Form -->
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
    document.getElementById('skipForm').submit();
  }

  // Show bank info when 'Bank Transfer' is selected
  document.getElementById('payment_method').addEventListener('change', function () {
    const bankInfo = document.getElementById('bankDetails');
    bankInfo.style.display = this.value === 'bank' ? 'block' : 'none';
  });
</script>
@endsection
