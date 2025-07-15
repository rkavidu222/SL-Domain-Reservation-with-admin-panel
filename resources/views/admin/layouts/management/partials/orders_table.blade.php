@if($orders->count() > 0)
<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle text-center">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th class="text-start">Customer Name</th>
                <th class="text-start">Domain</th>
                <th>Mobile</th>
                <th>Payment Status</th>
                <th style="min-width: 120px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td class="text-start">{{ $order->first_name }} {{ $order->last_name ?? '' }}</td>
                <td class="text-start">{{ $order->domain_name ?? 'N/A' }}</td>
                <td>{{ $order->mobile ?? '-' }}</td>
                <td>
                    @php
                        $status = strtolower($order->payment_status ?? '');
                    @endphp
                    @if($status === 'paid')
                        <span class="badge bg-success">Paid</span>
                    @elseif($status === 'pending')
                        <span class="badge bg-warning text-dark">Pending</span>
                    @else
                        <span class="badge bg-secondary">-</span>
                    @endif
                </td>
                <td>
                    <button
                      class="btn btn-sm btn-info btn-view-order"
                      type="button"
                      data-order='@json($order)'
                      title="View Order Details"
                    >
                        <i class="bi bi-eye"></i> View
                    </button>
                    <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this order?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" type="submit" title="Delete Order">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="alert alert-info text-center mt-4">
    No orders found.
</div>
@endif
