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
    cursor: pointer;
    text-decoration: underline;
  }

  .resend-link.disabled {
    color: #a1a1a1;
    cursor: not-allowed;
    text-decoration: none;
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

  <p class="info-text">
    Enter the 6-digit code we sent to your mobile number to continue.
  </p>

  @if ($errors->has('otp'))
    <div class="alert alert-danger mb-3">
      {{ $errors->first('otp') }}
    </div>
  @endif

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
	  <small>OTP expires in <span id="countdown">05:00</span></small>
    <a href="javascript:void(0)" id="resendLink" class="resend-link">Resend OTP</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
  document.getElementById('otpForm').addEventListener('submit', function () {
    const button = document.getElementById('verifyBtn');
    button.disabled = true;
    button.innerHTML = `
      <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
      Verifying...
    `;
  });

  let timeLeft = {{ $remainingSeconds ?? 300 }};
  const countdownElement = document.getElementById('countdown');
  const otpInput = document.getElementById('otp');
  const verifyBtn = document.getElementById('verifyBtn');
  const resendLink = document.getElementById('resendLink');

  function updateTimer() {
    let minutes = Math.floor(timeLeft / 60);
    let seconds = timeLeft % 60;

    minutes = minutes < 10 ? '0' + minutes : minutes;
    seconds = seconds < 10 ? '0' + seconds : seconds;

    countdownElement.textContent = `${minutes}:${seconds}`;

    if (timeLeft <= 0) {
      clearInterval(timerInterval);
      countdownElement.textContent = "00:00";

      otpInput.disabled = true;
      verifyBtn.disabled = true;

      resendLink.classList.remove('disabled');
      resendLink.style.pointerEvents = 'auto';
    } else {
      resendLink.classList.add('disabled');
      resendLink.style.pointerEvents = 'none';
    }

    timeLeft--;
  }

  let timerInterval = setInterval(updateTimer, 1000);

  resendLink.addEventListener('click', function () {
    if (resendLink.classList.contains('disabled')) return;

    resendLink.textContent = 'Resending...';
    resendLink.classList.add('disabled');
    resendLink.style.pointerEvents = 'none';

    axios.post('{{ route("otp.resend") }}', {}, {
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      }
    })
    .then(response => {
      alert(response.data.message);

      // Reset timer to 5 minutes
      timeLeft = 300;

      // Enable input and button again
      otpInput.disabled = false;
      verifyBtn.disabled = false;

      // Clear previous timer and restart
      clearInterval(timerInterval);
      timerInterval = setInterval(updateTimer, 1000);
    })
    .catch(error => {
      alert('Error resending OTP, please try again later.');
      resendLink.classList.remove('disabled');
      resendLink.style.pointerEvents = 'auto';
    })
    .finally(() => {
      resendLink.textContent = 'Resend OTP';
    });
  });
</script>

@endsection
