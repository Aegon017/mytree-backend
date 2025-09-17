@extends('Admin.layouts.admin_layout')
@section('title', 'Reports')
@section('content')


    <div class="container">
        <div id="flash-message">
        </div>
        <br />
        <!-- Add Item report -->
        <div class="col-lg-4">
            <!-- report Content -->
           
            <!-- End of Tree Content -->
        </div>



        <!-- Update Tree Modal -->
       

        <div class="row">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="h4">Reports</h1>
                </div>
            </div><br /><br />
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <!-- <select class="form-select" id="trash" aria-label="Default select example">
                                <option value="0" selected><span id="allCount">All</span></option>
                                <option value="1"><span id="trashCount">Trash</span></option>
                            </select> -->
                            <input type="date" id="startDate" name="startDate" class="form-control" value="{{ old('month', \Carbon\Carbon::now()->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-6">
                        <div class="form-group">
                            <input type="date" id="endDate" name="endDate" class="form-control" value="{{ old('month', \Carbon\Carbon::now()->format('Y-m-d')) }}" required>

                        </div>
                        </div>
                    </div>
                </div>
                <br/> <br />
                <br />
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="col-auto mb-3 mb-sm-0">
                                <div class="calendar d-flex" style="border: 1px solid #98a998;width: 13.125rem;">
                                    <span class="calendar-month" style="background-color: #10b981;">Total Paid Orders</span>
                                    <span class="calendar-day py-2" style="background-color: #ffffff;"><span id="paid_orders">0</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="col-auto mb-3 mb-sm-0">
                                <div class="calendar d-flex" style="border: 1px solid #98a998;width: 13.125rem;">
                                    <span class="calendar-month" style="background-color: #e11d48;">Total Failed Orders</span>
                                    <span class="calendar-day py-2" style="background-color: #ffffff;"><span id="failed_orders">0</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="col-auto mb-3 mb-sm-0">
                                <div class="calendar d-flex" style="border: 1px solid #98a998;width: 13.125rem;">
                                    <span class="calendar-month" style="background-color: #e28207;">Total Pending Orders</span>
                                    <span class="calendar-day py-2" style="background-color: #ffffff;"><span id="pending_orders">0</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="col-auto mb-3 mb-sm-0">
                                <div class="calendar d-flex" style="border: 1px solid #98a998;width: 13.125rem;">
                                    <span class="calendar-month" style="background-color: #10b981;">Total Revenue</span>
                                    <span class="calendar-day py-2" style="background-color: #ffffff;"><span id="total_revenue">0</span></span>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="col-md-6 ml-auto">
                    <!-- <button class="btn btn-success mb-3" name="search" id="search"
                        onclick="updateActivationStatus('1')">&nbsp;Active</button>
                    <button class="btn btn-warning mb-3" onclick="updateActivationStatus('0')"><i class="fa fa-ban"
                            aria-hidden="true"></i>&nbsp;In-Active</button> -->
                    <!-- <button class="btn btn-danger mb-3" onclick="commonDelete('1')"><i class="fa fa-trash"
                            aria-hidden="true" onclick=""></i>&nbsp;Delete</button>

                    <button class="btn btn-info mb-3" style="display:none;" id="restore_btn"
                        onclick="commonDelete('0')"><i class="fa fa-trash" aria-hidden="true"
                            onclick=""></i>&nbsp;Restore</button> -->
                </div>
            </div>
            <br />
            <br />
            <div class="card border-0 shadow mb-4" style="margin-top: 20px;">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0 rounded serversideDatatableForItems"
                            id="myTable">
                            <thead class="thead-light">
                                <tr>
                                   
                                    <th>User</th>
                                    <th>Date</th>
                                    <th>OrderId</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Razorpay OrderId</th>
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
       var reportOrderUrl = "{{ route('admin.report.orders.list') }}";
       var getCountReport = '{{ route('admin.report.orders.count', ['start' => ':start', 'end' => ':end']) }}';

    </script>
    <script src="{{ asset('admin/js/reportsOrders.js') }}"></script>
    <script src="{{ asset('admin/js/commonUpdates.js') }}"></script>
@endsection
