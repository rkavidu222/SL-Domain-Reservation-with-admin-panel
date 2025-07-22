@extends('admin.layouts.app')

@section('title', 'Edit SMS Template')

@push('styles')
  <link rel="stylesheet" href="{{ asset('admin/css/editTemplate.css') }}">
@endpush

@section('content')


<div class="admin-management-container">
  <h2 class="mb-4 text-primary fw-bold">
    <i class="bi bi-pencil-square"></i> Edit SMS Template
  </h2>

  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.sms.template.update', $template->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-3">
      <label for="name" class="form-label">Template Name</label>
      <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $template->name) }}" required>
    </div>
    <div class="mb-3">
      <label for="slug" class="form-label">Slug (unique identifier)</label>
      <input type="text" id="slug" name="slug" class="form-control" value="{{ old('slug', $template->slug) }}" required>
      <small class="form-text text-muted">Slug should be unique and contain no spaces.</small>
    </div>
    <div class="mb-3">
      <label for="content" class="form-label">Message Content</label>
      <textarea id="content" name="content" class="form-control" rows="6" required>{{ old('content', $template->content) }}</textarea>
      <small class="form-text text-muted">You can use variables like <code>{name}</code>, <code>{phone}</code>, etc.</small>
    </div>
    <div class="text-end">
      <button type="submit" class="btn btn-primary">Update Template</button>
    </div>
  </form>
</div>
@endsection
