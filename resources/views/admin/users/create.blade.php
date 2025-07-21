@extends('admin.layouts.app')

@section('title', 'Add New Admin')

@section('content')
<style>
    .edit-admin-container {
        max-width: 580px;
        margin: 0.5rem auto 2rem auto;
        padding: 2rem 2rem 3rem;
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 8px 20px rgba(0, 0, 0, 0.07);
        border: 1px solid #dee2e6;
        transition: box-shadow 0.3s ease;
    }

    .edit-admin-container:hover {
        box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15), 0 12px 30px rgba(0, 0, 0, 0.1);
    }

    .form-group {
        margin-bottom: 1rem;
        position: relative;
    }

    label {
        font-weight: 600;
        margin-bottom: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.9rem;
    }

    label svg {
        width: 16px;
        height: 16px;
        color: #0d6efd;
    }

    .form-control-sm {
        font-size: 0.9rem;
        padding: 0.375rem 0.5rem;
        padding-right: 2.5rem;
    }

    .password-toggle-icon {
		position: absolute;
		top: 50%;
		right: 12px;
		transform: translateY(-50%);
		cursor: pointer;
		z-index: 10;
		font-size: 1.1rem;
		color: #6c757d;
		pointer-events: auto;
		line-height: 1;
		display: flex;
		align-items: center;
		justify-content: center;
		height: 100%;
	}
	
	.form-control-sm {
		padding-right: 2.5rem;
	}



    #passwordMismatch {
        font-size: 0.8rem;
        color: red;
        display: none;
        margin-top: 4px;
    }

    .password-rules {
        font-size: 0.75rem;
        margin-top: 6px;
        line-height: 1.4;
    }

    .password-rules .valid {
        color: green;
    }

    .password-rules .invalid {
        color: red;
    }
</style>

<div class="edit-admin-container">
    <h2 class="mb-4 d-flex justify-content-center align-items-center gap-2 text-primary fw-bold text-center">
        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-person-plus-fill" viewBox="0 0 16 16" width="24" height="24">
            <path d="M8 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
            <path d="M8 10a5 5 0 0 0-5 5v1h10v-1a5 5 0 0 0-5-5z"/>
            <path fill-rule="evenodd" d="M13 6.5a.5.5 0 0 1 .5.5v1H15a.5.5 0 0 1 0 1h-1.5v1a.5.5 0 0 1-1 0v-1H11a.5.5 0 0 1 0-1h1.5v-1a.5.5 0 0 1 .5-.5z"/>
        </svg>
        Add New Admin
    </h2>

    @if ($errors->any())
        <div class="alert alert-danger py-2">
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show py-2" role="alert" id="autoCloseAlert">
            {{ Session::get('success') }}
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @php $current = auth()->guard('admin')->user(); @endphp

    <form action="{{ route('admin.users.store') }}" method="POST" novalidate>
        @csrf

        {{-- Name --}}
        <div class="form-group">
            <label for="name">
                <svg class="bi bi-person"><path d="..."/></svg> Name
            </label>
            <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Email --}}
        <div class="form-group">
            <label for="email">
                <svg class="bi bi-envelope"><path d="..."/></svg> Email Address
            </label>
            <input type="email" class="form-control form-control-sm @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Password --}}
        <div class="form-group">
            <label for="password">
                <svg class="bi bi-lock"><path d="..."/></svg> Password
            </label>
            <input type="password" class="form-control form-control-sm @error('password') is-invalid @enderror" id="password" name="password" required>
            <i class="bi bi-eye-fill password-toggle-icon" onclick="toggleVisibility('password', this)"></i>
            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror

            <div class="password-rules" id="passwordRules">
                <div id="length" class="invalid">• At least 8 characters</div>
                <div id="uppercase" class="invalid">• One uppercase letter</div>
                <div id="lowercase" class="invalid">• One lowercase letter</div>
                <div id="number" class="invalid">• One number</div>
                <div id="special" class="invalid">• One special character</div>
            </div>
        </div>

        {{-- Confirm Password --}}
        <div class="form-group mb-2">
            <label for="password_confirmation">
                <svg class="bi bi-lock-fill"><path d="..."/></svg> Confirm Password
            </label>
            <input type="password" class="form-control form-control-sm" id="password_confirmation" name="password_confirmation" required>
            <i class="bi bi-eye-fill password-toggle-icon" onclick="toggleVisibility('password_confirmation', this)"></i>
            <div id="passwordMismatch">Passwords do not match</div>
        </div>

        {{-- Role --}}
        @if($current->role === 'super_admin')
        <div class="form-group mb-4">
            <label for="role">
                <svg class="bi bi-shield-lock"><path d="..."/></svg> Role
            </label>
            <select name="role" id="role" class="form-select form-select-sm" required>
                <option value="" disabled selected>Select role</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
            </select>
        </div>
        @endif

        {{-- Submit --}}
        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm px-4">Cancel</a>
            <button type="submit" class="btn btn-primary btn-sm px-4">Create</button>
        </div>
    </form>
</div>

<!-- JS -->
<script>
    setTimeout(() => {
        const alert = document.getElementById('autoCloseAlert');
        if (alert) bootstrap.Alert.getOrCreateInstance(alert).close();
    }, 3000);

    function toggleVisibility(id, icon) {
        const input = document.getElementById(id);
        const type = input.getAttribute('type');
        input.setAttribute('type', type === 'password' ? 'text' : 'password');
        icon.classList.toggle('bi-eye-fill');
        icon.classList.toggle('bi-eye-slash-fill');
    }

    const password = document.getElementById('password');
    const confirm = document.getElementById('password_confirmation');
    const mismatch = document.getElementById('passwordMismatch');

    const rules = {
        length: document.getElementById('length'),
        uppercase: document.getElementById('uppercase'),
        lowercase: document.getElementById('lowercase'),
        number: document.getElementById('number'),
        special: document.getElementById('special'),
    };

    password.addEventListener('input', () => {
        const val = password.value;
        rules.length.className = val.length >= 8 ? 'valid' : 'invalid';
        rules.uppercase.className = /[A-Z]/.test(val) ? 'valid' : 'invalid';
        rules.lowercase.className = /[a-z]/.test(val) ? 'valid' : 'invalid';
        rules.number.className = /\d/.test(val) ? 'valid' : 'invalid';
        rules.special.className = /[^A-Za-z0-9]/.test(val) ? 'valid' : 'invalid';
        mismatch.style.display = confirm.value && confirm.value !== val ? 'block' : 'none';
    });

    confirm.addEventListener('input', () => {
        mismatch.style.display = confirm.value !== password.value ? 'block' : 'none';
    });
</script>
@endsection
