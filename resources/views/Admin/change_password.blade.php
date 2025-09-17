@extends('Admin.layouts.admin_layout')
@section('title', 'ChangePassword')
@section('content')

    {{-- <div class="py-4">
                <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                    <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                        <li class="breadcrumb-item">
                            <a href="#">
                                <svg class="icon icon-xxs" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            </a>
                        </li>
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Change Password</li>
                    </ol>
                </nav>
            </div> --}}
    <div class="container">
        <div id="flash-message">
        </div>
        <div class="row">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="h4">Change Password</h1>
                </div>
            </div><br /><br />
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card border-0 shadow components-section">
                        <form id="store_item" method="post" enctype="multipart/form-data">
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-lg-6 col-sm-4">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Old Password</label>
                                            <input type="password" name="old_password" class="form-control"
                                                id="old_password" placeholder="Enter Old Password">
                                            <span class="error_msg" id="old-passwordErr"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-4">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">New Password</label>
                                            <input type="password" class="form-control" id="new_password"
                                                name="new_password" maxlength="12" placeholder="Enter New password">
                                            <span class="error_msg" id="new_passwordErr"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-lg-6 col-sm-4">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Confirm Password</label>
                                            <input type="password" name="confirm_password" class="form-control"
                                                id="confirm_password" placeholder="Enter Confirm Password">
                                            <span class="error_msg" id="confirm_passwordErr"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-4">
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-lg-6 col-sm-4">
                                    </div>
                                    <div class="col-lg-6 col-sm-4">
                                        <button type="submit" class="btn btn-primary">Change</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


        </div>
    </div>
@endsection
@section('script')
    @parent
    <script>
        var changePasswordStore = "{{ route('admin.updatePassword') }}";
    </script>
    <script src="{{ asset('admin/js/changePassword.js') }}"></script>
    <script src="{{ asset('admin/js/commonUpdates.js') }}"></script>
@endsection
