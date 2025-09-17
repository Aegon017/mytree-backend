@if (\Session::has('error'))
<div class="alert alert-danger">
    <ul>
        <li>{!! \Session::get('error') !!}</li>
    </ul>
</div>
@endif
@if (\Session::has('success'))
<div class="alert alert-success">
    <ul>
        <li>3333{!! \Session::get('success') !!}</li>
    </ul>
</div>
@endif