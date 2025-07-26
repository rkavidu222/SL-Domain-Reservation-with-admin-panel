@extends('layouts.app')

@section('title', 'Invoice for ' . $order->domain_name)

@section('content')

@push('styles')
  <link href="{{ asset('/resources/css/viewInvoice.css') }}" rel="stylesheet">
@endpush

<div class="invoice-container">

  <div class="header-title">
    <img src="https://www.srilankahosting.lk/logo.svg" alt="SriLankaHosting Logo" />
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


@endsection
