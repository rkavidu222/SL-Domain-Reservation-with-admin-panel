@extends('admin.layouts.app')

@section('title', 'Manage Domain Pricing')

@section('content')

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
