@extends('layouts.app')

@section('title', 'Reservation Confirmed - SriLanka Hosting')

@section('head')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
<style>
  body {
    background-color: #f3f6fa;
    font-family: 'Inter', sans-serif;
  }

  .card-animate {
    background: #fff;
    border-radius: 1rem;
    padding: 2rem 2.5rem;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.07);
    animation: fadeIn 0.35s ease-in-out;
    max-width: 500px;
    margin: 3.5rem auto;
    text-align: center;
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

  .confirmation-icon {
    font-size: 4rem;
    color: #22c55e;
    margin-bottom: 1rem;
    animation: popIn 0.5s ease forwards;
  }

  @keyframes popIn {
    0% {
      transform: scale(0.5);
      opacity: 0;
    }
    80% {
      transform: scale(1.15);
      opacity: 1;
    }
    100% {
      transform: scale(1);
    }
  }

  .confirmation-title {
    font-size: 2rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.25rem;
  }

  .confirmation-subtitle {
    font-size: 1rem;
    color: #4b5563;
    margin-bottom: 1.5rem;
  }

  .confirmation-message {
    font-size: 1rem;
    color: #475569;
    margin-top: 1rem;
    line-height: 1.6;
  }


  .support-info {
    font-size: 1rem;
    color: #334155;
    margin-top: 2rem;
    padding: 1.5rem 1.75rem;
    text-align: left;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    background-color: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
  }

  .support-info p,
  .support-info a {
    margin-bottom: 0.6rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .support-info p.fw-semibold {
    font-weight: 700;
    font-size: 1.1rem;
    margin-bottom: 1rem;
  }

  .support-info a {
    color: #2563eb;
    text-decoration: none;
    font-weight: 600;
  }

  .support-info a:hover {
    text-decoration: underline;
  }

  .icon {
    font-size: 1.2rem;
  }

  .btn-home {
    margin-top: 2rem;
    font-size: 1rem;
    padding: 0.65rem 1.75rem;
    border-radius: 0.5rem;
    box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
    display: inline-flex;
    align-items: center;
  }

  .btn-home:hover {
    background-color: #1e40af;
    box-shadow: 0 6px 15px rgba(30, 64, 175, 0.5);
  }

  .btn-home i {
    margin-right: 0.5rem;
  }

  .brand-logo {
    display: flex;
    justify-content: center;
    margin-bottom: 1.75rem;
  }

  .brand-logo img {
    width: 150px;
  }
</style>
@endsection

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
