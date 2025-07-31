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

  @if(session('success'))
    <div class="toast-notification toast-success" role="alert">
      <div class="toast-icon">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20 6L9 17l-5-5"/>
        </svg>
      </div>
      <div class="toast-message">{!! session('success') !!}</div>
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
    <table id="smsTemplateTable" class="table table-bordered table-hover align-middle table-striped">
      <thead>
        <tr>
          <th>#</th>
          <th class="text-start">Title</th>
          <th class="text-start">Slug</th>
          <th class="text-start">Content</th>
          <th class="text-start">Created</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($templates as $template)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td class="text-start">{{ $template->name }}</td>
          <td class="text-start">{{ $template->slug }}</td>
          <td class="text-start">{{ Str::limit($template->content, 60) }}</td>
          <td class="text-start">{{ $template->created_at->format('Y-m-d') }}</td>
          <td>
            <button class="btn btn-sm btn-info" onclick="showTemplate({{ json_encode(['title' => $template->name, 'slug' => $template->slug, 'content' => $template->content]) }})">
                <i class="bi bi-eye"> View</i>
            </button>
            <a href="{{ route('admin.sms.template.edit', $template->id) }}" class="btn btn-sm btn-warning">
                <i class="bi bi-pencil-square"> Edit</i>
            </a>
            @if(auth('admin')->user()->role === 'super_admin')
            <form action="{{ route('admin.sms.template.destroy', $template->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                <i class="bi bi-trash"></i>
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

<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="viewModalLabel">SMS Template Preview</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p><strong>Title:</strong> <span id="modalTitle"></span></p>
        <p><strong>Slug:</strong> <span id="modalSlug"></span></p>
        <p><strong>Content:</strong></p>
        <div class="border p-2 bg-light rounded" id="modalContent"></div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  $(document).ready(function () {
    $('#smsTemplateTable').DataTable({
      paging: true,
      lengthChange: true,
      searching: true,
      ordering: true,
      info: true,
      autoWidth: false,
      responsive: true,
      order: [[0, 'desc']],
      columnDefs: [{ orderable: false, targets: 5 }],
      language: {
        search: "_INPUT_",
        searchPlaceholder: "Search templates..."
      }
    });
  });

  function showTemplate(template) {
    document.getElementById('modalTitle').textContent = template.title;
    document.getElementById('modalSlug').textContent = template.slug;
    document.getElementById('modalContent').textContent = template.content;
    new bootstrap.Modal(document.getElementById('viewModal')).show();
  }

  document.addEventListener('DOMContentLoaded', () => {
    const toasts = document.querySelectorAll('.toast-notification');
    toasts.forEach(toast => {
      const timeoutId = setTimeout(() => {
        hideToast(toast);
      }, 4000);
      const closeBtn = toast.querySelector('.toast-close');
      closeBtn.addEventListener('click', () => {
        clearTimeout(timeoutId);
        hideToast(toast);
      });
    });
    function hideToast(toast) {
      toast.style.animation = 'slideOut 0.4s forwards';
      toast.addEventListener('animationend', () => {
        toast.remove();
      });
    }
  });
</script>
@endsection
