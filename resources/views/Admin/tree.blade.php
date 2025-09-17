@extends('Admin.layouts.admin_layout')
@section('title', $typeName)
@push('styles')
    <style>
        .harish-price{
    float: left;
    margin-left:8px;
}
.harish-price-top{
    float: left;
    margin-left:8px;
    margin-top:24px;
}
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
        <input type="hidden"  class="form-control" id="typeBased"  value="{{$typeId}}">
        <input type="hidden"  class="form-control" id="treeIdFilter"  value="{{$treeId}}">
        <input type="hidden"  class="form-control" id="adoptedStatus"  value="{{$adoptedStatus}}">
        
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
                                    <h1 class="mb-0 h4">Add Tree</h1>
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
                                                                    <label for="exampleInputEmail1">Tree Name</label>
                                                                    <input type="text" name="name"
                                                                        class="form-control" id="name"
                                                                        placeholder="Enter Tree name">
                                                                    <span class="error_msg" id="nameErr"></span>
                                                                </div>
                                                                <input type="hidden" name="type" class="form-control" id="type"  value="{{$typeId}}">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-4">
                                                            <div class="col-lg-6 col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">Quantity</label>
                                                                    <input type="text" name="quantity"
                                                                        class="form-control" id="quantity"
                                                                        placeholder="Enter tree quantity">
                                                                    <span class="error_msg" id="quantityErr"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">Age</label>
                                                                    <input type="number" name="age"
                                                                        class="form-control" id="age"
                                                                        placeholder="Enter age">
                                                                    <span class="error_msg" id="ageErr"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @if($typeId!=1)
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
                                                                        <select class="form-select" id="area_id"
                                                                            name ="area_id" aria-label="Default select example">
                                                                            <option value="0">Select Area</option>
                                                                        </select>
                                                                        <span class="error_msg" id="area_idErr"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @endif
                                                        <div class="row mb-4">
                                                            <!-- <div class="col-lg-6 col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">Price</label>
                                                                    <input type="number" name="price"
                                                                        class="form-control" id="price"
                                                                        placeholder="Enter price">
                                                                    <span class="error_msg" id="priceErr"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">Discount Price</label>
                                                                    <input type="number" name="discount_price"
                                                                        class="form-control" id="discount_price"
                                                                        placeholder="Enter discount price">
                                                                    <span class="error_msg" id="discount_priceErr"></span>
                                                                </div>
                                                            </div> -->


                                                            <div id="price-fields">
                                                                <div class="price-entry">
                                                                <div class="col-lg-3 col-sm-3 harish-price">
                                                                    <div class="form-group">
                                                                        <label for="duration">Duration (in years):</label>
                                                                        <select class="form-control" name="durations[]" required>
                                                                            <option value="1">1 Year</option>
                                                                            <option value="2">2 Years</option>
                                                                            <option value="3">3 Years</option>
                                                                            <option value="4">4 Years</option>
                                                                            <option value="5">5 Years</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-3 col-sm-3 harish-price">
                                                                    <div class="form-group">
                                                                        <label for="price">Price:</label>
                                                                        <input class="form-control" type="number" name="prices[]" required>
                                                                    </div>
                                                                </div>
                                                                    <div class="col-lg-3 col-sm-3 harish-price-top">
                                                                        <button type="button"  class="btn btn-primary" onclick="addPriceField(1)">+</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                       


                                                        <div class="row mb-4">
                                                            <div class="col-lg-6 col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">Image</label>
                                                                    <input type="file" name="main_image"
                                                                        class="form-control-file" id="main_image"
                                                                        accept=".png, .jpg, .jpeg">
                                                                    <span class="error_msg" id="main_imageErr"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">More Images</label>
                                                                    <input type="file" name="more_imgs[]"
                                                                        class="form-control-file" id="more_imgs"
                                                                        accept=".png, .jpg, .jpeg" multiple>
                                                                    <span class="error_msg" id="more_imgsErr"></span>
                                                                </div>
                                                            </div>
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
                                                        <div class="row mb-4">
                                                            <div class="col-lg-12 col-sm-12">
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">
                                                                        Price Info</label>
                                                                        <textarea name="price_info"
                                                                        class="form-control" id="price_info"></textarea>
                                                                   
                                                                    <span class="error_msg"
                                                                        id="price_infoErr"></span>
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
        <div id="editItem" class="modal fade" role="dialog" style="margin-top: 106px">
            <div class="modal-dialog modal-dialog-centered modal-lg">

                <!-- Tree content-->
                <div class="modal-content">
                    <form id="update_item" method="post" enctype="multipart/form-data">
                        <div class="modal-body p-0">
                            <div class="card p-3 p-lg-4">
                                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                                <div class="text-center text-md-center mb-4 mt-md-0">
                                    <h1 class="mb-0 h4">Update Tree</h1>
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
                                                                    <label for="exampleInputEmail1">Tree Name</label>
                                                                    <input type="text" name="name"
                                                                        class="form-control" id="name_edit"
                                                                        placeholder="Enter Tree name">
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
                                                                    <label for="exampleInputEmail1">Quantity</label>
                                                                    <input type="text" name="quantity"
                                                                        class="form-control" id="quantity_edit"
                                                                        placeholder="Enter tree quantity">
                                                                    <span class="error_msg" id="quantity_editErr"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">Age</label>
                                                                    <input type="number" name="age"
                                                                        class="form-control" id="age_edit"
                                                                        placeholder="Enter age">
                                                                    <span class="error_msg" id="age_editErr"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @if($typeId!=1)
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
                                                                <select class="form-select" id="area_id_edit"
                                                                    name ="area_id" aria-label="Default select example">
                                                                    <option value="0">Select Area</option>
                                                                </select>
                                                                <input type="hidden" name="id" class="form-control"
                                                                    id="update_id">
                                                                <input type="hidden" name="_method" value="PUT">
                                                                <span class="error_msg" id="area_id_editErr"></span>
                                                            </div>
                                                            </div>
                                                        </div>
                                                        @endif
                                                        <div class="row mb-4">
                                                            <div id="price-fields-edit">

                                                            </div>
                                                            <div class="col-lg-3 col-sm-3 harish-price-top">
                                                                        <button type="button"  class="btn btn-primary" onclick="addPriceField(2)">+</button>
                                                            </div>
                                                            <!-- <div class="col-lg-6 col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">Price</label>
                                                                    <input type="number" name="price"
                                                                        class="form-control" id="price_edit"
                                                                        placeholder="Enter price">
                                                                    <span class="error_msg" id="price_editErr"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6 col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">Discount Price</label>
                                                                    <input type="number" name="discount_price"
                                                                        class="form-control" id="discount_price_edit"
                                                                        placeholder="Enter discount price">
                                                                    <span class="error_msg" id="discount_price_editErr"></span>
                                                                </div>
                                                            </div> -->

                                                            <!-- <div id="price-fields">
                                                                <div class="price-entry">
                                                                    <label for="duration">Duration (in years):</label>
                                                                    <select name="durations[]" required>
                                                                        <option value="1">1 Year</option>
                                                                        <option value="2">2 Years</option>
                                                                        <option value="3">3 Years</option>
                                                                        <option value="5">5 Years</option>
                                                                        <option value="10">10 Years</option>
                                                                    </select>
                                                                    <label for="price">Price:</label>
                                                                    <input type="text" name="prices[]" required>
                                                                    <button type="button" onclick="addPriceField()">+</button>
                                                                </div>
                                                            </div>

                                                            <button type="submit">Add Prices</button> -->
                                                        </div>
                                                    


                                                    <div class="row mb-4">

                                                    </div>

                                                    <div class="row mb-4">
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
                                                    </div>
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

                                                        <div class="row mb-4">
                                                            <div class="col-lg-12 col-sm-12">
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">
                                                                        Price Info</label>
                                                                        <textarea name="price_info"
                                                                        class="form-control" id="price_info_edit"></textarea>
                                                                   
                                                                    <span class="error_msg"
                                                                        id="price_info_editErr"></span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                    <button class="btn btn-outline-danger" type="button"
                                        data-bs-dismiss="modal">Close</button>

                                </div>

                                <div class="modal-footer">

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
                    @if($typeId==1)
                        <h1 class="h4">Manage Trees</h1>
                    @else
                        <h1 class="h4">Manage {{$typeName}} Trees</h1>
                    @endif
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
                    @if(empty($adoptedStatus))
                    <button type="button" class="btn btn-block btn-gray-800 mb-3" onclick="addItemModel()">Add
                        Tree</button>
                    
                    <button class="btn btn-success mb-3" name="search" id="search"
                        onclick="updateActivationStatus('1')">&nbsp;Active</button>
                    <button class="btn btn-warning mb-3" onclick="updateActivationStatus('0')"><i class="fa fa-ban"
                            aria-hidden="true"></i>&nbsp;In Active</button>
                    <button class="btn btn-danger mb-3" onclick="commonDelete('1')"><i class="fa fa-trash"
                            aria-hidden="true" onclick=""></i>&nbsp;Delete</button>

                    <button class="btn btn-info mb-3" style="display:none;" id="restore_btn"
                        onclick="commonDelete('0')"><i class="fa fa-trash" aria-hidden="true"
                            onclick=""></i>&nbsp;Restore</button>
                    @endif
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
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                    @if($typeId!=1)
                                    <th>State</th>
                                    <th>City</th>
                                    <th>Area</th>
                                    @endif
                                </tr>
                            </thead>

                        </table>
                    </div>
                </div>
            </div>


        </div>
    </div>
    <!-- <textarea id="editor" name="content"></textarea> -->
@endsection
@section('script')
    @parent
    <script>
        var treeUrl = "{{ route('tree.index') }}";
        var treeStore = "{{ route('tree.store') }}";
        var treeEdit = '{{ route('tree.edit', ':id') }}';
        var getCity = '{{ route('getCities', ':id') }}';
        var treeUpdate = '{{ route('tree.update', ':id') }}';
        var statusUpdate = '{{ route('tree-status') }}';
        var deleteUpdate = '{{ route('tree-delete') }}';
        var getArea = '{{ route('area', ':id') }}';
        var editorImgUrl = '{{ route('ckeditor.upload', ['_token' => csrf_token()]) }}';
        var imageDeleteUrl ="{{ route('tree.image.delete') }}";
    </script>
    <script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
    <script src="{{ asset('admin/js/ckeditorMyTree.js') }}"></script>

    <script src="{{ asset('admin/js/tree.js') }}"></script>
    <script src="{{ asset('admin/js/commonUpdates.js') }}"></script>
    
    <!-- Summernote CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">

<!-- Summernote JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>


@endsection
