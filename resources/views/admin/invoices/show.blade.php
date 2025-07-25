@extends('admin.layouts.app')

@section('title', 'Invoice Details')

@push('styles')
  <style>
    .invoice-container {
      max-width: 600px;
      margin: 2rem auto;
      padding: 2rem;
      border: 1px solid #007bff; /* blue border */
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #fff;
      color: #004085; /* fallback text */
      border-radius: 6px;
      box-shadow: 0 0 8px rgba(0, 123, 255, 0.15);
    }
    .header-title {
      font-weight: 700;
      font-size: 1.8rem;
      color: #0056b3;
      margin-bottom: 1rem;
      border-bottom: 3px solid #0056b3;
      padding-bottom: 0.5rem;

      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 0.5rem;
      justify-content: center;
      text-align: center;
    }
    .header-title img {
      height: 50px;
      width: auto;
      flex-basis: 100%;
      margin-bottom: 0.3rem;
    }
    .header-title span {
      flex-basis: 100%;
    }
    @media (min-width: 576px) {
      .header-title {
        flex-wrap: nowrap;
        justify-content: flex-start;
        text-align: left;
      }
      .header-title img,
      .header-title span {
        flex-basis: auto;
        margin-bottom: 0;
      }
      .header-title img {
        margin-right: 1rem;
      }
    }

    /* New colors for labels and values */
    .section-label {
      font-weight: 600;
      color: #0056b3; /* blue-ish color for labels */
      width: 180px;
      display: inline-block;
    }
    .section-value {
      font-weight: 500;
      color: #212529; /* dark gray for values */
    }

    .amount-due {
      font-size: 1.8rem;
      font-weight: 700;
      color: #004085;
      margin-top: 1rem;
      margin-bottom: 1rem;
    }
    .outstanding {
      font-weight: 700;
      color: #dc3545; /* bootstrap danger/red */
      font-size: 1.25rem;
    }
    .info-row {
      margin-bottom: 0.8rem;
      word-wrap: break-word;
    }
    .footer-text {
      margin-top: 2rem;
      font-size: 0.85rem;
      color: #004085;
      text-align: center;
      border-top: 1px solid #007bff;
      padding-top: 1rem;
    }
    .powered-by {
      margin-top: 1rem;
      font-weight: 600;
      font-size: 0.9rem;
      color: #004085;
      text-align: center;
    }
    a {
      color: #0056b3;
      text-decoration: underline;
    }
    @media (max-width: 400px) {
      .invoice-container {
        padding: 1rem;
      }
      .section-label {
        width: 140px;
      }
      .header-title {
        font-size: 1.4rem;
      }
      .amount-due {
        font-size: 1.5rem;
      }
      .outstanding {
        font-size: 1.1rem;
      }
    }

    /* Back button styles */
    .back-button-container {
      max-width: 600px;
      margin: 0 auto 2rem auto;
      text-align: right;
    }
    .back-button {
      display: inline-block;
      padding: 0.5rem 1rem;
      background-color: #2563eb;
      color: white;
      border-radius: 0.375rem;
      text-decoration: none;
      font-weight: 600;
      transition: background-color 0.3s ease;
      user-select: none;
    }
    .back-button:hover {
      background-color: #1d4ed8;
      color: white;
      text-decoration: none;
    }
  </style>
@endpush

@section('content')
  <div class="invoice-container">

    <div class="header-title">
      <img src="https://www.srilankahosting.lk/logo.svg" alt="SriLankaHosting Logo" /><br>
      <span>SriLankaHosting Invoice</span>
    </div>

    <div class="info-row">
      <span class="section-label">Order Number :</span>
      <span class="section-value">{{ $order->id }}</span>
    </div>

    <div class="info-row">
      <span class="section-label">Customer Name :</span>
      <span class="section-value">{{ $order->first_name }} {{ $order->last_name }}</span>
    </div>

    <div class="info-row">
      <span class="section-label">Domain Name :</span>
      <span class="section-value">{{ $order->domain_name }}</span>
    </div>

    <div class="info-row">
      <span class="section-label">Email :</span>
      <span class="section-value">{{ $order->email }}</span>
    </div>

    <div class="info-row">
      <span class="section-label">Mobile :</span>
      <span class="section-value">{{ $order->mobile }}</span>
    </div>

    <div class="info-row">
      <span class="section-label">Billing Month :</span>
      <span class="section-value">{{ optional($order->created_at)->format('Y-F') ?? 'N/A' }}</span>
    </div>

    <div class="info-row">
      <span class="section-label">Billing Date :</span>
      <span class="section-value">{{ optional($order->created_at)->format('Y-m-d') ?? 'N/A' }}</span>
    </div>

    <div class="info-row">
      <span class="section-label">Payment Status :</span>
      <span class="section-value">
        @if(($order->payment_status ?? '') === 'paid')
          <span style="color: green; font-weight: 700;">PAID</span>
        @elseif(($order->payment_status ?? '') === 'pending')
          <span style="color: orange; font-weight: 700;">PENDING</span>
        @else
          <span style="color: grey; font-weight: 700;">UNKNOWN</span>
        @endif
      </span>
    </div>

    <div class="info-row amount-due">
      Total Amount Due: Rs. {{ number_format($order->price, 2) }}
    </div>

    @if(($order->payment_status ?? '') === 'pending')
      <div class="info-row">
        <span class="section-label">Outstanding :</span>
        <span class="outstanding">Rs. {{ number_format($order->price, 2) }}</span>
      </div>
    @endif

    <hr>

    <div class="powered-by">
      Powered by<br>
      <strong>SriLankaHosting.lk © 2025</strong>
    </div>

  </div>

  <div class="back-button-container">
    <a href="{{ url()->previous() }}" class="back-button">&larr; Back</a>
  </div>
@endsection
