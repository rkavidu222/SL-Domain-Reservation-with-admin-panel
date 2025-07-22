@extends('admin.layouts.app')

@section('title', 'Edit Admin')

@push('styles')
  <link rel="stylesheet" href="{{ asset('admin/css/createAdmin.css') }}">
@endpush


@section('content')



<div class="edit-admin-container">
    <h4 class="text-center text-primary fw-bold mb-4">
        <i class="bi bi-person-fill"></i> Edit Admin
    </h4>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger py-2">
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Success Message --}}
    @if(Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show py-2" role="alert" id="autoCloseAlert">
            {{ Session::get('success') }}
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @php $current = auth()->guard('admin')->user(); @endphp

    <form action="{{ route('admin.users.update', $admin->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label fw-semibold">
                <i class="bi bi-person"></i> Name
            </label>
            <input type="text" name="name" id="name"
                   class="form-control form-control-sm @error('name') is-invalid @enderror"
                   value="{{ old('name', $admin->name) }}" placeholder="Full Name" required>
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">
                <i class="bi bi-envelope"></i> Email Address
            </label>
            <input type="email" name="email" id="email"
                   class="form-control form-control-sm @error('email') is-invalid @enderror"
                   value="{{ old('email', $admin->email) }}" placeholder="Email Address" required>
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">
                <i class="bi bi-lock"></i> Password <small class="text-muted">(leave blank to keep current)</small>
            </label>
            <div class="input-group input-group-sm">
                <input type="password" id="password" name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="Password" autocomplete="new-password">
                <button class="btn btn-outline-secondary" type="button"
                        onclick="toggleVisibility('password', this)">
                    <i class="bi bi-eye-fill"></i>
                </button>
            </div>
            @error('password')
            <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror

            <div class="password-rules" id="passwordRules">
                <div id="length" class="invalid">• At least 8 characters</div>
                <div id="uppercase" class="invalid">• One uppercase letter</div>
                <div id="lowercase" class="invalid">• One lowercase letter</div>
                <div id="number" class="invalid">• One number</div>
                <div id="special" class="invalid">• One special character</div>
            </div>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label fw-semibold">
                <i class="bi bi-lock-fill"></i> Confirm Password
            </label>
            <div class="input-group input-group-sm">
                <input type="password" id="password_confirmation" name="password_confirmation"
                       class="form-control" placeholder="Confirm Password" autocomplete="new-password">
                <button class="btn btn-outline-secondary" type="button"
                        onclick="toggleVisibility('password_confirmation', this)">
                    <i class="bi bi-eye-fill"></i>
                </button>
            </div>
            <div id="passwordMismatch">Passwords do not match</div>
        </div>

        @if($current->role === 'super_admin')
            <div class="mb-4">
                <label for="role" class="form-label fw-semibold">
                    <i class="bi bi-shield-lock"></i> Role
                </label>
                <select name="role" id="role" class="form-select form-select-sm" required>
                    <option value="admin" {{ $admin->role === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="super_admin" {{ $admin->role === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                </select>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mt-3">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm px-4">Cancel</a>
            <button type="submit" class="btn btn-primary btn-sm px-4">Update</button>
        </div>
    </form>
</div>

{{-- Auto-close alert --}}
@if(Session::has('success'))
<script>
    setTimeout(() => {
        const alert = document.getElementById('autoCloseAlert');
        if (alert) {
            bootstrap.Alert.getOrCreateInstance(alert).close();
        }
    }, 3000);
</script>
@endif

{{-- Password visibility & validation --}}
<script>
    function toggleVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        icon.classList.toggle('bi-eye-fill', !isPassword);
        icon.classList.toggle('bi-eye-slash-fill', isPassword);
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
