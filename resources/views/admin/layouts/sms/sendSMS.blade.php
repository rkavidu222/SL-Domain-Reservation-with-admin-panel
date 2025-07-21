@extends('admin.layouts.app')

@section('title', 'Send SMS')

@section('content')
<style>
    .quick-sms-container {
        max-width: 1000px;
        margin: 1rem auto;
        background: #fff;
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .quick-sms-header {
        font-size: 1.5rem;
        font-weight: 600;
        color: #0d6efd;
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 500;
    }

    .template-preview {
        font-family: monospace;
        background: #f8f9fa;
        border: 1px solid #ced4da;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        white-space: pre-wrap;
    }

    @media (max-width: 768px) {
        .quick-sms-container {
            padding: 1.5rem;
        }
    }

    /* Toast Styles */
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
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        opacity: 0;
        transform: translateX(120%);
        animation: slideIn 0.4s forwards;
        z-index: 1080;
    }

    .toast-success { background-color: #28a745; }
    .toast-error { background-color: #dc3545; }

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

<div class="container mt-4 quick-sms-container">
    <div class="quick-sms-header">
        <i class="bi bi-send-fill me-2"></i>Send SMS
    </div>

    {{-- Toast flash messages --}}
    @if (session('success'))
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

    @if (session('error'))
        <div class="toast-notification toast-error" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12" y2="16"/>
                </svg>
            </div>
            <div class="toast-message">
                {!! session('error') !!}
            </div>
            <button class="toast-close" aria-label="Close notification">&times;</button>
        </div>
    @endif

    <form id="smsForm" method="POST" action="{{ route('admin.sms.send.post') }}">
        @csrf

        <!-- Template Select -->
        <div class="mb-3">
            <label for="template-select" class="form-label">Select Template</label>
            <select name="template_slug" id="template-select" class="form-select" required>
                <option value="">-- Select Template --</option>
                @foreach($templatesList as $template)
                    <option value="{{ $template->slug }}"
                        data-content="{{ htmlentities($template->content) }}"
                        {{ old('template_slug') == $template->slug ? 'selected' : '' }}>
                        {{ $template->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Template Preview -->
        <div id="template-preview" class="template-preview d-none"></div>

        <!-- Placeholder Inputs -->
        <div id="placeholders-container" class="mb-3"></div>

        <!-- Recipients -->
        <div class="mb-3">
            <label for="recipients" class="form-label">Recipients (one per line)</label>
            <textarea name="recipients" class="form-control" id="recipients" rows="4" required>{{ old('recipients') }}</textarea>
        </div>

        <!-- Submit Button with Spinner -->
        <button type="submit" id="sendSmsBtn" class="btn btn-primary d-flex align-items-center">
            <span id="btnText">Send SMS</span>
            <span id="btnSpinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
            <span id="loadingText" class="ms-2 d-none">Loading...</span>
        </button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function extractPlaceholders(text) {
        const regex = /{([^}]+)}/g;
        let match;
        let placeholders = new Set();
        while ((match = regex.exec(text)) !== null) {
            placeholders.add(match[1]);
        }
        return Array.from(placeholders);
    }

    const templateSelect = document.getElementById('template-select');
    const placeholdersContainer = document.getElementById('placeholders-container');
    const templatePreview = document.getElementById('template-preview');

    templateSelect.addEventListener('change', () => {
        const selected = templateSelect.selectedOptions[0];
        const content = selected.getAttribute('data-content') || '';

        if (content) {
            templatePreview.classList.remove('d-none');
            templatePreview.textContent = content;
        } else {
            templatePreview.classList.add('d-none');
            templatePreview.textContent = '';
        }

        const placeholders = extractPlaceholders(content);
        placeholdersContainer.innerHTML = '';

        if (placeholders.length > 0) {
            placeholders.forEach(ph => {
                const div = document.createElement('div');
                div.className = 'mb-3';

                const label = document.createElement('label');
                label.className = 'form-label';
                label.textContent = `Value for {${ph}}`;

                const input = document.createElement('input');
                input.className = 'form-control';
                input.name = `placeholders[${ph}]`;
                input.required = true;

                div.appendChild(label);
                div.appendChild(input);
                placeholdersContainer.appendChild(div);
            });
        } else {
            placeholdersContainer.innerHTML = '<p class="text-muted">No placeholders in selected template.</p>';
        }
    });

    // Trigger change on load
    window.addEventListener('load', () => {
        if (templateSelect.value) {
            templateSelect.dispatchEvent(new Event('change'));
        }
    });

    // Spinner and disable logic
    const form = document.getElementById('smsForm');
    const sendBtn = document.getElementById('sendSmsBtn');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');
    const loadingText = document.getElementById('loadingText');

    form.addEventListener('submit', () => {
        sendBtn.disabled = true;
        btnText.classList.add('d-none');
        btnSpinner.classList.remove('d-none');
        loadingText.classList.remove('d-none');
    });

    // Toast close logic
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
            toast.addEventListener('animationend', () => toast.remove());
        }
    });
</script>
@endsection
