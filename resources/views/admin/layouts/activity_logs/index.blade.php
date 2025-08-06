@extends('admin.layouts.app')

@section('title', 'Activity Logs')

@push('styles')
<link rel="stylesheet" href="{{ asset('admin/css/order.css') }}">
@endpush

@section('content')
<div class="admin-management-container">
    <h2 class="mb-4 text-primary fw-bold">
        <i class="bi bi-clock-history"></i> Activity Logs
    </h2>

    {{-- Flash Message --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table id="activityLogsTable" class="table table-bordered table-hover align-middle table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th class="text-start">User</th>
                    <th class="text-start">Task</th>
                    <th class="text-start">Logged At</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($logs as $index => $log)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        @if ($log->admin)
                            {{ $log->admin->name }}<br>
                            <small class="text-muted">{{ $log->admin->email }}</small>
                        @else
                            @php
                                // Extract email from the task string if present
                                preg_match('/email=([\w\.\-\+@]+)/', $log->task, $matches);
                                $email = $matches[1] ?? null;
                            @endphp
                            @if ($email)
                                <em class="text-muted">Deleted User</em><br>
                                <small class="text-muted">{{ $email }}</small>
                            @else
                                <em class="text-muted">System / Deleted User</em>
                            @endif
                        @endif
                    </td>
                    <td>{{ $log->task }}</td>
                    <td>{{ $log->created_at->format('Y-m-d h:i A') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination (optional) --}}
    {{--
    <div class="mt-3">
        {{ $logs->links('pagination::bootstrap-4') }}
    </div>
    --}}
</div>
@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script>
  $(document).ready(function () {
    $('#activityLogsTable').DataTable({
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
        searchPlaceholder: "Search activity logs..."
      }
    });
  });
</script>
@endsection
