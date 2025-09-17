@extends('Admin.layouts.admin_layout')
@section('title', 'FAQs')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h5>Create FAQ</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('faqs.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="question" class="form-label">Question</label>
                    <input type="text" class="form-control @error('question') is-invalid @enderror" id="question" name="question" value="{{ old('question') }}" required>
                    @error('question')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="answer" class="form-label">Answer</label>
                    <textarea class="form-control @error('answer') is-invalid @enderror" id="answer" name="answer" rows="5" required>{{ old('answer') }}</textarea>
                    @error('answer')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Save FAQ</button>
                <a href="{{ route('faqs.index') }}" class="btn btn-secondary">Back to FAQ List</a>
            </form>
        </div>
    </div>
</div>
@endsection
