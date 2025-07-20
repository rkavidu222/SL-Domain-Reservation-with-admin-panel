@extends('admin.layouts.app')

@section('title', 'Quick SMS Send')

@section('content')
<style>
  .sms-send-container {
    max-width: 1100px;
    margin: 2rem auto;
    background: #fff;
    padding: 2rem 2.5rem;
    border-radius: 10px;
    border: 1px solid #ccc;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
  }
  .sms-section-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
  }
  textarea {
    resize: vertical;
  }
</style>

<div class="sms-send-container">
  <form method="POST" action="#">
    @csrf

    <div class="row mb-4">
      <div class="col-md-6">
        <label class="form-label sms-section-title">ORIGINATOR</label>
        <input type="text" class="form-control" value="SLHosting" readonly>
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-md-2">
        <label class="form-label">Country Code</label>
        <input type="text" class="form-control" value="+94" readonly>
      </div>
      <div class="col-md-10">
        <label class="form-label">Recipients <small class="text-muted">(Max 100 numbers)</small></label>
        <textarea class="form-control" rows="4" placeholder="Enter one number per line or paste numbers here (e.g., 771234567)"></textarea>
      </div>
    </div>

    <div class="mb-3">
      <p class="mb-1">TOTAL NUMBER OF RECIPIENTS: <strong>0</strong></p>
    </div>

    <div class="row mb-4">
      <div class="col-md-6">
        <label class="form-label">SMS Template (Optional)</label>
        <select class="form-select">
          <option selected disabled>Select one</option>
          <option>Welcome message</option>
          <option>Promotion message</option>
          <option>Reminder message</option>
        </select>
      </div>
    </div>

    <div class="mb-4">
      <label class="form-label">Message</label>
      <textarea class="form-control" rows="5" maxlength="160" placeholder='You can use spintax. Example: "{Hi|Hello} {John|Jane}"'></textarea>
      <div class="form-text mt-1">
        REMAINING: <strong>160 / 160</strong> (0 CHARACTERS)
        &nbsp;&nbsp; MESSAGE(S): 0
        &nbsp;&nbsp; (ENCODING: GSM_7BIT)
      </div>
    </div>

    <button type="submit" class="btn btn-primary px-4">Send SMS</button>
  </form>
</div>
@endsection
