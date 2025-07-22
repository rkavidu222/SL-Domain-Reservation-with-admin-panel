@extends('admin.layouts.app')

@section('title', 'Trashed Orders')

@section('content')

@push('styles')
<link rel="stylesheet" href="{{ asset('admin/css/trashed-orders.css') }}">
@endpush



<div class="admin-management-container">
  <h2 class="mb-4 text-primary fw-bold text-center">
    <i class="bi bi-trash3-fill"></i> Trashed Orders
  </h2>

  @if(session('success') || session('error'))
    <div id="flashMessage" class="flash-message {{ session('success') ? 'success' : 'error' }}">
      <div class="flash-content">
        {!! session('success') ?? session('error') !!}
        <button type="button" class="btn-close btn-close-white ms-3" aria-label="Close" onclick="closeFlash()"></button>
      </div>
    </div>
  @endif

  <ul class="nav nav-tabs mb-3" id="trashedTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">
        All ({{ $all->count() }})
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="paid-tab" data-bs-toggle="tab" data-bs-target="#paid" type="button" role="tab" aria-controls="paid" aria-selected="false">
        Paid ({{ $paid->count() }})
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="false">
        Pending ({{ $pending->count() }})
      </button>
    </li>
  </ul>

  <div class="tab-content" id="trashedTabContent">

    {{-- All --}}
    <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
      @if($all->count())
        <div class="table-responsive">
          <table id="allTable" class="table table-bordered table-hover align-middle table-striped" style="width:100%">
            <thead>
              <tr>
                <th>#</th>
                <th class="text-start">Customer Name</th>
                <th>Mobile</th>
                <th class="text-start">Domain</th>
                <th>Price (Rs.)</th>
                <th>Payment Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($all as $order)
              <tr>
                <td>{{ $order->id }}</td>
                <td class="text-start">{{ $order->first_name }}</td>
                <td>{{ $order->mobile }}</td>
                <td class="text-start">{{ $order->domain_name }}</td>
                <td class="text-end">{{ number_format($order->price, 2) }}</td>
                <td>
                  @if(strtolower($order->payment_status) === 'paid')
                    <span class="badge bg-success">{{ ucfirst($order->payment_status) }}</span>
                  @elseif(strtolower($order->payment_status) === 'pending')
                    <span class="badge bg-warning text-dark">{{ ucfirst($order->payment_status) }}</span>
                  @else
                    <span class="badge bg-secondary">{{ ucfirst($order->payment_status) }}</span>
                  @endif
                </td>
                <td>
                  <form action="{{ route('admin.orders.restore', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Do you want to restore this order?');">
                    @csrf
                    <button class="btn btn-sm btn-success" title="Restore">
                      <i class="bi bi-arrow-counterclockwise"></i> Restore
                    </button>
                  </form>
                  <form action="{{ route('admin.orders.forceDelete', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete permanently?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger ms-1" title="Delete Permanently">
                      <i class="bi bi-x-circle"></i> Delete
                    </button>
                  </form>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div class="alert alert-info">No trashed orders found.</div>
      @endif
    </div>

    {{-- Paid --}}
    <div class="tab-pane fade" id="paid" role="tabpanel" aria-labelledby="paid-tab">
      @if($paid->count())
        <div class="table-responsive">
          <table id="paidTable" class="table table-bordered table-hover align-middle table-striped" style="width:100%">
            <thead>
              <tr>
                <th>#</th>
                <th class="text-start">Customer Name</th>
                <th>Mobile</th>
                <th class="text-start">Domain</th>
                <th>Price (Rs.)</th>
                <th>Payment Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($paid as $order)
              <tr>
                <td>{{ $order->id }}</td>
                <td class="text-start">{{ $order->first_name }}</td>
                <td>{{ $order->mobile }}</td>
                <td class="text-start">{{ $order->domain_name }}</td>
                <td class="text-end">{{ number_format($order->price, 2) }}</td>
                <td><span class="badge bg-success">Paid</span></td>
                <td>
                  <form action="{{ route('admin.orders.restore', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Do you want to restore this order?');">
                    @csrf
                    <button class="btn btn-sm btn-success" title="Restore">
                      <i class="bi bi-arrow-counterclockwise"></i> Restore
                    </button>
                  </form>
                  <form action="{{ route('admin.orders.forceDelete', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete permanently?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger ms-1" title="Delete Permanently">
                      <i class="bi bi-x-circle"></i> Delete
                    </button>
                  </form>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div class="alert alert-info">No paid trashed orders found.</div>
      @endif
    </div>

    {{-- Pending --}}
    <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
      @if($pending->count())
        <div class="table-responsive">
          <table id="pendingTable" class="table table-bordered table-hover align-middle table-striped" style="width:100%">
            <thead>
              <tr>
                <th>#</th>
                <th class="text-start">Customer Name</th>
                <th>Mobile</th>
                <th class="text-start">Domain</th>
                <th>Price (Rs.)</th>
                <th>Payment Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($pending as $order)
              <tr>
                <td>{{ $order->id }}</td>
                <td class="text-start">{{ $order->first_name }}</td>
                <td>{{ $order->mobile }}</td>
                <td class="text-start">{{ $order->domain_name }}</td>
                <td class="text-end">{{ number_format($order->price, 2) }}</td>
                <td><span class="badge bg-warning text-dark">Pending</span></td>
                <td>
                  <form action="{{ route('admin.orders.restore', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Do you want to restore this order?');">
                    @csrf
                    <button class="btn btn-sm btn-success" title="Restore">
                      <i class="bi bi-arrow-counterclockwise"></i> Restore
                    </button>
                  </form>
                  <form action="{{ route('admin.orders.forceDelete', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete permanently?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger ms-1" title="Delete Permanently">
                      <i class="bi bi-x-circle"></i> Delete
                    </button>
                  </form>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div class="alert alert-info">No pending trashed orders found.</div>
      @endif
    </div>

  </div>
</div>
@endsection

@section('scripts')
<script>
  $(document).ready(function () {
    $('#allTable, #paidTable, #pendingTable').DataTable({
      paging: true,
      lengthChange: true,
      searching: true,
      ordering: true,
      info: true,
      autoWidth: false,
      responsive: true,
      order: [[0, 'desc']],
      columnDefs: [
        { orderable: false, targets: 6 }
      ],
      language: {
        search: "_INPUT_",
        searchPlaceholder: "Search orders..."
      }
    });
  });

  // Flash message close function
  function closeFlash() {
    const flash = document.getElementById('flashMessage');
    if (!flash) return;
    flash.style.animation = 'fadeOutSlide 0.5s ease forwards';
    setTimeout(() => flash.remove(), 500);
  }
</script>
@endsection
