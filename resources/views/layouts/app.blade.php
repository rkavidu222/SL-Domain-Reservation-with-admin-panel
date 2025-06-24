<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>@yield('title')</title>
  <link rel="shortcut icon" href="https://www.srilankahosting.lk/assets/images/icons/favicon.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  @yield('head')

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    body { background-color: #f4f7fb; }
    .top-header { background-color: #003f91; color: white; padding: 15px; text-align: center; font-size: 1.2rem; font-weight: bold; }
    footer { background-color: #003f91; color: white; text-align: center; padding: 15px; font-size: 14px; }
    .whatsapp-float { position: fixed; bottom: 20px; right: 20px; z-index: 999; }
  </style>

</head>
<body>

  @include('partials.header')

  <main class="container py-4">
    @yield('content')
  </main>

  @include('partials.footer')

  @yield('scripts')
</body>
</html>
