@extends('layouts.app')

@section('title', 'OTP Verification | SriLanka Hosting')

@section('head')
<style>
  body {
    background: #f8fafc;
  }

  .otp-card {
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

  .otp-input {
    font-size: 1.2rem;
    letter-spacing: 0.3rem;
    text-align: center;
    padding: 0.6rem;
    border-radius: 0.5rem;
    border: 1px solid #ccc;
    width: 100%;
    transition: 0.2s;
  }

  .otp-input:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
    outline: none;
  }

  .verify-btn {
    background-color: #2563eb;
    border: none;
    padding: 0.6rem 1rem;
    font-size: 0.95rem;
    font-weight: 600;
    color: #fff;
    border-radius: 0.5rem;
    width: 100%;
    transition: background-color 0.2s ease-in-out;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
  }

  .verify-btn:hover {
    background-color: #1e40af;
  }

  .verify-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
  }

  .spinner-border-sm {
    width: 1rem;
    height: 1rem;
    border-width: 0.15em;
  }

  .info-text {
    font-size: 0.85rem;
    color: #6b7280;
    text-align: center;
    margin-bottom: 1rem;
  }

  .resend-link {
    font-size: 0.85rem;
    color: #475569;
    display: inline-block;
    margin-top: 1rem;
  }

  .resend-link:hover {
    color: #2563eb;
    text-decoration: underline;
  }

  .otp-icon {
    font-size: 1.75rem;
    color: #2563eb;
  }

  @media (max-width: 480px) {
    .otp-card {
      margin: 1rem;
      padding: 1.25rem;
    }

    .otp-input {
      font-size: 1.1rem;
      letter-spacing: 0.2rem;
    }

    .verify-btn {
      font-size: 0.9rem;
      padding: 0.55rem 1rem;
    }
  }
</style>
@endsection

@section('content')

<div class="mx-auto mt-4 otp-card" style="max-width: 500px;">
  <div class="text-center mb-3">
    <div class="otp-icon">📲</div>
    <h5 class="text-primary fw-semibold mt-2">OTP Verification</h5>
  </div>

  <p class="info-text">Enter the 6-digit code we sent to your mobile number to continue.</p>

  <form action="{{ route('payment.details') }}" method="POST" id="otpForm">
    @csrf
    <div class="mb-3">
      <input type="text" class="otp-input" id="otp" name="otp" placeholder="XXXXXX" required maxlength="6" minlength="4" pattern="\d+">
    </div>

    <button type="submit" class="verify-btn" id="verifyBtn">
      ✅ Verify & Continue
    </button>
  </form>

  <div class="text-center">
    <a href="#" class="resend-link">Resend OTP</a>
  </div>
</div>

<script>
  document.getElementById('otpForm').addEventListener('submit', function () {
    const button = document.getElementById('verifyBtn');
    button.disabled = true;
    button.innerHTML = `
      <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
      Verifying...
    `;
  });
</script>

@endsection
