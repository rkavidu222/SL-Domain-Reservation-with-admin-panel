@extends('admin.layouts.app')

@section('title', 'Invoice Details')

@push('styles')
<link rel="stylesheet" href="{{ asset('admin/css/invoiceShow.css') }}">
<style>
  /* Optional: add CSS classes for statuses instead of inline styles */
</style>
@endpush

@section('content')
  <div class="invoice-container">

    <div class="header-title">
      <img src="https://www.srilankahosting.lk/logo.svg" alt="SriLankaHosting Logo" /><br>
      <span>SriLanka Hosting Invoice</span>
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
      <span class="section-label">Duration:</span>
      <span class="section-value">
        {{ optional($order->created_at)->format('Y-F') ?? 'N/A' }}
        to
        {{ optional($order->created_at)->addMonths(12)->subDay()->format('Y-F') ?? 'N/A' }}
      </span>
    </div>

    <div class="info-row">
      <span class="section-label">Billing Date :</span>
      <span class="section-value">{{ optional($order->created_at)->format('Y-m-d') ?? 'N/A' }}</span>
    </div>

    @php
      // Trim to remove any accidental spaces, and lowercase for consistent matching
      $status = strtolower(trim($order->payment_status ?? ''));

      $statusLabels = [
        'paid' => ['label' => 'PAID', 'color' => 'green'],
        'pending' => ['label' => 'PENDING', 'color' => 'orange'],
        'awaiting_proof' => ['label' => 'AWAITING PROOF', 'color' => 'blue'],
        'client_acc_created' => ['label' => 'CLIENT ACC CREATED', 'color' => 'purple'],
        'actived' => ['label' => 'ACTIVED', 'color' => 'darkgreen'],
      ];
    @endphp

    <div class="info-row">
      <span class="section-label">Payment Status :</span>
      <span class="section-value">
        @if(array_key_exists($status, $statusLabels))
          <span style="color: {{ $statusLabels[$status]['color'] }}; font-weight: 700;">
            {{ $statusLabels[$status]['label'] }}
          </span>
        @else
          <span style="color: grey; font-weight: 700;">UNKNOWN</span>
        @endif
      </span>
    </div>

    <div class="info-row amount-due">
      Total Amount : Rs. {{ number_format($order->price, 2) }}
    </div>

    @if($status === 'pending')
      <div class="info-row">
        <span class="section-label">Outstanding :</span>
        <span class="outstanding">Rs. {{ number_format($order->price, 2) }}</span>
      </div>
    @endif

    <hr>

  <div class="powered-by">
	  Developed by<br>
	  <strong>SriLanka Hosting Team © {{ date('Y') }}</strong>
	</div>


  </div>

  <div class="back-button-container">
    <a href="{{ url()->previous() }}" class="back-button">&larr; Back</a>
  </div>
@endsection
