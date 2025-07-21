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

  .btn-blue {
    background-color: #2563eb;
    color: #fff;
    border: none;
    padding: 0.5rem 1.5rem;
    border-radius: 0.375rem;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  .btn-blue:hover {
    background-color: #1d4ed8;
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
</style>

<!-- Create Template Form -->
<div class="admin-management-container">
  <h2 class="mb-4 text-primary fw-bold">
    <i class="bi bi-envelope-plus"></i> Create SMS Template
  </h2>

  <form>
    <div class="mb-3">
      <label class="form-label">Template Title</label>
      <input type="text" class="form-control" placeholder="e.g., Job Creation Alert" />
    </div>

    <div class="mb-3">
      <label class="form-label">Receiver</label>
      <select class="form-select">
        <option value="">Select Receiver</option>
        <option value="client">Client</option>
        <option value="user">Internal User</option>
        <option value="admin">Admin</option>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">SMS Content</label>
      <textarea class="form-control" rows="5" placeholder="Type SMS body here..."></textarea>
      <small class="text-muted mt-2 d-block">You can use variables like <code>{client_name}</code>, <code>{job_id}</code></small>
    </div>

    <button type="submit" class="btn-blue">Save Template</button>
  </form>
</div>

<!-- Template List Table -->
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
          <th>Receiver</th>
          <th class="text-start">Created</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @php
          $templates = [
              ['id' => 1, 'title' => 'Job Creation Alert', 'receiver' => 'Client', 'content' => 'Hello {client_name}, a job with ID {job_id} was created.', 'created_at' => '2025-07-19'],
              ['id' => 2, 'title' => 'New Admin Notice', 'receiver' => 'Admin', 'content' => 'Admin Alert: A new job {job_id} was posted.', 'created_at' => '2025-07-18'],
              ['id' => 3, 'title' => 'Internal Notification', 'receiver' => 'User', 'content' => 'Dear user, job {job_id} is assigned to you.', 'created_at' => '2025-07-17'],
          ];
        @endphp
        @foreach($templates as $template)
        <tr>
          <td>{{ $template['id'] }}</td>
          <td class="text-start">{{ $template['title'] }}</td>
          <td>{{ $template['receiver'] }}</td>
          <td class="text-start">{{ $template['created_at'] }}</td>
          <td>
            <button class="btn btn-sm btn-info" onclick="showTemplate({{ json_encode($template) }})" title="View">
              <i class="bi bi-eye">View</i>
            </button>
            <button class="btn btn-sm btn-warning" title="Edit">
              <i class="bi bi-pencil-square">Edit</i>
            </button>
            <button class="btn btn-sm btn-danger" title="Delete">
              <i class="bi bi-trash"></i>
            </button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="viewModalLabel">SMS Template Preview</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p><strong>Title:</strong> <span id="modalTitle"></span></p>
        <p><strong>Receiver:</strong> <span id="modalReceiver"></span></p>
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
      columnDefs: [
        { orderable: false, targets: 4 }
      ],
      language: {
        search: "_INPUT_",
        searchPlaceholder: "Search templates..."
      }
    });
  });

  function showTemplate(template) {
    document.getElementById('modalTitle').textContent = template.title;
    document.getElementById('modalReceiver').textContent = template.receiver;
    document.getElementById('modalContent').textContent = template.content;
    new bootstrap.Modal(document.getElementById('viewModal')).show();
  }
</script>
@endsection
