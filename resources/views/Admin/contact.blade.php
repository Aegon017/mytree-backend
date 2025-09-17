@extends('Admin.layouts.admin_layout')
@section('title', 'ContactUs')
@section('content')


    <div class="container">
        <div id="flash-message">
        </div>
        <br />

        <div class="row">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="h4">Manage Contact Us</h1>
                </div>
            </div><br /><br />
            <div class="row" style="display:none;">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <select class="form-select" id="trash" aria-label="Default select example">
                                <option value="0" selected><span id="allCount">All</span></option>
                                <option value="1"><span id="trashCount">Trash</span></option>
                            </select>
                        </div>
                        <div class="col-md-6">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 ml-auto">
                    <button type="button" class="btn btn-block btn-gray-800 mb-3" onclick="addItemModel()">Add
                        Addons</button>
                    <button class="btn btn-success mb-3" name="search" id="search"
                        onclick="updateActivationStatus('1')">&nbsp;Active</button>
                    <button class="btn btn-warning mb-3" onclick="updateActivationStatus('0')"><i class="fa fa-ban"
                            aria-hidden="true"></i>&nbsp;In Active</button>
                    <button class="btn btn-danger mb-3" onclick="commonDelete('1')"><i class="fa fa-trash"
                            aria-hidden="true" onclick=""></i>&nbsp;Delete</button>
                    <button class="btn btn-info mb-3" style="display:none;" id="restore_btn" onclick="commonDelete('0')"><i
                            class="fa fa-trash" aria-hidden="true" onclick=""></i>&nbsp;Restore</button>
                </div>
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
                                    <th>Name</th>
                                    {{-- <th>Mobile</th> --}}
                                    <th>Email</th>
                                    {{-- <th>Subject</th> --}}
                                    <th>Message</th>

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
        var contactUrl = "{{ route('contact.index') }}";
    </script>
    <script src="{{ asset('admin/js/contact.js') }}"></script>
    <script src="{{ asset('admin/js/commonUpdates.js') }}"></script>
@endsection
