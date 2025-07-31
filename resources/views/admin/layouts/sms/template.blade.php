@extends('admin.layouts.app')

@section('title', 'Create SMS Template')

@push('styles')
  <link rel="stylesheet" href="{{ asset('admin/css/order.css') }}">
@endpush

@section('content')
<div class="admin-management-container">
  <h2 class="mb-4 text-primary fw-bold">
    <i class="bi bi-envelope-plus"></i> Create SMS Template
  </h2>

  {{-- Flash Messages --}}
  @if(session('success'))
    <div class="toast-notification toast-success" role="alert" aria-live="polite" aria-atomic="true">
      <div class="toast-icon">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20 6L9 17l-5-5"/>
        </svg>
      </div>
      <div class="toast-message">{!! session('success') !!}</div>
      <button class="toast-close" aria-label="Close notification">&times;</button>
    </div>
  @endif

  @if(session('error'))
    <div class="toast-notification toast-error" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-icon">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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

  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.sms.template.store') }}" method="POST">
    @csrf
    <div class="mb-3">
      <label for="name" class="form-label">Template Name</label>
      <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
    </div>
    <div class="mb-3">
      <label for="slug" class="form-label">Slug (unique identifier)</label>
      <input type="text" id="slug" name="slug" class="form-control" value="{{ old('slug') }}" required>
      <small class="form-text text-muted">Slug should be unique and contain no spaces.</small>
    </div>
    <div class="mb-3">
      <label for="content" class="form-label">Message Content</label>
      <textarea id="content" name="content" class="form-control" rows="5" required>{{ old('content') }}</textarea>
      <small class="form-text text-muted">You can use variables like <code>{name}</code>, <code>{phone}</code>, etc.</small>
    </div>
    <div class="text-end">
      <button type="submit" class="btn btn-primary">Save Template</button>
    </div>
  </form>
</div>

<div class="admin-management-container">
  <h4 class="mb-3 fw-bold text-dark">
    <i class="bi bi-table"></i> SMS Templates
  </h4>

  <div class="table-responsive">
    <table id="smsTemplateTable" class="table table-bordered table-hover table-striped" style="width:100%">
      <thead class="bg-primary text-white text-center align-middle">
        <tr>
          <th>#</th>
          <th>Title</th>
          <th>Slug</th>
          <th>Content</th>
          <th>Created</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($templates as $template)
        <tr>
          <td class="text-center">{{ $loop->iteration }}</td>
          <td>{{ $template->name }}</td>
          <td>{{ $template->slug }}</td>
          <td>{{ Str::limit($template->content, 60) }}</td>
          <td class="text-center">{{ $template->created_at->format('Y-m-d') }}</td>
          <td class="text-center">
            <button class="btn btn-sm btn-info btn-view-template" data-template='@json(["title" => $template->name, "slug" => $template->slug, "content" => $template->content])'>
              <i class="bi bi-eye"></i> View
            </button>
            <a href="{{ route('admin.sms.template.edit', $template->id) }}" class="btn btn-sm btn-warning ms-1">
              <i class="bi bi-pencil-square"></i> Edit
            </a>
            @if(auth('admin')->user()->role === 'super_admin')
            <form action="{{ route('admin.sms.template.destroy', $template->id) }}" method="POST" class="d-inline action-form">
              @csrf
              @method('DELETE')
              <button type="button" class="btn btn-sm btn-danger ms-1 action-btn" data-message="Are you sure you want to delete template: {{ $template->name }}?">
                <i class="bi bi-trash"></i> Delete
              </button>
            </form>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

{{-- View Modal --}}
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="viewModalLabel">SMS Template Preview</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><strong>Title:</strong> <span id="modalTitle"></span></p>
        <p><strong>Slug:</strong> <span id="modalSlug"></span></p>
        <p><strong>Content:</strong></p>
        <div class="border p-2 bg-light rounded" id="modalContent"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

{{-- Confirmation Modal --}}
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
<script>
  $(function () {
    // Initialize DataTable
    $('#smsTemplateTable').DataTable({
      responsive: true,
      paging: true,
      lengthChange: true,
      searching: true,
      ordering: true,
      info: true,
      autoWidth: false,
      order: [[0, 'desc']],
      columnDefs: [
        { orderable: false, targets: 5 }, // Disable sorting on Actions column
        { className: 'text-center', targets: [0, 4, 5] } // Center-align #, Created, Actions
      ],
      language: {
        search: "_INPUT_",
        searchPlaceholder: "Search templates..."
      }
    });

    // View template modal
    $(document).on('click', '.btn-view-template', function () {
      const template = $(this).data('template');
      $('#modalTitle').text(template.title ?? '-');
      $('#modalSlug').text(template.slug ?? '-');
      $('#modalContent').text(template.content ?? '-');
      new bootstrap.Modal(document.getElementById('viewModal')).show();
    });

    // Session timeout warning
    const sessionLifetime = {{ config('session.lifetime') }} * 60 * 1000; // Convert minutes to milliseconds
    setTimeout(() => {
      alert('Your session is about to expire. You will be redirected to the login page.');
      window.location.href = '{{ route('admin.login') }}'; // Redirect to lkadminslh/login
    }, sessionLifetime - 30000); // Warn 30 seconds before expiration

    // Toast notification handling
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

    // Confirmation Modal logic
    const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
    const modalMessage = document.getElementById('modalConfirmMessage');
    const confirmBtn = document.getElementById('modalConfirmYes');
    let currentForm = null;

    document.querySelectorAll('.action-btn').forEach(btn => {
      btn.addEventListener('click', function () {
        currentForm = this.closest('form');
        modalMessage.textContent = this.getAttribute('data-message') || 'Confirm this action?';
        modal.show();
      });
    });

    confirmBtn.addEventListener('click', () => {
      if (currentForm) currentForm.submit();
    });
  });
</script>
@endsection
