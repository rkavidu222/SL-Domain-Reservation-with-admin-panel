@extends('layouts.app')

@section('title', 'OTP Verification | SriLanka Hosting')

@push('styles')
  <link href="{{ asset('/resources/css/otp.css') }}" rel="stylesheet">
@endpush


@section('content')

<div class="mx-auto mt-4 otp-card" style="max-width: 500px;">
  <div class="text-center mb-3">
    <div class="otp-icon">📲</div>
    <h5 class="text-primary fw-semibold mt-2">OTP Verification</h5>
  </div>


  <p class="info-text">
    Enter the 6-digit code we sent to your mobile number to continue.
  </p>


<!--

<p class="info-text">
  Enter the 6-digit code we sent to your mobile number to continue.
</p>

@if(session('otp'))
  <div class="alert alert-info text-center">
    <strong>Testing Only:</strong> Your OTP is <code>{{ session('otp') }}</code>
  </div>
@endif

-->

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
