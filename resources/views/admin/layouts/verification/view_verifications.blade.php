
@extends('admin.layouts.app')

@section('title', 'View Verifications')

@push('styles')
  <link rel="stylesheet" href="{{ asset('admin/css/order.css') }}">
@endpush

@section('content')
<div class="admin-management-container">
  <h2 class="mb-4 text-primary fw-bold text-center">
    <i class="bi bi-file-earmark-check-fill"></i> View Verifications
  </h2>

  {{-- Flash Messages --}}
  @if (session('success'))
    <div class="toast-notification toast-success" role="alert">
      <div class="toast-icon">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M20 6L9 17l-5-5"/>
        </svg>
      </div>
      <div class="toast-message">
        <strong>Success:</strong> {{ session('success') }}
      </div>
      <button class="toast-close" aria-label="Close notification">&times;</button>
    </div>
  @endif

  @if (session('error'))
    <div class="toast-notification toast-error" role="alert">
      <div class="toast-icon">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"/>
          <line x1="12" y1="8" x2="12" y2="12"/>
          <line x1="12" y1="16" x2="12" y2="16"/>
        </svg>
      </div>
      <div class="toast-message">
        <strong>Error:</strong> {{ session('error') }}
      </div>
      <button class="toast-close" aria-label="Close notification">&times;</button>
    </div>
  @endif

  <div class="table-responsive">
    <table id="verificationsTable" class="table table-bordered table-hover table-striped w-100">
      <thead class="bg-primary text-white text-center">
        <tr>
          <th>#</th>
          <th>Order ID</th>
          <th>Reference Number</th>
          <th>Customer Name</th>
          <th>Domain</th>
          <th>Domain Category</th>
          <th>Price (Rs.)</th>
          <th>Payment Slip</th>
          <th>Payment Status</th>
          <th>Uploaded At</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($verifications as $verification)
          <tr>
            <td class="text-center">{{ $verification->id }}</td>
            <td class="text-center">{{ $verification->domain_order_id }}</td>
            <td class="text-center">{{ $verification->reference_number ?? '-' }}</td>
            <td>
              {{ $verification->domainOrder->first_name ?? '-' }}
              {{ $verification->domainOrder->last_name ?? '' }}
            </td>
            <td>{{ $verification->domainOrder->domain_name ?? '-' }}</td>
            <td class="text-center">{{ $verification->domainOrder->category ?? '-' }}</td>
            <td class="text-end">{{ number_format($verification->domainOrder->price ?? 0, 2) }}</td>
            <td class="text-center">
              @if ($verification->receipt_path)
                <button
                  type="button"
                  class="btn btn-sm btn-outline-primary btn-view-slip"
                  data-receipt-path="{{ url('public/' . $verification->receipt_path) }}"
                  data-domain-order="{{ json_encode($verification->domainOrder) }}"
                  data-reference-number="{{ $verification->reference_number ?? '-' }}"
                >
                  View Slip
                </button>
              @else
                <span class="text-muted">No Slip</span>
              @endif
            </td>
            <td class="text-center">
              <form action="{{ route('admin.verification.updateStatus', $verification->id) }}" method="POST" class="d-inline action-form">
                @csrf
                @method('PATCH')
                <select name="payment_status" class="form-select form-select-sm action-status" data-current="{{ $verification->domainOrder->payment_status ?? '' }}">
                  @php
                    $paymentStatuses = ['pending', 'paid', 'awaiting_proof', 'client_acc_created', 'actived'];
                  @endphp
                  @foreach($paymentStatuses as $status)
                    <option value="{{ $status }}" {{ ($verification->domainOrder->payment_status ?? '') === $status ? 'selected' : '' }}>
                      {{ ucfirst($status) }}
                    </option>
                  @endforeach
                </select>
              </form>
            </td>
            <td class="text-center">{{ $verification->created_at->format('Y-m-d') }}</td>
            <td class="text-center">
              <button
                class="btn btn-sm btn-info btn-details"
                data-bs-toggle="modal"
                data-bs-target="#detailsModal"
                data-verification="{{ json_encode($verification) }}"
                data-domain-order="{{ json_encode($verification->domainOrder) }}"
              >Details</button>

              <form action="{{ route('admin.verification.destroy', $verification->id) }}" method="POST" class="d-inline action-form">
                @csrf
                @method('DELETE')
                <button type="button"
                  class="btn btn-sm btn-danger ms-1 action-btn"
                  data-message="Are you sure you want to delete this verification?">
                  Delete
                </button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<!-- Modal for Details -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="detailsModalLabel">Verification Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="modalContent">
          <p><strong>Loading details...</strong></p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal for Viewing Slip -->
