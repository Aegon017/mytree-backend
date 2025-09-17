@extends('Admin.layouts.admin_layout')
@section('title', 'Orders')
@section('content')


    <div class="container">
        <div id="flash-message">
        </div>
        <br />
        <!-- Add Item Modal -->





        <div class="row">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="h4">Manage order</h1>
                </div>
            </div><br /><br />
            <div class="row">
                <div class="col-md-6">

                </div>
                <div class="col-md-6 ml-auto">
<!-- 
                    <button class="btn btn-danger mb-3" onclick="orderCancel()"><i class="fa fa-trash" aria-hidden="true"
                            onclick=""></i>&nbsp;Cancel</button> -->

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
                                    <th>Order ID </th>
                                    <th>Order Date </th>
                                    <th>Type</th>
                                    <th>Order Status</th>
                                    <th>Payment Status</th>
                                    <th>Paid Amount</th>
                                    <th>Actions</th>
                                    <!-- <th>Order List</th> -->
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
        var orderUrl = "{{ route('order.index') }}";
    </script>
    <script src="{{ asset('admin/js/order.js') }}"></script>
    <script src="{{ asset('admin/js/commonUpdates.js') }}"></script>
@endsection
