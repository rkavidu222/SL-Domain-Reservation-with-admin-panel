@extends('admin.layouts.app')

@section('title', 'Invoice Report')

@push('styles')
<link rel="stylesheet" href="{{ asset('admin/css/order.css') }}">
@endpush

@section('content')
<div class="admin-management-container">
    <h2 class="mb-4 text-primary fw-bold">
        <i class="bi bi-receipt"></i> Invoice Report
    </h2>

    <div class="table-responsive">
        <table id="invoiceReportTable" class="table table-bordered table-hover align-middle table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th class="text-start">Invoice ID</th>
                    <th class="text-start">Customer Name</th>
                    <th class="text-start">Email</th>
                    <th class="text-start">Amount</th>
                    <th class="text-start">Status</th>
                    <th class="text-start">Created At</th>
                </tr>
            </thead>
            <tbody>
                @foreach(range(1, 10) as $i)
                <tr>
                    <td>{{ $i }}</td>
                    <td>#INV{{ 1000 + $i }}</td>
                    <td>Customer {{ $i }}</td>
                    <td>customer{{ $i }}@example.com</td>
                    <td>${{ number_format(1000 + $i * 50, 2) }}</td>
                    <td>
                        @php
                            $statuses = ['Paid', 'Pending', 'Failed'];
                            $status = $statuses[array_rand($statuses)];
                        @endphp
                        @if($status === 'Paid')
                            <span class="badge bg-success">Paid</span>
                        @elseif($status === 'Pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @else
                            <span class="badge bg-danger">Failed</span>
                        @endif
                    </td>
                    <td>{{ now()->subDays($i)->format('Y-m-d h:i A') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
  $(document).ready(function () {
    $('#invoiceReportTable').DataTable({
      paging: true,
      lengthChange: true,
      searching: true,
      ordering: true,
      info: true,
      autoWidth: false,
      responsive: true,
      order: [[0, 'desc']],
      language: {
        search: "_INPUT_",
        searchPlaceholder: "Search invoices..."
      }
    });
  });
</script>
@endsection
