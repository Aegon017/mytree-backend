@extends('Admin.layouts.admin_layout')
@section('title', 'Theme Settings')

@section('content')
<div class="container mt-4">
    <h2>Update Theme Colors</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="primary_color" class="form-label">Primary Color</label>
            <input type="color" id="primary_color" name="primary_color" class="form-control"
                   value="{{ $settings['primary_color'] }}">
        </div>
        <div class="mb-3">
            <label for="secondary_color" class="form-label">Secondary Color</label>
            <input type="color" id="secondary_color" name="secondary_color" class="form-control"
                   value="{{ $settings['secondary_color'] }}">
        </div>
        <div class="mb-3">
            <label for="background_color" class="form-label">Background Color</label>
            <input type="color" id="background_color" name="background_color" class="form-control"
                   value="{{ $settings['background_color'] }}">
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>
@endsection
