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
  <p class="confirmation-subtitle">Your domain reservation was successful.</p>

  <p class="confirmation-message">
    Thank you for reserving your <strong>.lk domain</strong> with <strong>SriLanka Hosting</strong>.
  </p>

  <p class="confirmation-message text-muted">
    You've chosen to <strong>skip payment</strong> for now.<br>
    Our team will contact you within <strong>24 hours</strong> to help complete your order and activate your domain.
  </p>

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
