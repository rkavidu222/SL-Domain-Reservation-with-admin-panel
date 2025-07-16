@extends('admin.layouts.app')

@section('title', 'Order Management')

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

  #ordersTab .nav-link {
    color: #1e40af;
    background-color: #dbeafe;
    border: 1px solid transparent;
    transition: background-color 0.3s ease, color 0.3s ease;
  }

  #ordersTab .nav-link:hover {
    background-color: #bfdbfe;
    color: #1e3a8a;
  }

  #ordersTab .nav-link.active {
    color: #ffffff !important;
    background-color: #2563eb !important;
    border-color: #2563eb !important;
    font-weight: 600;
  }
</style>

<div class="admin-management-container">
  <h2 class="mb-4 text-primary fw-bold text-center">Order Management</h2>

  <ul class="nav nav-tabs mb-4" id="ordersTab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">
        All Orders ({{ $orders->total() }})
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="paid-tab" data-bs-toggle="tab" data-bs-target="#paid" type="button" role="tab" aria-controls="paid" aria-selected="false">
        Paid Orders ({{ $paidOrders->total() }})
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="false">
        Pending Orders ({{ $pendingOrders->total() }})
      </button>
    </li>
  </ul>

  <div class="tab-content" id="ordersTabContent">

    {{-- All Orders --}}
    <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
      <div class="table-responsive">
        <table id="ordersTable" class="table table-bordered table-hover table-striped" style="width:100%">
          <thead class="bg-primary text-white text-center align-middle">
            <tr>
              <th>#</th>
              <th>Customer Name</th>
              <th>Mobile</th>
              <th>Domain</th>
              <th>Category</th>
              <th>Price (Rs.)</th>
              <th>Payment</th>
              <th>Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($orders as $order)
            <tr>
              <td class="text-center">{{ $order->id }}</td>
              <td>{{ $order->first_name }}</td>
              <td>{{ $order->mobile }}</td>
              <td>{{ $order->domain_name }}</td>
              <td>{{ $order->category ?? '-' }}</td>
              <td class="text-end">{{ number_format($order->price, 2) }}</td>
              <td class="text-center">
                <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }}">
                  {{ ucfirst($order->payment_status) }}
                </span>
              </td>
              <td class="text-center">{{ $order->created_at->format('Y-m-d') }}</td>
              <td class="text-center">
                <button class="btn btn-sm btn-info btn-view-order" data-order='@json($order)'>View</button>
                <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure to delete this order?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger ms-1">Delete</button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    {{-- Paid Orders --}}
    <div class="tab-pane fade" id="paid" role="tabpanel" aria-labelledby="paid-tab">
      <div class="table-responsive">
        <table id="paidOrdersTable" class="table table-bordered table-hover table-striped" style="width:100%">
          <thead class="bg-primary text-white text-center align-middle">
            <tr>
              <th>#</th>
              <th>Customer Name</th>
              <th>Mobile</th>
              <th>Domain</th>
              <th>Category</th>
              <th>Price (Rs.)</th>
              <th>Payment</th>
              <th>Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($paidOrders as $order)
            <tr>
              <td class="text-center">{{ $order->id }}</td>
              <td>{{ $order->first_name }}</td>
              <td>{{ $order->mobile }}</td>
              <td>{{ $order->domain_name }}</td>
              <td>{{ $order->category ?? '-' }}</td>
              <td class="text-end">{{ number_format($order->price, 2) }}</td>
              <td class="text-center">
                <span class="badge bg-success">Paid</span>
              </td>
              <td class="text-center">{{ $order->created_at->format('Y-m-d') }}</td>
              <td class="text-center">
                <button class="btn btn-sm btn-info btn-view-order" data-order='@json($order)'>View</button>
                <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure to delete this order?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger ms-1">Delete</button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    {{-- Pending Orders --}}
    <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
      <div class="table-responsive">
        <table id="pendingOrdersTable" class="table table-bordered table-hover table-striped" style="width:100%">
          <thead class="bg-primary text-white text-center align-middle">
            <tr>
              <th>#</th>
              <th>Customer Name</th>
              <th>Mobile</th>
              <th>Domain</th>
              <th>Category</th>
              <th>Price (Rs.)</th>
              <th>Payment</th>
              <th>Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($pendingOrders as $order)
            <tr>
              <td class="text-center">{{ $order->id }}</td>
              <td>{{ $order->first_name }}</td>
              <td>{{ $order->mobile }}</td>
              <td>{{ $order->domain_name }}</td>
              <td>{{ $order->category ?? '-' }}</td>
              <td class="text-end">{{ number_format($order->price, 2) }}</td>
              <td class="text-center">
                <span class="badge bg-warning">Pending</span>
              </td>
              <td class="text-center">{{ $order->created_at->format('Y-m-d') }}</td>
              <td class="text-center">
                <button class="btn btn-sm btn-info btn-view-order" data-order='@json($order)'>View</button>
                <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure to delete this order?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger ms-1">Delete</button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

{{-- Order Details Modal --}}
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <dl class="row mb-0">
          <dt class="col-sm-4">Order ID</dt><dd class="col-sm-8" id="modal-order-id"></dd>
          <dt class="col-sm-4">Customer Name</dt><dd class="col-sm-8" id="modal-customer-name"></dd>
          <dt class="col-sm-4">Email</dt><dd class="col-sm-8" id="modal-email"></dd>
          <dt class="col-sm-4">Phone</dt><dd class="col-sm-8" id="modal-phone"></dd>
          <dt class="col-sm-4">Domain</dt><dd class="col-sm-8" id="modal-domain"></dd>
          <dt class="col-sm-4">Category</dt><dd class="col-sm-8" id="modal-category"></dd>
          <dt class="col-sm-4">Price</dt><dd class="col-sm-8" id="modal-price"></dd>
          <dt class="col-sm-4">Order Date</dt><dd class="col-sm-8" id="modal-order-date"></dd>
          <dt class="col-sm-4">Payment Status</dt><dd class="col-sm-8" id="modal-payment-status"></dd>
        </dl>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  $(function () {
    // Initialize all DataTables
    $('#ordersTable, #paidOrdersTable, #pendingOrdersTable').DataTable({
      paging: true,
      lengthChange: true,
      searching: true,
      ordering: true,
      info: true,
      autoWidth: false,
      responsive: true,
    });

    // Bootstrap modal instance
    const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));

    // Use delegated event handler to handle clicks on dynamically created .btn-view-order buttons
    $(document).on('click', '.btn-view-order', function () {
      const order = $(this).data('order');
      $('#modal-order-id').text(order.id ?? '-');
      $('#modal-customer-name').text(`${order.first_name ?? ''} ${order.last_name ?? ''}`.trim() || '-');
      $('#modal-email').text(order.email ?? '-');
      $('#modal-phone').text(order.mobile ?? '-');
      $('#modal-domain').text(order.domain_name ?? '-');
      $('#modal-category').text(order.category ?? '-');
      $('#modal-price').text(order.price !== undefined ? 'Rs.' + parseFloat(order.price).toFixed(2) : '-');
      $('#modal-order-date').text(order.created_at ? new Date(order.created_at).toLocaleDateString() : '-');
      $('#modal-payment-status').text(order.payment_status ? order.payment_status.charAt(0).toUpperCase() + order.payment_status.slice(1) : '-');
      modal.show();
    });

  });
</script>
@endsection
