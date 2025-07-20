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

    <form method="POST" action="#">
        @csrf

        <div class="mb-3">
            <label class="form-label">Originator</label>
            <input type="text" class="form-control" value="SLHosting" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Country Code</label>
            <input type="text" class="form-control" value="+94" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Recipients:</label>
            <textarea class="form-control" id="recipients" rows="3" placeholder="Enter up to 100 phone numbers, one per line or comma-separated"></textarea>
            <div class="form-text">Note: You can send a maximum of 100 rows by copy-pasting.</div>
        </div>

        <div class="mb-3">
            <label class="form-label">Total Number of Recipients:</label>
            <div class="form-control bg-light" id="recipient-count">0</div>
        </div>

        <div class="mb-3">
            <label class="form-label">SMS Template (Optional)</label>
            <select class="form-select" id="template">
                <option selected>Select one</option>
                <option>Promo: Get 25% off today!</option>
                <option>Reminder: Your payment is due.</option>
                <option>Notice: Your account has been updated.</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Message</label>
            <textarea class="form-control" id="message" rows="4" maxlength="160" placeholder="Enter your message here..."></textarea>
            <div class="d-flex justify-content-between mt-1">
                <span class="char-count" id="char-count">REMAINING: 160 / 160 (0 CHARACTERS)</span>
                <span class="badge bg-primary badge-count" id="msg-count">MESSAGE(S): 0</span>
            </div>
            <div class="form-text">
                You can randomize the message by using spintax. For example: <code>{Hi|Hello} {John|Jane}</code> will result in <code>Hi John</code> or <code>Hello Jane</code>.
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-send me-1"></i>Send SMS
            </button>
        </div>
    </form>
</div>

<script>
    const messageInput = document.getElementById('message');
    const charCount = document.getElementById('char-count');
    const msgCount = document.getElementById('msg-count');
    const recipientsInput = document.getElementById('recipients');
    const recipientCountDisplay = document.getElementById('recipient-count');

    messageInput.addEventListener('input', updateMessageCount);
    recipientsInput.addEventListener('input', updateRecipientCount);

    function updateMessageCount() {
        const msg = messageInput.value;
        const length = msg.length;
        const remaining = 160 - length;
        const messages = Math.ceil(length / 160);
        charCount.innerText = `REMAINING: ${remaining >= 0 ? remaining : 0} / 160 (${length} CHARACTERS)`;
        msgCount.innerText = `MESSAGE(S): ${messages}`;
    }

    function updateRecipientCount() {
        const input = recipientsInput.value;
        const split = input.split(/[\n,]+/).map(s => s.trim()).filter(Boolean);
        const count = split.length;
        recipientCountDisplay.innerText = count > 100 ? "100 (Max)" : count;
    }
</script>
@endsection
