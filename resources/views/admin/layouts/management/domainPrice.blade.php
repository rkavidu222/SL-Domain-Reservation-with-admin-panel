@extends('admin.layouts.app')

@section('title', 'Manage Domain Pricing')

@section('content')
<style>
    .management-container {
        max-width: 1100px;
        margin: 1rem auto 2rem;
        padding: 2rem;
        background: #fff;
        border-radius: 1rem;
        border: 1px solid #dee2e6;
        box-shadow:
            0 4px 6px rgba(0, 0, 0, 0.1),
            0 8px 20px rgba(0, 0, 0, 0.07);
        transition: box-shadow 0.3s ease;
    }
    .management-container:hover {
        box-shadow:
            0 8px 12px rgba(0, 0, 0, 0.15),
            0 12px 30px rgba(0, 0, 0, 0.1);
    }

    /* Toast styles for flash messages */
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

<div class="management-container">
    <h2 class="mb-4 d-flex justify-content-center align-items-center gap-2 text-primary fw-bold flex-wrap text-center">
        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-tag-fill" viewBox="0 0 16 16" width="24" height="24">
            <path d="M2 1a1 1 0 0 0-1 1v4.586a1 1 0 0 0 .293.707l7.414 7.414a2 2 0 0 0 2.828 0l4.586-4.586a2 2 0 0 0 0-2.828L7.121 1.293A1 1 0 0 0 6.414 1H2zm3.5 3a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
        </svg>
        Domain Price Management
    </h2>

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


    @foreach($prices as $price)
        <form method="POST" action="{{ route('admin.domain_prices.update.single', $price->id) }}" class="mb-4">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-header fw-bold">{{ $price->category }}</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Old Price</label>
                            <input type="number" step="0.01" class="form-control" name="old_price" value="{{ $price->old_price }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">New Price</label>
                            <input type="number" step="0.01" class="form-control" name="new_price" value="{{ $price->new_price }}" required>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-success">Update {{ $price->category }}</button>
                </div>
            </div>
        </form>
    @endforeach
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
</script>
@endsection
