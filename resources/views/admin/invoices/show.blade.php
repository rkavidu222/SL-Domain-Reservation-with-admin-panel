@extends('admin.layouts.app')

@section('title', 'Invoice Details')

@push('styles')
  <style>
    .invoice-container {
      max-width: 600px;
      margin: 2rem auto;
      padding: 2rem;
      border: 1px solid #ddd;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #fff;
      color: #333;
    }
    .header-title {
      font-weight: 700;
      font-size: 1.6rem;
      color: #2f3e6e;
      margin-bottom: 1rem;
      border-bottom: 2px solid #2f3e6e;
      padding-bottom: 0.5rem;
    }
    .section-label {
      font-weight: 600;
      color: #444;
      width: 180px;
      display: inline-block;
    }
    .section-value {
      font-weight: 500;
    }
    .amount-due {
      font-size: 1.8rem;
      font-weight: 700;
      color: #d9534f;
      margin-top: 1rem;
      margin-bottom: 1rem;
    }
    .info-row {
      margin-bottom: 0.8rem;
    }
    .footer-text {
      margin-top: 2rem;
      font-size: 0.85rem;
      color: #777;
      text-align: center;
      border-top: 1px solid #ddd;
      padding-top: 1rem;
    }
    .powered-by {
      margin-top: 1rem;
      font-weight: 600;
      font-size: 0.9rem;
      color: #555;
      text-align: center;
    }
  </style>
@endpush

@section('content')
<div class="invoice-container">

  <div class="header-title">SriLankaHosting Invoice</div>

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

  <div class="info-row">
    <span class="section-label">Outstanding :</span>
    <span class="section-value">Rs. {{ number_format(($order->outstanding ?? 0), 2) }}</span>
  </div>

  <div class="info-row" style="font-size: 0.9rem; font-style: italic; margin-top: 1rem;">
    (Full or partial payments accepted)
  </div>

  <hr>

  <div style="margin-top: 1rem; font-size: 0.9rem;">
    To receive invoices monthly via email, register <a href="mailto:{{ $order->email }}" style="color:#2f3e6e;">here</a>.
  </div>

  <div class="powered-by">
    Powered by<br>
    <strong>SriLankaHosting.lk © 2025</strong>
  </div>

</div>
@endsection
