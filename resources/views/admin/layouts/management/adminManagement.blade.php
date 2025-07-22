@extends('admin.layouts.app')

@section('title', 'Admin Management')

@push('styles')
  <link rel="stylesheet" href="{{ asset('admin/css/admin-management.css') }}">
@endpush


@section('content')


<div class="admin-management-container">
    <h2 class="mb-4 d-flex justify-content-center align-items-center gap-2 text-primary fw-bold">
        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16" width="24" height="24">
            <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
        </svg>
        All Admins
    </h2>

	{{-- Flash Message Container --}}
	@if(session('success'))
	  <div class="toast-notification toast-success" role="alert" aria-live="polite" aria-atomic="true">
		<div class="toast-icon">
		  <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
			<path d="M20 6L9 17l-5-5"/>
		  </svg>
		</div>
		<div class="toast-message">
		  {!! session('success') !!}
		</div>
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
		  <strong>Error:</strong> {!! session('error') !!}
		</div>
		<button class="toast-close" aria-label="Close notification">&times;</button>
	  </div>
	@endif



    @php $current = auth()->guard('admin')->user(); @endphp
    @if($current->role === 'super_admin')
        <a href="{{ route('admin.users.create') }}" class="btn-add-admin">
            <i class="bi bi-person-plus-fill"></i> Add Admin
        </a>
    @endif

    <div class="table-responsive">
        <table id="adminsTable" class="table table-bordered table-hover align-middle text-center" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th class="text-start">Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th style="min-width: 180px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($admins as $admin)
                    <tr>
                        <td>{{ $admin->id }}</td>
                        <td class="text-start">{{ $admin->name }}</td>
                        <td>{{ $admin->email }}</td>
                        <td>{{ ucfirst($admin->role ?? 'admin') }}</td>
                        <td>
                            @if($admin->is_suspended)
                                <span class="badge bg-danger">Suspended</span>
                            @else
                                <span class="badge bg-success">Active</span>
                            @endif
                        </td>
                        <td>{{ $admin->created_at->format('d M Y') }}</td>
                        <td>
                            @if($current->role === 'super_admin' || $current->id === $admin->id)
                                <a href="{{ route('admin.users.edit', $admin->id) }}" class="btn btn-sm btn-primary mb-1">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                            @endif
                            @if($current->role === 'super_admin' && $current->id !== $admin->id)
                                <form action="{{ route('admin.users.destroy', $admin->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this admin?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger mb-1" type="submit">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                                <form action="{{ route('admin.users.suspend', $admin->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ $admin->is_suspended ? 'Activate this admin?' : 'Suspend this admin?' }}');">
                                    @csrf
                                    @method('PUT')
                                    <button class="btn btn-sm {{ $admin->is_suspended ? 'btn-success' : 'btn-warning' }}" type="submit">
                                        <i class="bi {{ $admin->is_suspended ? 'bi-check-circle' : 'bi-slash-circle' }}"></i>
                                        {{ $admin->is_suspended ? 'Activate' : 'Suspend' }}
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
@endsection

@section('scripts')
<script>
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

  $(document).ready(function() {
    $('#adminsTable').DataTable({
      paging: true,
      lengthChange: true,
      searching: true,
      ordering: true,
      info: true,
      autoWidth: false,
      responsive: true,
    });
  });
</script>
@endsection
