@extends('Admin.layouts.admin_layout')
@section('title', 'Slider')
@section('content')


    <div class="container">
        <div id="flash-message">
        </div>
        <br />
        <!-- Add Item slider -->
        <div class="col-lg-4">
            <!-- slider Content -->
            <div class="modal fade" id="addItem" tabindex="-1" role="dialog" aria-labelledby="modal-form" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered  modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-body p-0">
                            <div class="card p-3 p-lg-4">
                                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                                <div class="text-center text-md-center mb-4 mt-md-0">
                                    <h1 class="mb-0 h4">Add slider</h1>
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
                                                                    <label for="exampleInputEmail1">Slider Title</label>
                                                                    <input type="text" name="title"
                                                                        class="form-control" id="name"
                                                                        placeholder="Enter title">
                                                                    <span class="error_msg" id="nameErr"></span>
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
                                    <h1 class="mb-0 h4">Update Slider</h1>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-12 mb-4">
                                            <div class="card border-0 shadow components-section">
                                                <div class="card-body">
                                                    
                                                    


                                                    
                                                    <div class="row mb-4">
                                                            <div class="col-lg-12 col-sm-12">
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">Slider Title</label>
                                                                    <input type="text" name="title"
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
                    <h1 class="h4">Manage Sliders</h1>
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
                        slider</button>
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
                                    <th>Title</th>
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
        var sliderUrl = "{{ route('sliders.index') }}";
        var sliderStore = "{{ route('sliders.store') }}";
        var sliderEdit = '{{ route('sliders.edit', ':id') }}';
        var sliderUpdate = '{{ route('sliders.update', ':id') }}';
        var statusUpdate = '{{ route('sliders-status') }}';
        var deleteUpdate = '{{ route('sliders-delete') }}';
    </script>
    <script src="{{ asset('admin/js/slider.js') }}"></script>
    <script src="{{ asset('admin/js/commonUpdates.js') }}"></script>
@endsection
