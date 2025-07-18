@extends('admin.layouts.app')

@section('title', 'Trashed Orders')

@section('content')
<style>
  .admin-management-container {
    max-width: 1400px;
    margin: 1rem auto 3rem auto;
    padding: 2rem;
    background: #fff;
    border-radius: 1rem;
    border: 1px solid #dee2e6;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1), 0 8px 20px rgba(0,0,0,0.07);
    transition: box-shadow 0.3s ease;
  }

  .admin-management-container:hover {
    box-shadow: 0 8px 12px rgba(0,0,0,0.15), 0 12px 30px rgba(0,0,0,0.1);
  }

  /* Nav tabs styling */
  #trashedTabs .nav-link {
    color: #1e40af;
    background-color: #dbeafe;
    border: 1px solid transparent;
    transition: background-color 0.3s ease, color 0.3s ease;
    font-weight: 500;
  }

  #trashedTabs .nav-link:hover {
    background-color: #bfdbfe;
    color: #1e3a8a;
  }

  #trashedTabs .nav-link.active {
    color: #fff !important;
    background-color: #2563eb !important;
    border-color: #2563eb !important;
    font-weight: 600;
  }

  /* Table styles */
  table.table thead {
    background-color: #2563eb;
    color: white;
    text-align: center;
    vertical-align: middle;
  }

  table.table th, table.table td {
    padding: 0.75rem 1rem;
    border: 1px solid #dee2e6;
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
  }

  table.table th.text-start,
  table.table td.text-start {
    text-align: left;
    max-width: 150px;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  table.table tbody tr:hover {
    background-color: #e0e7ff;
  }

  /* Buttons */
  .btn-sm {
    font-size: 0.8rem;
    padding: 0.3rem 0.6rem;
    border-radius: 0.35rem;
  }


	#flashMessage {
    position: fixed;
    top: 1rem;
    right: 1rem;
    z-index: 11000;
    min-width: 320px;
    max-width: 400px;
    border-radius: 0.375rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    color: #fff;
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    font-weight: 600;
    animation: fadeInSlide 0.5s ease forwards;
  }

  #flashMessage.success {
    background-color: #28a745; /* Bootstrap success green */
  }

  #flashMessage.error {
    background-color: #dc3545; /* Bootstrap danger red */
  }

  #flashMessage .btn-close {
    opacity: 0.8;
    transition: opacity 0.3s ease;
  }
  #flashMessage .btn-close:hover {
    opacity: 1;
  }

  @keyframes fadeInSlide {
    0% {
      opacity: 0;
      transform: translateX(100%);
    }
    100% {
      opacity: 1;
      transform: translateX(0);
    }
  }
  @keyframes fadeOutSlide {
    0% {
      opacity: 1;
      transform: translateX(0);
    }
    100% {
      opacity: 0;
      transform: translateX(100%);
    }
  }

</style>

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
                  <form action="{{ route('admin.orders.restore', $order->id) }}" method="POST" class="d-inline">
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
                  <form action="{{ route('admin.orders.restore', $order->id) }}" method="POST" class="d-inline">
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
                  <form action="{{ route('admin.orders.restore', $order->id) }}" method="POST" class="d-inline">
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



</script>
@endsection
