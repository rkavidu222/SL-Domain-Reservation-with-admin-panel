@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="app-content">
      <div class="container-fluid">
        @include('admin.layouts.dashboard-widgets')
      </div>
    </div>
@endsection
