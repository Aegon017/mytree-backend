@extends('Admin.layouts.admin_layout')
@section('title', 'TreeFund')
@push('styles')
    <style>
.modal-content {
        position: relative !important;
    }

    .ck.ck-balloon-panel {
        z-index: 1056 !important;
        position: absolute !important;
    }

    body.modal-open {
        overflow: hidden !important;
        padding-right: 0 !important;
    }

    .ck.ck-editor__main {
        scroll-margin-top: 0 !important;
    }
</style>
@endpush
@section('content')


    <div class="container">
        <div id="flash-message">
        </div>
        <br />
        <!-- Add Item Tree -->
        <div class="col-lg-4">
            <!-- Tree Content -->
            <div class="modal fade" id="addItem" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered  modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-body p-0">
                            <div class="card p-3 p-lg-4">
                                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                                <div class="text-center text-md-center mb-4 mt-md-0">
                                    <h1 class="mb-0 h4">Add Campaign</h1>
                                </div>
                                <form id="store_item" method="post" enctype="multipart/form-data">
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-12 mb-4">
                                                <div class="card border-0 shadow components-section">
                                                    <div class="card-body">
                                                        <div class="row mb-4">
                                                            <div class="col-lg-12 col-sm-12">
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">Campaign Name</label>
                                                                    <input type="text" name="name"
                                                                        class="form-control" id="name"
                                                                        placeholder="Enter Campaign name">
                                                                    <span class="error_msg" id="nameErr"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-4">
                                                            <div class="col-lg-6 col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">Expiration Date</label>
                                                                    <input type="date" name="expiration_date"
                                                                        class="form-control" id="expiration_date"
                                                                        placeholder="Enter Date">
                                                                    <span class="error_msg" id="expiration_dateErr"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="row mb-4">
                                                            <div class="col-lg-6 col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">Goal Amount</label>
                                                                    <input type="number" name="goal_amount"
                                                                        class="form-control" id="goal_amount"
                                                                        placeholder="Enter Goal Amount">
                                                                    <span class="error_msg" id="goal_amountErr"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">Image</label>
                                                                    <input type="file" name="main_image"
                                                                        class="form-control-file" id="main_image"
                                                                        accept=".png, .jpg, .jpeg">
                                                                    <span class="error_msg" id="main_imageErr"></span>
                                                                </div>
                                                            </div>
                                                        </div>


                                                        <div class="row mb-4">
                                                            <div class="col-lg-4 col-sm-4">
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">State</label>
                                                                    <select class="form-select" id="state_id"
                                                                        name ="state_id" aria-label="Default select example">
                                                                        <option value="0">Select State</option>
                                                                        @if (count($states) > 0)
                                                                            @foreach ($states as $state)
                                                                                <option value="{{ $state->id }}">
                                                                                    {{ $state->name }}</option>
                                                                            @endforeach
                                                                        @endif
                                                                    </select>
                                                                    <span class="error_msg" id="state_idErr"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4 col-sm-4">
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">City</label>
                                                                    <select class="form-select" id="city_id"
                                                                        name ="city_id" aria-label="Default select example">
                                                                        <option value="0">Select City</option>
                                                                    </select>
                                                                    <span class="error_msg" id="city_idErr"></span>
                                                                </div>

                                                            </div>
                                                            <div class="col-lg-4 col-sm-4">
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">Area</label>
                                                                    <input type="text" name="area"
                                                                        class="form-control" id="area"
                                                                        placeholder="Enter area">
                                                                    <span class="error_msg" id="areaErr"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                       


                                                        <div class="row mb-4">
                                                            
                                                            <!-- <div class="col-lg-6 col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">More Images</label>
                                                                    <input type="file" name="more_imgs[]"
                                                                        class="form-control-file" id="more_imgs"
                                                                        accept=".png, .jpg, .jpeg" multiple>
                                                                    <span class="error_msg" id="more_imgsErr"></span>
                                                                </div>
                                                            </div> -->
                                                        </div>
                                                        

                                                        <div class="row mb-4">
                                                            <div class="col-lg-12 col-sm-12">
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">
                                                                        Description</label>
                                                                        <textarea name="descriptionEditor"
                                                                        class="form-control" id="descriptionEditor"></textarea>
                                                                    <textarea id="description" name="description" style="display:none;"></textarea>
                                                                   
                                                                    <span class="error_msg"
                                                                        id="descriptionErr"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Add</button>
                                        <button class="btn btn-outline-danger" type="button"
                                            data-bs-dismiss="modal">Close</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of Tree Content -->
        </div>



        <!-- Update Tree Modal -->
        <div id="editItem" class="modal fade" role="dialog" style="margin-top: 0px">
            <div class="modal-dialog modal-dialog-centered modal-lg">

                <!-- Tree content-->
                <div class="modal-content">
                    <form id="update_item" method="post" enctype="multipart/form-data">
                        <div class="modal-body p-0">
                            <div class="card p-3 p-lg-4">
                                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                                <div class="text-center text-md-center mb-4 mt-md-0">
                                    <h1 class="mb-0 h4">Update Campaign</h1>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-12 mb-4">
                                            <div class="card border-0 shadow components-section">
                                                <div class="card-body">
                                                    
                                                    


                                                    <!-- <div class="row mb-4">
                                                        <div class="col-lg-6 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="exampleInputEmail1">City</label>
                                                                <select class="form-select" id="city_id_edit"
                                                                    name ="city_id" aria-label="Default select example">
                                                                    <option value="0">Select City</option>
                                                                    
                                                                </select>
                                                                <span class="error_msg" id="city_id_editErr"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="exampleInputEmail1">Area</label>
                                                                <select class="form-select" id="area_id_edit"
                                                                    name ="area_id" aria-label="Default select example">
                                                                    <option value="0">Select Area</option>
                                                                </select>
                                                                <span class="error_msg" id="area_id_editErr"></span>
                                                            </div>

                                                        </div>
                                                    </div> -->
                                                    <div class="row mb-4">
                                                            <div class="col-lg-12 col-sm-12">
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">Campaign  Name</label>
                                                                    <input type="text" name="name"
                                                                        class="form-control" id="name_edit"
                                                                        placeholder="Enter Campaign name">
                                                                        <input type="hidden" name="id" class="form-control"
                                                                    id="update_id">
                                                                <input type="hidden" name="_method" value="PUT">
                                                                    <span class="error_msg" id="name_editErr"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mb-4">
                                                            <div class="col-lg-6 col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">Expiration Date</label>
                                                                    <input type="date" name="expiration_date"
                                                                        class="form-control" id="expiration_date_edit"
                                                                        placeholder="Enter Date">
                                                                    <span class="error_msg" id="expiration_date_editErr"></span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row mb-4">
                                                        <div class="col-lg-6 col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">Goal Amount</label>
                                                                    <input type="number" name="goal_amount"
                                                                        class="form-control" id="goal_amount_edit"
                                                                        placeholder="Enter Goal Amount">
                                                                    <span class="error_msg" id="goal_amount_editErr"></span>
                                                                </div>
                                                            </div>
                                                        <div class="col-lg-6 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="exampleInputEmail1">Image</label>
                                                                <input type="file" name="main_image"
                                                                    class="form-control-file" id="main_image_edit"
                                                                    accept=".png, .jpg, .jpeg">
                                                                <br />
                                                                <div class="error_msg" id="main_image_display"></div>
                                                                <span class="error_msg" id="main_image_editErr"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                        
                                                        

                                                        <div class="row mb-4">
                                                            <div class="col-lg-4 col-sm-4">
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">State</label>
                                                                    <select class="form-select" id="state_id_edit"
                                                                        name ="state_id" aria-label="Default select example">
                                                                        <option value="0">Select State</option>
                                                                        @if (count($states) > 0)
                                                                            @foreach ($states as $state)
                                                                                <option value="{{ $state->id }}">
                                                                                    {{ $state->name }}</option>
                                                                            @endforeach
                                                                        @endif
                                                                    </select>
                                                                    <span class="error_msg" id="state_id_editErr"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-4 col-sm-4">
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">City</label>
                                                                    <select class="form-select" id="city_id_edit"
                                                                        name ="city_id" aria-label="Default select example">
                                                                        <option value="0">Select City</option>
                                                                    </select>
                                                                    <span class="error_msg" id="city_id_editErr"></span>
                                                                </div>

                                                            </div>
                                                            <div class="col-lg-4 col-sm-4">
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">Area</label>
                                                                    <input type="text" name="area"
                                                                        class="form-control" id="area_edit"
                                                                        placeholder="Enter area">
                                                                    <span class="error_msg" id="area_editErr"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    


                                                    <div class="row mb-4">

                                                    </div>

                                                    <!-- <div class="row mb-4">
                                                        <div class="col-lg-6 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="exampleInputEmail1">Image</label>
                                                                <input type="file" name="main_image"
                                                                    class="form-control-file" id="main_image_edit"
                                                                    accept=".png, .jpg, .jpeg">
                                                                <br />
                                                                <div class="error_msg" id="main_image_display"></div>
                                                                <span class="error_msg" id="main_image_editErr"></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6 col-sm-6">
                                                            <div class="form-group">
                                                                <label for="exampleInputEmail1">More Images</label>
                                                                <input type="file" name="more_imgs[]"
                                                                    class="form-control-file" id="more_imgs_edit"
                                                                    accept=".png, .jpg, .jpeg" multiple>
                                                                <div class="error_msg" id="more_image_display"></div>
                                                                <span class="error_msg" id="more_imgs_editErr"></span>
                                                            </div>
                                                        </div>
                                                    </div> -->
                                                    <div class="row mb-4">
                                                            <div class="col-lg-12 col-sm-12">
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">
                                                                        Description</label>
                                                                        <textarea name="content"
                                                                        class="form-control" id="descriptionEditor_edit"></textarea>
                                                                        <textarea id="description_edit" name="description" style="display:none;"></textarea>
                                                                   
                                                                    <span class="error_msg"
                                                                        id="description_editErr"></span>
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
                                    <button class="btn btn-outline-danger" type="button"
                                        data-bs-dismiss="modal">Close</button>
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
                    <h1 class="h4">Manage Green Fund (Campaign)</h1>
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
                    Campaign</button>
                    <button class="btn btn-success mb-3" name="search" id="search"
                        onclick="updateActivationStatus('1')">&nbsp;Active</button>
                    <button class="btn btn-warning mb-3" onclick="updateActivationStatus('0')"><i class="fa fa-ban"
                            aria-hidden="true"></i>&nbsp;In Active</button>
                    <button class="btn btn-danger mb-3" onclick="commonDelete('1')"><i class="fa fa-trash"
                            aria-hidden="true" onclick=""></i>&nbsp;Delete</button>

                    <button class="btn btn-info mb-3" style="display:none;" id="restore_btn"
                        onclick="commonDelete('0')"><i class="fa fa-trash" aria-hidden="true"
                            onclick=""></i>&nbsp;Restore</button>
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
                                    <th>SKU</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Goal Amount</th>
                                    <th>Raised Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                    <th>State</th>
                                    <th>City</th>
                                    <th>Area</th>
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
        var treeUrl = "{{ route('campaign.index') }}";
        var treeStore = "{{ route('campaign.store') }}";
        var treeEdit = '{{ route('campaign.edit', ':id') }}';
        var getCity = '{{ route('getCities', ':id') }}';
        var treeUpdate = '{{ route('campaign.update', ':id') }}';
        var statusUpdate = '{{ route('campaign-status') }}';
        var deleteUpdate = '{{ route('campaign-delete') }}';
        var editorImgUrl = '{{ route('ckeditor.upload', ['_token' => csrf_token()]) }}';
    </script>
      <script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
      <script src="{{ asset('admin/js/ckeditorMyTree.js') }}"></script>

    <script src="{{ asset('admin/js/campaign.js') }}"></script>
    <script src="{{ asset('admin/js/commonUpdates.js') }}"></script>
@endsection
