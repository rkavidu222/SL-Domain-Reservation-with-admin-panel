@extends('admin.layouts.app')

@section('title', 'Edit Admin')

@section('content')
<style>
    /* Container styles */
    .edit-admin-container {
        max-width: 580px;
        margin: 0.5rem auto 2rem auto;
        padding: 2rem 2rem 3rem;
        background: #fff;
        border-radius: 1rem;
        box-shadow:
            0 4px 6px rgba(0, 0, 0, 0.1),
            0 8px 20px rgba(0, 0, 0, 0.07);
        border: 1px solid #dee2e6;
        transition: box-shadow 0.3s ease;
    }
    .edit-admin-container:hover {
        box-shadow:
            0 8px 12px rgba(0, 0, 0, 0.15),
            0 12px 30px rgba(0, 0, 0, 0.1);
    }
    /* Buttons hover effect */
    .btn-sm.px-4:hover {
        transform: translateY(-2px);
        transition: transform 0.2s ease;
    }
    /* Form group spacing & relative positioning for toggle icons */
    .form-group {
        margin-bottom: 1rem;
        position: relative; /* needed for absolute position toggle icon */
    }
    /* Label and icon alignment */
    label {
        font-weight: 600;
        margin-bottom: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.9rem;
    }
    /* Icon size */
    label svg {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
        color: #0d6efd; /* Bootstrap primary color */
    }
    /* Improve input visibility */
    input.form-control-sm, select.form-select-sm {
        font-size: 0.9rem;
        padding: 0.375rem 0.5rem;
        padding-right: 2.5rem; /* room for toggle icon */
    }
    /* Password toggle icon styling */
    .password-toggle-icon {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        cursor: pointer;
        z-index: 10;
        font-size: 1.1rem;
        color: #6c757d;
        user-select: none;
    }
    /* Password rules styling */
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
    /* Password mismatch message */
    #passwordMismatch {
        font-size: 0.8rem;
        color: red;
        margin-top: 4px;
        display: none;
    }
</style>

<div class="edit-admin-container">
    <h2 class="mb-4 d-flex justify-content-center align-items-center gap-2 text-primary text-center fw-bold">
        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16" width="24" height="24">
            <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
        </svg>
        Edit Admin
    </h2>

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

    <form action="{{ route('admin.users.update', $admin->id) }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16" width="16" height="16">
                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                    <path fill-rule="evenodd" d="M8 9a5 5 0 0 0-5 5v.5A.5.5 0 0 0 3.5 15h9a.5.5 0 0 0 .5-.5V14a5 5 0 0 0-5-5z"/>
                </svg>
                Name
            </label>
            <input
                type="text"
                class="form-control form-control-sm @error('name') is-invalid @enderror"
                id="name"
                name="name"
                placeholder="Full Name"
                value="{{ old('name', $admin->name) }}"
                required
            />
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16" width="16" height="16">
                    <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4z"/>
                    <path d="M.05 4.555L8 9.414l7.95-4.86A1 1 0 0 0 14 4H2a1 1 0 0 0-.95.555z"/>
                </svg>
                Email Address
            </label>
            <input
                type="email"
                class="form-control form-control-sm @error('email') is-invalid @enderror"
                id="email"
                name="email"
                placeholder="Email Address"
                value="{{ old('email', $admin->email) }}"
                required
            />
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-lock" viewBox="0 0 16 16" width="16" height="16">
                    <path d="M8 1a3 3 0 0 0-3 3v3h6V4a3 3 0 0 0-3-3z"/>
                    <path d="M3 8a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/>
                </svg>
                Password <small class="text-muted">(leave blank to keep current)</small>
            </label>
            <input
                type="password"
                class="form-control form-control-sm @error('password') is-invalid @enderror"
                id="password"
                name="password"
                placeholder="Password"
                autocomplete="new-password"
            />
            <i class="bi bi-eye-fill password-toggle-icon" onclick="toggleVisibility('password', this)"></i>

            {{-- Password Validation Rules --}}
            <div class="password-rules" id="passwordRules">
                <div id="length" class="invalid">• At least 8 characters</div>
                <div id="uppercase" class="invalid">• One uppercase letter</div>
                <div id="lowercase" class="invalid">• One lowercase letter</div>
                <div id="number" class="invalid">• One number</div>
                <div id="special" class="invalid">• One special character</div>
            </div>

            @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="password_confirmation">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-lock-fill" viewBox="0 0 16 16" width="16" height="16">
                    <path d="M2.5 9a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v5a1 1 0 0 1-1 1h-9a1 1 0 0 1-1-1v-5z"/>
                    <path d="M11 4a3 3 0 1 0-6 0v3h6V4z"/>
                </svg>
                Confirm Password
            </label>
            <input
                type="password"
                class="form-control form-control-sm"
                id="password_confirmation"
                name="password_confirmation"
                placeholder="Confirm Password"
                autocomplete="new-password"
            />
            <i class="bi bi-eye-fill password-toggle-icon" onclick="toggleVisibility('password_confirmation', this)"></i>

            <div id="passwordMismatch">Passwords do not match</div>
        </div>

        @if($current->role === 'super_admin')
            <div class="form-group mb-4">
                <label for="role">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-shield-lock" viewBox="0 0 16 16" width="16" height="16">
                        <path d="M5.5 9a1.5 1.5 0 1 1 3 0v1h-3V9z"/>
                        <path d="M7.072 1.22a.5.5 0 0 1 .856 0l2.568 4.134 5.908.855a.5.5 0 0 1 .277.852l-4.273 4.32 1.01 5.89a.5.5 0 0 1-.725.527L8 14.347l-5.591 2.436a.5.5 0 0 1-.725-.527l1.01-5.89-4.273-4.32a.5.5 0 0 1 .277-.852l5.908-.855L7.072 1.22z"/>
                    </svg>
                    Role
                </label>
                <select name="role" id="role" class="form-select form-select-sm" required>
                    <option value="admin" {{ $admin->role === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="super_admin" {{ $admin->role === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                </select>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm px-4">
                Cancel
            </a>
            <button type="submit" class="btn btn-primary btn-sm px-4">
                Update
            </button>
        </div>
    </form>
</div>

{{-- Auto close success alert after 3 seconds --}}
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

<script>
    function toggleVisibility(inputId, icon) {
        const input = document.getElementById(inputId);
        const type = input.getAttribute('type');
        if(type === 'password') {
            input.setAttribute('type', 'text');
            icon.classList.remove('bi-eye-fill');
            icon.classList.add('bi-eye-slash-fill');
        } else {
            input.setAttribute('type', 'password');
            icon.classList.remove('bi-eye-slash-fill');
            icon.classList.add('bi-eye-fill');
        }
    }

    // Password validation rules references
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

        // Show mismatch if confirm is non-empty and doesn't match password
        mismatch.style.display = confirm.value && confirm.value !== val ? 'block' : 'none';
    });

    confirm.addEventListener('input', () => {
        mismatch.style.display = confirm.value !== password.value ? 'block' : 'none';
    });
</script>
@endsection
