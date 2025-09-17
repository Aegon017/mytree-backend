@extends('Admin.layouts.admin_layout')
@section('title', 'Blog')

@push('styles')
<style>
    .blog-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        background: #FFF;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .blog-image {
        flex: 1;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .blog-image img {
        width: 100%;
        height: auto;
    }

    .blog-details {
        flex: 2;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .blog-title {
        font-size: 2rem;
        font-weight: bold;
        color: #12263a;
        border-bottom: 2px solid #256eff;
        padding-bottom: 10px;
    }

    .blog-description {
        font-size: 1rem;
        line-height: 1.6;
        color: #444;
    }

    .blog-content {
        margin-top: 20px;
        font-size: 1rem;
        line-height: 1.8;
        color: #555;
    }
</style>
@endpush

@section('content')
<div class="blog-container">
    <div class="blog-image">
        <img src="{{$blog->main_image_url}}" alt="Blog Image">
    </div>
    <div class="blog-details">
        <div class="blog-title">{{$blog->title}}</div>
        <div class="blog-description">{!! $blog->content !!}</div>
    </div>
</div>
@endsection
