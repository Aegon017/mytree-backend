@extends('Admin.layouts.admin_layout')
@section('title', 'Ecommerce')
@section('content')


<div class="container">
    <div id="flash-message">
    </div>
    <br />
    <!-- Add Item Modal -->
    <div class="col-lg-4">
        <!-- Modal Content -->
        <div class="modal fade" id="addItem" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered  modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-body p-0">
                        <div class="card p-3 p-lg-4">
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                            <div class="text-center text-md-center mb-4 mt-md-0">
                                <h1 class="mb-0 h4">Add coupon</h1>
                            </div>
                            <form id="store_item" method="post" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-12 mb-4">
                                            <div class="card border-0 shadow components-section">
                                                <div class="card-body">
                                                    <div class="row mb-4">
                                                       <div class="col-lg-6 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="exampleInputEmail1"> Coupon Code</label>
                                                                <input type="text" name="code" class="form-control" id="code" placeholder="Enter code">
                                                                <span class="error_msg" id="codeErr"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="exampleInputEmail1"> Type</label>
                                                                <select class="form-control" id="type" name="type">
                                                                    <option value="fixed">Fixed</option>
                                                                    <option value="percentage">Percentage</option>
                                                                </select>
                                                                <span class="error_msg" id="typeErr"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-4">
                                                       <div class="col-lg-6 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="discount_value"> Discount Value</label>
                                                                <input type="number" name="discount_value" class="form-control" id="discount_value" placeholder="Enter Discount Value">
                                                                <span class="error_msg" id="discount_valueErr"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="exampleInputEmail1"> Usage Limit</label>
                                                                <input type="number" name="usage_limit" class="form-control" id="usage_limit" placeholder="Enter Usage Limit">
                                                                <span class="error_msg" id="usage_limitErr"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-4">
                                                       <div class="col-lg-6 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="valid_from"> Valid From</label>
                                                                <input type="date" name="valid_from" class="form-control" id="valid_from" placeholder="Enter Valid From">
                                                                <span class="error_msg" id="valid_fromErr"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="exampleInputEmail1"> Valid To</label>
                                                                <input type="date" name="valid_to" class="form-control" id="valid_to" placeholder="Enter Valid To">
                                                                <span class="error_msg" id="valid_toErr"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-4">
                                                       <div class="col-lg-6 col-sm-6">
                                                        </div>
                                                        <div class="col-lg-6 col-sm-6">
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Add</button>
                                    <button class="btn btn-outline-danger" type="button" data-bs-dismiss="modal">Close</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End of Modal Content -->
    </div>



    <!-- Update Brand Modal -->
    <div id="editItem" class="modal fade" role="dialog" style="margin-top: 20px">
        <div class="modal-dialog modal-dialog-centered modal-lg">

            <!-- Modal content-->
            <div class="modal-content">
                <form id="update_item" method="post" enctype="multipart/form-data">
                    <div class="modal-body p-0">
                        <div class="card p-3 p-lg-4">
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                            <div class="text-center text-md-center mb-4 mt-md-0">
                                <h1 class="mb-0 h4">Update coupon</h1>
                            </div>
                            <div class="modal-body">
                            <div class="row">
                                        <div class="col-12 mb-4">
                                            <div class="card border-0 shadow components-section">
                                                <div class="card-body">
                                                    
                                                    
                                                    <div class="row mb-4">
                                                       <div class="col-lg-6 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="exampleInputEmail1"> Coupon Code</label>
                                                                <input type="hidden" name="id" class="form-control"
                                                                    id="update_id">
                                                                    <input type="hidden" name="_method" value="PUT">
                                                                <input type="text" name="code" class="form-control" id="code_edit" placeholder="Enter code">
                                                                <span class="error_msg" id="code_editErr"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="exampleInputEmail1"> Type</label>
                                                                <select class="form-control" id="type_edit" name="type">
                                                                    <option value="fixed">Fixed</option>
                                                                    <option value="percentage">Percentage</option>
                                                                </select>
                                                                <span class="error_msg" id="type_editErr"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-4">
                                                       <div class="col-lg-6 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="discount_value"> Discount Value</label>
                                                                <input type="number" name="discount_value" class="form-control" id="discount_value_edit" placeholder="Enter Discount Value">
                                                                <span class="error_msg" id="discount_value_editErr"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="exampleInputEmail1"> Usage Limit</label>
                                                                <input type="number" name="usage_limit" class="form-control" id="usage_limit_edit" placeholder="Enter Usage Limit">
                                                                <span class="error_msg" id="usage_limit_editErr"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-4">
                                                       <div class="col-lg-6 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="valid_from"> Valid From</label>
                                                                <input type="date" name="valid_from" class="form-control" id="valid_from_edit" placeholder="Enter Valid From">
                                                                <span class="error_msg" id="valid_from_editErr"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="exampleInputEmail1"> Valid To</label>
                                                                <input type="date" name="valid_to" class="form-control" id="valid_to_edit" placeholder="Enter Valid To">
                                                                <span class="error_msg" id="valid_to_editErr"></span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                
                            </div>

                            <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Update</button>
                                <button class="btn btn-outline-danger" type="button" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="row">
            <div class="col-md-12">
                <h1 class="h4">Manage coupon</h1>
            </div>
        </div><br /><br />
        <div class="row">
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
                coupon</button>
                <button class="btn btn-success mb-3" name="search" id="search" onclick="updateActivationStatus('1')">&nbsp;Active</button>
                <button class="btn btn-warning mb-3" onclick="updateActivationStatus('0')"><i class="fa fa-ban" aria-hidden="true"></i>&nbsp;In Active</button>
                <button class="btn btn-danger mb-3" onclick="commonDelete('1')"><i class="fa fa-trash" aria-hidden="true" onclick=""></i>&nbsp;Delete</button>
                <button class="btn btn-info mb-3" style="display:none;" id="restore_btn" onclick="commonDelete('0')"><i class="fa fa-trash" aria-hidden="true" onclick=""></i>&nbsp;Restore</button>
            </div>
        </div>
        <br />
        <br />
        <div class="card border-0 shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-centered table-nowrap mb-0 rounded serversideDatatableForItems" id="myTable">
                        <thead class="thead-light">
                            <tr>
                                <th><input type="checkbox" class="inline-checkbox" name="multiAction" id="multiAction" /> S.No.</th>
                                <th>Code</th>
                                <th>Type</th>
                                <th>Discount Value</th>
                                <th>Usage Limit</th>
                                <th>Used Count</th>
                                <th>Valid From</th>
                                <th>Valid To</th>
                                <th>Status</th>
                                <th>Actions</th>
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
    var couponUrl = "{{ route('coupon.index') }}";
    var couponStore = "{{ route('coupon.store') }}";
    var couponEdit = '{{ route('coupon.edit', ':id') }}';
    var couponUpdate = '{{ route('coupon.update',':id') }}';
    var statusUpdate = '{{ route('coupon-status') }}';
    var deleteUpdate = '{{ route('coupon-delete') }}';
</script>
<script src="{{ asset('admin/js/coupon.js') }}"></script>
<script src="{{ asset('admin/js/commonUpdates.js') }}"></script>
@endsection