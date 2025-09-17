@extends('Admin.layouts.admin_layout')
@section('title', 'Users')
@section('content')


    <div class="container">
        <div id="flash-message">
        </div>
        <br />
        <!-- Add Item Modal -->





        <div class="row">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="h4">Manage Users</h1>
                </div>
            </div><br /><br />
            <div class="row">


            </div>
            <br />
            <br />
            <div class="card border-0 shadow mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0 rounded serversideDatatableForItems"
                            id="myTable">
                            <thead class="thead-light">
                                <tr>
                                    <th><input type="checkbox" class="inline-checkbox" name="multiAction"
                                            id="multiAction" /> S.No.</th>
                                    <th>Details</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                </tr>
                            </thead>

                        </table>
                    </div>
                </div>
            </div>


        </div>
    </div>
@endsection
@section('script')
    @parent
    <script>
        var userUrl = "{{ route('user.index') }}";
    </script>
    <script src="{{ asset('admin/js/users.js') }}"></script>
    <script src="{{ asset('admin/js/commonUpdates.js') }}"></script>
@endsection