<div class="modal fade" id="slipModal" tabindex="-1" aria-labelledby="slipModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="slipModalLabel">Payment Slip & Domain Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body d-flex flex-column flex-md-row gap-4">
        <div id="slipContent" class="flex-fill text-center">
          <p><strong>Loading slip...</strong></p>
        </div>
        <div id="slipDomainDetails" class="flex-fill">
          <p><strong>Loading domain details...</strong></p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center p-4">
      <div class="modal-body">
        <div class="text-warning mb-3" style="font-size: 48px;">
          <i class="bi bi-exclamation-circle-fill"></i>
        </div>
        <h2 class="mb-3" id="modalConfirmTitle">Action Required</h2>
        <p id="modalConfirmMessage">You need to confirm this action.</p>
        <div class="d-flex justify-content-center gap-3 mt-4">
          <button type="button" class="btn btn-danger" id="modalConfirmYes">Yes</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<!-- jQuery (required for DataTables) -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(function () {
  // Initialize DataTable
  $('#verificationsTable').DataTable({
    responsive: true,
    paging: true,
    lengthChange: true,
    searching: true,
    ordering: true,
    info: true,
    autoWidth: false,
  });

  // Toast auto-hide and close
  const toasts = document.querySelectorAll('.toast-notification');
  toasts.forEach(toast => {
    const timeoutId = setTimeout(() => hideToast(toast), 4000);
    const closeBtn = toast.querySelector('.toast-close');
    closeBtn.addEventListener('click', () => {
      clearTimeout(timeoutId);
      hideToast(toast);
    });
  });

  function hideToast(toast) {
    toast.style.animation = 'slideOut 0.4s forwards';
    toast.addEventListener('animationend', () => toast.remove());
  }

  // Confirmation modal logic
  const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
  const modalConfirmMessage = document.getElementById('modalConfirmMessage');
  const confirmBtn = document.getElementById('modalConfirmYes');
  let currentForm = null;
  let currentSelect = null;

  // Handle action buttons (Delete)
  document.querySelectorAll('.action-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      currentForm = this.closest('form');
      currentSelect = null; // Reset select for non-select actions
      modalConfirmMessage.textContent = this.getAttribute('data-message') || 'Confirm this action?';
      confirmationModal.show();
    });
  });

  // Handle payment status change
  $('select[name="payment_status"]').on('change', function(e) {
    const select = $(this);
    currentForm = select.closest('form');
    currentSelect = select;
    const oldStatus = select.data('current') || select.find('option[selected]').val();
    const newStatus = select.val();

    // If no change or same status, do nothing
    if (oldStatus === newStatus) return;

    modalConfirmMessage.textContent = `Are you sure you want to change payment status from "${capitalize(oldStatus)}" to "${capitalize(newStatus)}"?`;
    confirmationModal.show();
  });

  // Handle modal confirmation
  confirmBtn.addEventListener('click', () => {
    if (currentSelect) {
      currentSelect.data('current', currentSelect.val()); // Update current status
    }
    if (currentForm) currentForm.submit();
    confirmationModal.hide();
  });

  // Handle cancel button to revert select
  document.querySelector('.modal .btn-secondary').addEventListener('click', () => {
    if (currentSelect) {
      const oldStatus = currentSelect.data('current') || currentSelect.find('option[selected]').val();
      currentSelect.val(oldStatus); // Revert to previous status
    }
    confirmationModal.hide();
  });

  // Details modal logic
  const detailsModal = document.getElementById('detailsModal');
  const modalContent = document.getElementById('modalContent');

  detailsModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const verification = JSON.parse(button.getAttribute('data-verification'));
    const domainOrder = JSON.parse(button.getAttribute('data-domain-order'));
    const status = verification.status ?? domainOrder.payment_status ?? 'N/A';

    let html = `
      <h6>Verification Info</h6>
      <table class="table table-bordered">
        <tr><th>ID</th><td>${verification.id}</td></tr>
        <tr><th>Status</th><td>${status}</td></tr>
        <tr><th>Receipt Path</th><td>${verification.receipt_path ? '<a href="/public/' + verification.receipt_path + '" target="_blank">View Slip</a>' : 'No Slip'}</td></tr>
        <tr><th>Uploaded At</th><td>${new Date(verification.created_at).toLocaleString()}</td></tr>
      </table>

      <h6>Domain Order Info</h6>
      <table class="table table-bordered">
        <tr><th>Order ID</th><td>${domainOrder.id}</td></tr>
        <tr><th>Reference Number</th><td>${verification.reference_number ?? '-'}</td></tr>
        <tr><th>Customer Name</th><td>${domainOrder.first_name ?? ''} ${domainOrder.last_name ?? ''}</td></tr>
        <tr><th>Domain</th><td>${domainOrder.domain_name ?? '-'}</td></tr>
        <tr><th>Domain Category</th><td>${domainOrder.category ?? '-'}</td></tr>
        <tr><th>Price (Rs.)</th><td>${parseFloat(domainOrder.price).toFixed(2)}</td></tr>
        <tr><th>Payment Status</th><td>${domainOrder.payment_status ?? 'N/A'}</td></tr>
        <tr><th>Created At</th><td>${new Date(domainOrder.created_at).toLocaleString()}</td></tr>
      </table>
    `;

    modalContent.innerHTML = html;
  });

  // Handle "View Slip" button click
  $(document).on('click', '.btn-view-slip', function() {
    const receiptPath = $(this).data('receipt-path');
    const domainOrder = $(this).data('domain-order');
    const referenceNumber = $(this).data('reference-number');
    const extension = receiptPath.split('.').pop().toLowerCase();

    let slipHtml = '';
    if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(extension)) {
      slipHtml = `<img src="${receiptPath}" alt="Payment Slip" class="img-fluid rounded" style="max-height: 500px;">`;
    } else if (extension === 'pdf') {
      slipHtml = `<iframe src="${receiptPath}" style="width:100%; height:500px;" frameborder="0"></iframe>`;
    } else {
      slipHtml = `<p class="text-danger">Unsupported file format.</p>`;
    }

    $('#slipContent').html(slipHtml);

    let domainDetailsHtml = `
      <table class="table table-bordered">
        <tr><th>Order ID</th><td>${domainOrder.id}</td></tr>
        <tr><th>Reference Number</th><td>${referenceNumber}</td></tr>
        <tr><th>Customer Name</th><td>${domainOrder.first_name ?? ''} ${domainOrder.last_name ?? ''}</td></tr>
        <tr><th>Domain</th><td>${domainOrder.domain_name ?? '-'}</td></tr>
        <tr><th>Domain Category</th><td>${domainOrder.category ?? '-'}</td></tr>
        <tr><th>Price (Rs.)</th><td>${parseFloat(domainOrder.price).toFixed(2)}</td></tr>
        <tr><th>Payment Status</th><td>${domainOrder.payment_status ?? 'N/A'}</td></tr>
        <tr><th>Order Created At</th><td>${new Date(domainOrder.created_at).toLocaleString()}</td></tr>
      </table>
    `;

    $('#slipDomainDetails').html(domainDetailsHtml);

    const slipModal = new bootstrap.Modal(document.getElementById('slipModal'));
    slipModal.show();
  });

  // Helper function to capitalize words
  function capitalize(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
  }
});
</script>
@endsection
