@extends('admin.layouts.app')

@section('title', 'Admin Management')

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


    .btn-add-admin {
        background-color: #28a745;
        color: white !important;
        font-weight: 600;
        padding: 0.375rem 1rem;
        border-radius: 0.375rem;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.9rem;
        user-select: none;
        transition: background-color 0.2s ease;
        text-decoration: none;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .btn-add-admin i {
        font-size: 1rem;
    }
    .btn-add-admin:hover,
    .btn-add-admin:focus {
        background-color: #218838;
        color: white !important;
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        outline: none;
        text-decoration: none;
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

	.toast-success {
	  background-color: #28a745;
	}

	.toast-error {
	  background-color: #dc3545;
	}

	.toast-icon svg {
	  stroke: #fff;
	  flex-shrink: 0;
	}

	.toast-message {
	  flex: 1;
	  line-height: 1.3;
	}

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

	.toast-close:hover {
	  color: #fff;
	}


	@keyframes slideIn {
	  to {
		opacity: 1;
		transform: translateX(0);
	  }
	}


	@keyframes slideOut {
	  to {
		opacity: 0;
		transform: translateX(120%);
	  }
	}



</style>

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
