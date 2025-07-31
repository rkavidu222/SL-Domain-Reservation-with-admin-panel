@extends('layouts.app')

@section('title', 'Reservation Confirmed - SriLanka Hosting')

@push('styles')
  <link href="{{ asset('/resources/css/confirmation.css') }}" rel="stylesheet">
@endpush

@section('content')
<div class="card-animate">

  <div class="brand-logo">
    <img src="https://www.srilankahosting.lk/logo.svg" alt="SriLanka Hosting Logo" />
  </div>

  <div class="confirmation-icon">
    <i class="fa-solid fa-circle-check"></i>
  </div>

  <h2 class="confirmation-title">Reservation Confirmed!</h2>

  @if(session('paymentMethod') === 'skip')
    <p class="confirmation-subtitle">Your domain reservation was successful.</p>

    <p class="confirmation-message">
      Thank you for reserving your <strong>.lk domain</strong> with <strong>SriLanka Hosting</strong>.
    </p>

    <p class="confirmation-message text-muted">
      You've chosen to <strong>skip payment</strong> for now.<br>
      Our team will contact you within <strong>24 hours</strong> to help complete your order and activate your domain.
    </p>

  @elseif(session('paymentMethod') === 'paysecurely')
    <p class="confirmation-subtitle">Thank you for your payment submission.</p>

    <p class="confirmation-message">
      We have received your payment details for <strong>.lk domain</strong> registration.<br>
      Our team will review your payment proof and activate your domain shortly.<br>
      If there are any issues, we will contact you via email or phone.
    </p>
  @else
    <p class="confirmation-subtitle">Your domain reservation is being processed.</p>

    <p class="confirmation-message">
      Our team will contact you shortly with further information.
    </p>
  @endif

 @if(session('invoice_code'))
  <div class="alert alert-info mt-4" role="alert">
    <h5 class="alert-heading">Your Invoice is Ready</h5>
    <p>
      You can view your invoice here:
    </p>
    <a href="https://buydomains.srilankahosting.lk/invoice/view/{{ session('invoice_code') }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-sm">
      View Invoice <i class="fa-solid fa-arrow-up-right-from-square ms-1"></i>
    </a>
  </div>
@endif
  <div class="support-info">
    <p class="fw-semibold">Need help sooner?</p>

    <p><i class="fa-solid fa-phone icon"></i><strong>011 710 9693</strong> <span class="text-muted">(9AM - 5PM)</span></p>

    <p>
      <i class="fab fa-whatsapp icon"></i>
      <a href="https://wa.me/+94774233244" target="_blank" rel="noopener noreferrer">Chat with us on WhatsApp</a>
    </p>

    <p>
      <i class="fa-solid fa-envelope icon"></i>
      <a href="mailto:service@serverclub.lk">service@serverclub.lk</a>
    </p>
  </div>

  <a href="/" class="btn btn-primary btn-home">
    <i class="fas fa-arrow-left"></i> Back to Home
  </a>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form');
        if (!form) return; // Safety check if form not present
        const submitBtn = document.getElementById('submitBtn');
        if (!submitBtn) return; // Safety check if button not present
        const btnSpinner = document.getElementById('btnSpinner');
        const btnText = submitBtn.querySelector('.btn-text');

        form.addEventListener('submit', function () {
            // Disable the button to prevent multiple clicks
            submitBtn.disabled = true;

            // Hide button text and show spinner
            if (btnText) btnText.textContent = 'Please wait...';
            if (btnSpinner) btnSpinner.classList.remove('d-none');
        });
    });
</script>
@endsection
