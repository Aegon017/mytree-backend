$(document).ready(function () {
    var table = itemDataTable();
    table.on("draw.dt", function () {
        var info = table.page.info();
    });
});
$("#trash").on("change", function (event) {
    $("#trash").val() == 1
        ? $("#restore_btn").show()
        : $("#restore_btn").hide();
    event.preventDefault();
    itemDataTable();
});

function itemDataTable($rows = 10) {
        var dataDisplay  =[
        {
            data: "srno",
        },
        {
            data: "sku",
        },
        {
            data: "name",
        },
        {
            data: "price",
        },
        {
            data: "quantity",
        },
        {
            data: "status",
        },
        {
            data: "actions",
        },
        {
            data: "category",
        }];
        var displayCount = [0, 6]
    
    return $(".serversideDatatableForItems").DataTable({
        //dom: 'frt',
        serverSide: true,
        processing: true,
        responsive: true,
        bDestroy: true,
        pageLength: $rows,
        ajax: {
            url: productUrl,
            data: function (d) {
                d.trash = $("#trash").val();
                d.type = $("#typeBased").val();
                d.productId = $("#productIdFilter").val();
                d.adoptedStatus = $("#adoptedStatus").val();
                
                
            },
        },
        order: [], //disable default sort ordering

        drawCallback: function () {},
        language: {},
        columns: dataDisplay,
        columnDefs: [
            {
                targets: displayCount,
                /* column index */
                orderable: false,
                /* true or false */
            },
        ],
    });
}
$("#store_item").on("submit", function (e) {
    e.preventDefault();
    str = true;
    $(
        "#name,#description,#main_image,#more_imgs,#category_id,#price"//,#discount_price
    ).css("border", "");
    $(
        "#nameErr,#descriptionErr,#main_imageErr,#more_imgsErr,#category_idErr,#priceErr"//,#discount_priceErr
    ).html("");

    var name = $("#name").val();
    var age = $("#age").val();
    // var description = $("#description").val();
    // var description = CKEDITOR.instances['descriptionEditor'].getData();
    var description = add_editorInstance.getData();
    $('#description').html(description);
    var price_info = $("#price_info").val();
    var more_imgs = $("#more_imgs").val();
    var main_image = $("#main_image").val();
    var category_id = $("#category_id").val();
    var price = $("#price").val();
    // var discount_price = $("#discount_price").val();

    
    if (name == "") {
        $("#name").css("border", "1px solid red");
        $("#name").focus();
        $("#nameErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please enter name'
        );
        str = false;
    }
    
    if (price_info == "") {
        $("#price_info").css("border", "1px solid red");
        $("#price_info").focus();
        $("#price_infoErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please  Price Info'
        );
        str = false;
    }
    if (description == "") {
        $("#description").css("border", "1px solid red");
        $("#description").focus();
        $("#descriptionErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please  Description'
        );
        str = false;
    }

    if (main_image == "") {
        $("#main_image").css("border", "1px solid red");
        $("#main_image").focus();
        $("#main_imageErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please select image'
        );
        str = false;
    }
    if (more_imgs == "") {
        $("#more_imgs").css("border", "1px solid red");
        $("#more_imgs").focus();
        $("#more_imgsErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please select image'
        );
        str = false;
    }
    if (category_id == "0") {
        $("#category_id").css("border", "1px solid red");
        $("#category_id").focus();
        $("#category_idErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please select category'
        );
        str = false;
    }

    if (price == "") {
        $("#price").css("border", "1px solid red");
        $("#price").focus();
        $("#priceErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please enter price'
        );
        str = false;
    }
    // if (discount_price == "") {
    //     $("#discount_price").css("border", "1px solid red");
    //     $("#discount_price").focus();
    //     $("#discount_priceErr").html(
    //         '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please enter price'
    //     );
    //     str = false;
    // }

    if (str) {
        // Loader.show();
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: productStore,
            data: new FormData(this),
            dataType: "json",
            type: "POST",
            contentType: false,
            cache: false,
            processData: false,
            success: function (response) {
                if (response.code == 201) {
                    $("#addItem").modal("hide");
                    itemDataTable();
                    $("#store_item")[0].reset();
                    showAlertMessage(response.message, "success");
                } else {
                    showAlertMessage(response.message, "danger");
                }
                // Loader.hide();
            },
            error: function (response, data) {
                // Loader.hide();
                $("#addItem").modal("hide");
                showAlertMessage(response.responseJSON.message, "danger");
            },
        });
    }
});

