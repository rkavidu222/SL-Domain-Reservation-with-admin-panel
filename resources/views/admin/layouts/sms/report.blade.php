@extends('admin.layouts.app')

@section('title', 'SMS Report')

@section('content')
<div class="container mt-4">
    <div class="card shadow rounded-4 border-0">
        <div class="card-header bg-purple text-white rounded-top-4">
            <h4 class="mb-0">SMS Report</h4>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Receiver</th>
                            <th>Phone</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Sent At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Sample row -->
                        <tr>
                            <td>1</td>
                            <td>John Doe</td>
                            <td>+94 77 123 4567</td>
                            <td>Your job has been created. Ref: 1223</td>
                            <td><span class="badge bg-success">Delivered</span></td>
                            <td>2025-07-19 10:30 AM</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Jane Smith</td>
                            <td>+94 77 555 9876</td>
                            <td>Domain reminder: abc.lk will expire soon.</td>
                            <td><span class="badge bg-warning text-dark">Pending</span></td>
                            <td>2025-07-19 10:35 AM</td>
                        </tr>
                        <!-- End sample -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
