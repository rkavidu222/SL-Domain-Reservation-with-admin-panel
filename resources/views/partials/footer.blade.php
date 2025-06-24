<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ServerClub Contact Page</title>

  <!-- Tailwind CSS -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />

  <!-- Alpine.js -->
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

  <style>
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
    }
    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    main {
      flex: 1 0 auto;
    }
    footer {
      flex-shrink: 0;
      padding: 1rem;
      text-align: center;
    }

    .tooltip {
      position: absolute;
      background-color: rgba(59, 130, 246, 0.95);
      color: white;
      padding: 6px 12px;
      border-radius: 0.375rem;
      font-size: 0.875rem;
      font-weight: 500;
      white-space: nowrap;
      opacity: 0;
      transform: translateX(10px);
      transition: opacity 0.25s ease, transform 0.25s ease;
      z-index: 100;
      pointer-events: none;
    }
    .tooltip.show {
      opacity: 1;
      transform: translateX(0);
    }

    @keyframes bounce {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-10%); }
    }
    .animate-bounce {
      animation: bounce 2s infinite;
    }

    @keyframes gentle-bounce {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-5%); }
    }
    .animate-gentle-bounce {
      animation: gentle-bounce 2.5s infinite;
    }
  </style>
</head>
<body>

<main>
  <!-- Page content here -->
</main>

<!-- WhatsApp Button -->
<div class="fixed bottom-4 right-4 z-50"
     x-data="{ visible: false }"
     @mouseenter="visible = true"
     @mouseleave="visible = false"
     @focusin="visible = true"
     @focusout="visible = false">
  <a href="https://wa.me/+94774233244"
     target="_blank"
     rel="noopener noreferrer"
     class="bg-green-500 hover:bg-green-600 rounded-full w-14 h-14 flex items-center justify-center shadow-lg animate-bounce"
     aria-label="Chat on WhatsApp">
    <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 448 512" class="w-7 h-7">
      <path d="M380.9 97.1C339 55.2 283.2 32 224.1 32 100.3 32 .1 132.3.1 256c0 45.2 11.8 89.3 34.2 128.1L0 480l100.2-33.6C138 468.2 180.3 480 224.1 480c123.7 0 224-100.3 224-224 0-59.2-23.1-114.9-67.2-158.9zM224.1 433.8c-40.6 0-80.3-11.1-114.7-32.1l-8.2-4.9-59.2 19.8 20.4-57.7-5.3-8.4c-20.3-31.7-31-68.2-31-105.5 0-105.9 86.1-192.1 192-192.1 51.3 0 99.5 20 135.8 56.2 36.3 36.3 56.3 84.5 56.3 135.8 0 105.9-86.2 192-192.1 192zm101.7-138.1c-5.6-2.8-33.1-16.3-38.2-18.2-5.1-1.9-8.9-2.8-12.6 2.8-3.7 5.6-14.4 18.2-17.6 21.9-3.2 3.7-6.5 4.2-12.1 1.4-33.1-16.6-54.9-29.6-76.8-67.3-5.8-10 5.8-9.3 16.6-30.9 1.8-3.7.9-6.9-.5-9.7s-12.6-30.2-17.3-41.4c-4.6-11.1-9.3-9.6-12.6-9.8-3.3-.2-7.1-.2-10.9-.2s-10.2 1.4-15.6 6.9c-5.4 5.6-20.3 19.8-20.3 48.3s20.8 56 23.7 59.9c2.8 3.7 40.9 62.5 99.1 87.7 13.8 6 24.6 9.5 33 12.1 13.8 4.4 26.4 3.8 36.4 2.3 11.1-1.7 33.1-13.5 37.8-26.5 4.6-13 4.6-24.1 3.2-26.5-1.3-2.3-5.1-3.7-10.7-6.5z"/>
    </svg>
  </a>
  <div class="tooltip" :class="{ 'show': visible }" style="right: 60px; top: 10px;">
    Chat on WhatsApp
  </div>
</div>

<!-- Help Icon -->
<div class="fixed bottom-20 right-4 z-50"
     x-data="{ open: false, showTip: false }"
     @keydown.escape.window="open = false"
     @mouseenter="showTip = true"
     @mouseleave="showTip = false"
     @focusin="showTip = true"
     @focusout="showTip = false">
  <button @click="open = true"
          class="bg-blue-600 hover:bg-blue-700 text-white rounded-full w-14 h-14 shadow-lg flex items-center justify-center animate-gentle-bounce">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
      <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="transparent"/>
      <path stroke-linecap="round" stroke-linejoin="round" d="M9 9a3 3 0 016 0c0 2-3 3-3 5" />
      <circle cx="12" cy="17" r="1" fill="currentColor"/>
    </svg>
  </button>
  <div class="tooltip" :class="{ 'show': showTip }" style="right: 60px; top: 10px;">
    Do you need any help?
  </div>

  <!-- Modal -->
  <div x-show="open" x-transition.opacity.duration.300ms x-cloak
       class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-40 px-4"
       @click.self="open = false" role="dialog" aria-modal="true">
    <div class="bg-white w-full max-w-md mx-auto rounded-xl shadow-xl p-6 relative">
      <button @click="open = false"
              class="absolute top-2 right-2 text-gray-400 hover:text-gray-600 text-2xl font-bold"
              aria-label="Close" type="button">&times;</button>
      <div class="text-center">
        <h2 class="text-xl font-bold text-blue-600 mb-4">Need Help from ServerClub?</h2>
        <div class="space-y-4 text-sm text-gray-700">
          <div class="flex items-center space-x-2"><span>📞</span><span><strong>Sales:</strong> 0117109693 (9AM–5PM)</span></div>
          <div class="flex items-center space-x-2"><span>💬</span><a href="https://wa.me/+94774233244" class="text-blue-600 underline" target="_blank">Chat on WhatsApp</a></div>
          <div class="flex items-center space-x-2"><span>📧</span><a href="mailto:service@serverclub.lk" class="text-blue-600 underline">service@serverclub.lk</a></div>
          <div class="flex items-center space-x-2"><span>👨‍💻</span><span><strong>Support:</strong> 24/7 Customer Support</span></div>
        </div>
        <button @click="open = false" class="mt-6 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-full">Close</button>
      </div>
    </div>
  </div>
</div>

<footer>
  <div class="contact-info">
    <div><strong>Sales:</strong> 0117109693 <small>(09.00 AM - 05.00 PM)</small></div>
    <div><strong>Email:</strong> service@serverclub.lk</div>
    <div><strong>Support:</strong> 24/7 Real Human Assistance</div>
  </div>
  <div><strong>Powered By ServerClub.LK PVT Ltd</strong></div>
</footer>

</body>
</html>