function editItem(id) {
    // resetCkEditor();
   
    var url = productEdit;
    url = url.replace(":id", id);
    $.ajax({
        url: url,
        dataType: "json",
        type: "GET",
        contentType: false,
        cache: false,
        processData: false,
        success: function (response) {
            // Loader.hide();
            if (response.code == 200) {
                console.log(response.data);

                $("#editItem").modal("show");
                $("#update_id").val(response.data.id);
                $("#name_edit").val(response.data.name);
                $("#botanical_name_edit").val(response.data.botanical_name);
                $("#nick_name_edit").val(response.data.nick_name);
                $("#quantity_edit").val(response.data.quantity);
                $("#price_edit").val(response.data.price);
                // $("#discount_price_edit").val(response.data.discount_price);
                $("#description_edit").val(
                    response.data.description
                );
                updateEditorContent(response.data.description)
                $("#main_image_display").html(
                    '<img src="' +
                        response.data.main_image_url +
                        '" style="width:100px;height :100px" />'
                );
                $("#more_image_display").empty();
                for (var i = 0; i < response.data.images.length; i++) {
                    // $("#more_image_display").append(
                    //     '<img src="' +
                    //         response.data.images[i].image_url +
                    //         '" style="width:100px;height :100px;float:left" />'
                    // );
                    $("#more_image_display").append(`
                        <div class="position-relative d-inline-block m-1" id="image_wrap_${response.data.images[i].id}">
                            <img src="${response.data.images[i].image_url}" 
                                 style="width:100px; height:100px; border: 1px solid #ccc; border-radius:5px;" />
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0"
                                    onclick="deleteProductImage(${response.data.images[i].id})"
                                    style="padding:2px 6px; font-size:12px; z-index:10;">
                                &times;
                            </button>
                        </div>
                    `);
                }               
                $("#category_id_edit").val(response.data.category_id);
                
                $("#update_id").val(response.data.id);
            } else {
                showAlertMessage(response.message, "danger");
            }
        },
        error: function (response, data) {
            showAlertMessage(response.responseJSON.message, "danger");
            // Loader.hide();
        },
    });
}

function deleteProductImage(imageId) {
    if (!confirm('Are you sure you want to delete this image?')) return;

    $.ajax({
        url: imageDeleteUrl,
        type: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
            image_id: imageId,
        },
        success: function (response) {
            if (response.code === 200) {
                $("#image_wrap_" + imageId).remove();
                alert(response.message);
                showAlertMessage(response.message, "success");
            } else {
                showAlertMessage(response.message, "danger");
            }
        },
        error: function (xhr) {
            showAlertMessage("Something went wrong!", "danger");
        }
    });
}


$("#update_item").on("submit", function (e) {
   
    var url = productUpdate;
    var update_id = $("#update_id").val();
    url = url.replace(":id", update_id);
    e.preventDefault();
    str = true;

    $(
        "#name_edit,#description_edit,#main_image_edit,#more_imgs_edit,#category_id_edit,#price_edit"//#discount_price_edit
    ).css("border", "");
    $(
        "#name_editErr,#description_editErr,#main_image_editErr,#more_imgs_editErr,#category_id_editErr,#price_editErr"//#discount_price_editErr
    ).html("");

    var name = $("#name_edit").val();
    var age = $("#age_edit").val();
    // var description = CKEDITOR.instances['descriptionEditor_edit'].getData();
    // //$("#description").val();
    // $('#description_edit').html(description);
    // var description = CKEDITOR.instances['descriptionEditor_edit'].getData();
    var description = edit_editorInstance.getData();
$('#description_edit').val(description); // For <textarea>
$('#description_edit').html(description); // For <div>

    // var more_imgs = $("#more_imgs").val();
    // var main_image = $("#main_image").val();
    var category_id = $("#category_id_edit").val();
    var price = $("#price_edit").val();
    // var discount_price = $("#discount_price_edit").val();

    
    if (name == "") {
        $("#name_edit").css("border", "1px solid red");
        $("#name_edit").focus();
        $("#name_editErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please enter name'
        );
        str = false;
    }
    if (description == "") {
        $("#description_edit").css("border", "1px solid red");
        $("#description_edit").focus();
        $("#description_editErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please  Description'
        );
        str = false;
    }
    // if (main_image == "") {
    //     $("#main_image").css("border", "1px solid red");
    //     $("#main_image").focus();
    //     $("#main_imageErr").html(
    //         '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please select image'
    //     );
    //     str = false;
    // }
    // if (more_imgs == "") {
    //     $("#more_imgs").css("border", "1px solid red");
    //     $("#more_imgs").focus();
    //     $("#more_imgsErr").html(
    //         '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please select image'
    //     );
    //     str = false;
    // }
    if (category_id == "0") {
        $("#category_id_edit").css("border", "1px solid red");
        $("#category_id_edit").focus();
        $("#category_id_editErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please select category'
        );
        str = false;
    }
    if (price == "") {
        $("#price_edit").css("border", "1px solid red");
        $("#price_edit").focus();
        $("#price_editErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please enter price'
        );
        str = false;
    }
    // if (discount_price == "") {
    //     $("#discount_price_edit").css("border", "1px solid red");
    //     $("#discount_price_edit").focus();
    //     $("#discount_price_editErr").html(
    //         '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please enter price'
    //     );
    //     str = false;
    // }

    if (str) {
        // Loader.show();

        var data = new FormData(this);

        var form = $("#update_item");
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: url,
            data: data,
            contentType: false,
            cache: false,
            processData: false,
            dataType: "json",
            type: "POST",
            success: function (response) {
                if (response.code == 201) {
                    $("#editItem").modal("hide");
                    itemDataTable();
                    showAlertMessage(response.message, "success");
                    $("#update_item")[0].reset();
                } else {
                    showAlertMessage(response.message, "danger");
                }
                // Loader.hide();
            },
            error: function (response, data) {
                // Loader.hide();
                $("#editItem").modal("hide");
                showAlertMessage(response.responseJSON.message, "danger");
            },
        });
    }
});
$("#multiAction").click(function () {
    if ($("#multiAction").is(":checked")) {
        $("#multiAction").prop("checked", true);
        $('[name="multiple[]"]').prop("checked", true);
    } else {
        $("#multiAction").prop("checked", false);
        $('[name="multiple[]"]').prop("checked", false);
    }
});
function addItemModel() {
    $("#store_item").trigger("reset");
    $("#addItem").modal("toggle");
}