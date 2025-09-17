@extends('Admin.layouts.app')



@section('header')
    @include('Admin.commons.header')
    @include('Admin.commons.sidebar')
@stop


@section('content')
    <div class="content-part">
        <h1>Harish</h1>
        @include('layouts.master_commons.flash_messages')
        <admin-app></admin-app>
        @yield('child_content')
    </div>
@endsection


@push('scripts_body_starts')
@endpush

@push('scripts_footer')
    @stack('page_specific_scripts')
@endpush
