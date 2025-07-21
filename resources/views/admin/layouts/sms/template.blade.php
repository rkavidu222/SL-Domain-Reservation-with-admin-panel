@extends('admin.layouts.app')

@section('title', 'Create SMS Template')

@section('content')
<style>
  .admin-management-container {
    max-width: 1400px;
    margin: 1rem auto 3rem auto;
    padding: 2rem;
    background: #fff;
    border-radius: 1rem;
    border: 1px solid #dee2e6;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 8px 20px rgba(0, 0, 0, 0.07);
    transition: box-shadow 0.3s ease;
  }

  .admin-management-container:hover {
    box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15), 0 12px 30px rgba(0, 0, 0, 0.1);
  }

  .form-label {
    font-weight: 600;
    letter-spacing: 0.02em;
  }

  .form-control:focus, .form-select:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
  }

  .btn-primary {
    background-color: #2563eb;
    color: #fff;
    border: none;
    padding: 0.5rem 1.5rem;
    border-radius: 0.375rem;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  .btn-primary:hover {
    background-color: #1d4ed8;
  }

  .table-responsive {
    overflow-x: auto;
  }

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
    max-width: 250px;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  table.table tbody tr:hover {
    background-color: #e0e7ff;
  }

  .btn-sm {
    font-size: 0.8rem;
    padding: 0.3rem 0.6rem;
    border-radius: 0.35rem;
  }

  @media (max-width: 768px) {
    .admin-management-container {
      padding: 1rem;
    }
    table.table thead {
      font-size: 0.85rem;
    }
    table.table td, table.table th {
      font-size: 0.85rem;
      padding: 0.5rem;
    }
  }

  .toast-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    min-width: 320px;
    max-width: 400px;
    padding: 1rem 1.25rem;
    border-radius: 8px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.15);
    color: #fff;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    opacity: 0;
    transform: translateX(120%);
    animation: slideIn 0.4s forwards;
    z-index: 1080;
    user-select: none;
  }
  .toast-success { background-color: #28a745; }
  .toast-error { background-color: #dc3545; }
  .toast-icon svg { stroke: #fff; flex-shrink: 0; }
  .toast-message { flex: 1; line-height: 1.3; }
  .toast-close {
    background: transparent;
    border: none;
    color: rgba(255,255,255,0.8);
    font-size: 1.25rem;
    font-weight: bold;
    cursor: pointer;
    padding: 0;
    line-height: 1;
    transition: color 0.2s ease;
    flex-shrink: 0;
  }
  .toast-close:hover { color: #fff; }
  @keyframes slideIn { to { opacity: 1; transform: translateX(0); } }
  @keyframes slideOut { to { opacity: 0; transform: translateX(120%); } }
</style>

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
            <form action="{{ route('admin.sms.template.destroy', $template->id) }}" method="POST" class="d-inline">
              @csrf
              @method('DELETE')
              <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                <i class="bi bi-trash"></i>
              </button>
            </form>
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
