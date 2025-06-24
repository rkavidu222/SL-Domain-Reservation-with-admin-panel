@extends('layouts.app')

@section('title', '.LK Domain Reservation | SriLanka Hosting')

@section('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
    body {
        background-color: #f3f6fa;
    }

    .card-animate {
        background: #fff;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.05);
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .section-header {
        border-left: 6px solid #2563EB;
        padding-left: 12px;
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: #1F2937;
        background-color: #F3F4F6;
        padding: 10px;
        border-radius: 0.25rem;
    }

    .brand-logo {
        display: flex;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .brand-logo img {
        width: 150px;
    }

    .price-badge {
        font-size: 0.95rem;
    }

    @media (max-width: 480px) {
        .card-animate {
            margin: 1rem;
            padding: 1.25rem;
        }
    }
</style>
@endsection

@section('content')
<div class="card-animate mx-auto mt-4" style="max-width: 500px;">

    <div class="brand-logo">
        <img src="https://www.srilankahosting.lk/logo.svg" alt="SriLanka Hosting Logo" />
    </div>

    {{-- CAT2 --}}
    <div class="mb-5 p-4 rounded border border-gray-300 bg-white">
        <div class="section-header">CAT2 - Top Level Domain Only</div>
        <div class="text-center my-3 flex justify-center items-center gap-2">
            <i class="bi bi-globe2 text-blue-600"></i>
            <span class="font-medium">rash.lk</span>
            <span class="line-through text-gray-500 ml-3">LKR 5000.00</span>
            <span class="text-red-600 font-bold ml-3">LKR 2600.00</span>
        </div>
        <div class="text-center">
            <a href="{{ route('contact.page') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 flex items-center justify-center mx-auto w-fit">
                <i class="bi bi-cart3 mr-2"></i> Buy Now
            </a>
        </div>
    </div>

    {{-- CAT1 --}}
    <div class="mb-5 p-4 rounded border border-gray-300 bg-white">
        <div class="section-header">CAT1 - Full Domain Package</div>
        <div class="text-center my-3 flex justify-center items-center gap-2">
            <i class="bi bi-globe2 text-blue-600"></i>
            <span class="font-medium">rash.lk</span>
            <span class="line-through text-gray-500 ml-3">LKR 8000.00</span>
            <span class="text-red-600 font-bold ml-3">LKR 7500.00</span>
        </div>
        <div class="text-center text-gray-600 text-sm mt-3">
            <p class="mb-2">You automatically reserve the following names:</p>
            <ul class="list-disc list-inside text-left pl-6">
                <li>rash.edu.lk</li>
                <li>rash.com.lk</li>
                <li>rash.hotel.lk</li>
                <li>rash.org.lk</li>
                <li>rash.web.lk</li>
            </ul>
        </div>
        <div class="text-center mt-3">
            <a href="{{ route('contact.page') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 flex items-center justify-center mx-auto w-fit">
                <i class="bi bi-cart3 mr-2"></i> Buy Now
            </a>
        </div>
    </div>

    {{-- Other Options --}}
    <div class="mb-5 p-4 rounded border border-gray-300 bg-white">
        <div class="section-header">Other Options Related to Your Domain Search</div>

        {{-- CAT3 --}}
        <div class="mb-6">
            <div class="font-semibold text-blue-700 mb-3">CAT3 - Second Level Domain Only</div>
            @php
                $cat3domains = ['rash.edu.lk', 'rash.com.lk', 'rash.hotel.lk', 'rash.org.lk', 'rash.web.lk'];
            @endphp
            @foreach ($cat3domains as $domain)
            <div class="mb-4 p-3 border border-gray-200 rounded bg-gray-50">
                <div class="flex justify-between items-center mb-1">
                    <div class="flex items-center space-x-2">
                        <i class="bi bi-globe2 text-blue-600"></i>
                        <span>{{ $domain }}</span>
                    </div>
                    <div class="line-through text-gray-500 price-badge">LKR 1000.00</div>
                </div>
                <div class="flex justify-between items-center">
                    <div class="text-red-500 font-semibold">LKR 950.00</div>
                    <a href="{{ route('contact.page') }}"
                       class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 flex items-center">
                        <i class="bi bi-cart3 mr-1"></i> Buy Now
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Suggestions --}}
        <div>
            <div class="font-semibold text-blue-700 mb-3">Suggested Top Level Domains</div>
            @php
                $suggestedDomains = ['irash.lk', 'erash.lk', 'myrash.lk', 'fastrash.lk', 'mrash.lk'];
            @endphp
            @foreach ($suggestedDomains as $domain)
            <div class="mb-4 p-3 border border-gray-200 rounded bg-gray-50">
                <div class="flex justify-between items-center mb-1">
                    <div class="flex items-center space-x-2">
                        <i class="bi bi-globe2 text-blue-600"></i>
                        <span>{{ $domain }}</span>
                    </div>
                    <div class="line-through text-gray-500 price-badge">LKR 5000.00</div>
                </div>
                <div class="flex justify-between items-center">
                    <div class="text-red-500 font-semibold">LKR 4600.00</div>
                    <a href="{{ route('contact.page') }}"
                       class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 flex items-center">
                        <i class="bi bi-cart3 mr-1"></i> Buy Now
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
