@extends('admin.layouts.app')

@section('title', 'Invoice SMS Report')

@push('styles')
<link rel="stylesheet" href="{{ asset('admin/css/order.css') }}">
@endpush

@section('content')
<div class="admin-management-container">
    <h2 class="mb-4 text-primary fw-bold">
        <i class="bi bi-chat-left-text"></i> Invoice SMS Report
    </h2>

    <div class="table-responsive">
        <table id="smsReportTable" class="table table-bordered table-hover align-middle table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th class="text-start">Invoice ID</th>
                    <th class="text-start">Phone</th>
                    <th class="text-start">Message</th>
                    <th class="text-start">Status</th>
                    <th class="text-start">Sent At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $index => $log)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $log->invoice->id ?? 'N/A' }}</td>
                    <td>{{ $log->phone }}</td>
                    <td>{{ $log->message }}</td>
                    <td>
                        @if($log->status === 'Success')
                            <span class="badge bg-success">Success</span>
                        @elseif($log->status === 'Pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @else
                            <span class="badge bg-danger">Failed</span>
                        @endif
                    </td>
                    <td>{{ $log->created_at->format('Y-m-d ') }}</td>
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
    $('#smsReportTable').DataTable({
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
        searchPlaceholder: "Search SMS logs..."
      }
    });
  });
</script>
@endsection
