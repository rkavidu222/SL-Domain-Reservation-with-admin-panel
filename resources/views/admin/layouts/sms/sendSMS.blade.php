@extends('admin.layouts.app')

@section('title', 'Quick SMS Send')

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
        margin-bottom: 0.3rem;
    }
    textarea.form-control {
        resize: vertical;
        min-height: 120px;
    }
    .char-count {
        font-size: 0.9rem;
        color: #6c757d;
    }
    .badge-count {
        font-size: 0.9rem;
        margin-left: 0.5rem;
    }
    @media(max-width: 768px) {
        .quick-sms-container {
            padding: 1.5rem;
        }
    }
</style>

<div class="quick-sms-container">
    <div class="quick-sms-header">
        <i class="bi bi-send-fill me-2"></i>Quick SMS Send
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.sms.send') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Originator</label>
            <input type="text" class="form-control" name="originator" value="SLHosting" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Country Code</label>
            <input type="text" class="form-control" name="country_code" value="+94" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Recipients:</label>
            <textarea class="form-control" name="recipients" id="recipients" rows="3" placeholder="Enter up to 100 phone numbers, one per line or comma-separated"></textarea>
            <div class="form-text">Note: You can send a maximum of 100 rows by copy-pasting.</div>
        </div>

        <div class="mb-3">
            <label class="form-label">Total Number of Recipients:</label>
            <div class="form-control bg-light" id="recipient-count">0</div>
        </div>

        <div class="mb-3">
            <label class="form-label">SMS Template (Required)</label>
            <select name="message_template_slug" class="form-select" required>
                <option value="">Select one</option>
                @foreach($templates as $slug => $content)
                    <option value="{{ $slug }}">{{ $slug }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Message Preview</label>
            <textarea class="form-control" id="message-preview" rows="4" readonly placeholder="Message preview will appear here..."></textarea>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-send me-1"></i>Send SMS
            </button>
        </div>
    </form>
</div>

<script>
    const recipientsInput = document.getElementById('recipients');
    const recipientCountDisplay = document.getElementById('recipient-count');
    const templateSelect = document.querySelector('select[name="message_template_slug"]');
    const messagePreview = document.getElementById('message-preview');

    // Count recipients and update
    recipientsInput.addEventListener('input', () => {
        const input = recipientsInput.value;
        const split = input.split(/[\n,]+/).map(s => s.trim()).filter(Boolean);
        const count = split.length;
        recipientCountDisplay.innerText = count > 100 ? "100 (Max)" : count;
    });

    // Show template preview on select change
    templateSelect.addEventListener('change', () => {
        const selected = templateSelect.value;
        const templates = @json($templates);
        messagePreview.value = templates[selected] || '';
    });
</script>
@endsection
