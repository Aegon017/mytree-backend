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
    if(treeTypeIdValidations != 1){
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
            data: "quantity",
        },
        {
            data: "status",
        },

        {
            data: "actions",
        },
        {
            data: "state",
        },
        {
            data: "city",
        },
        {
            data: "area",
        }];
        var displayCount = [0, 8]
    }else{
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
                data: "quantity",
            },
            {
                data: "status",
            },
    
            {
                data: "actions",
            }
            ];
            var displayCount = [0, 5]
    }
    return $(".serversideDatatableForItems").DataTable({
        //dom: 'frt',
        serverSide: true,
        processing: true,
        responsive: true,
        bDestroy: true,
        pageLength: $rows,
        ajax: {
            url: treeUrl,
            data: function (d) {
                d.trash = $("#trash").val();
                d.type = $("#typeBased").val();
                d.treeId = $("#treeIdFilter").val();
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
var treeTypeIdValidations = $("#typeBased").val();
$("#store_item").on("submit", function (e) {
    e.preventDefault();
    str = true;
    $(
        "#name,#age,#description,#price_info,#main_image,#more_imgs,#state_id,#city_id,#area_id,#price,#discount_price"
    ).css("border", "");
    $(
        "#nameErr,#ageErr,#descriptionErr,#price_infoErr,#main_imageErr,#more_imgsErr,#state_idErr,#city_idErr,#areaErr,#priceErr,#discount_priceErr"
    ).html("");

    var name = $("#name").val();
    var age = $("#age").val();
    // var description = $("#description").val();
    var description = add_editorInstance.getData();
    $('#description').html(description);
    var price_info = $("#price_info").val();
    var more_imgs = $("#more_imgs").val();
    var main_image = $("#main_image").val();
    var state_id = $("#state_id").val();
    var city_id = $("#city_id").val();
    var area = $("#area_id").val();
    var price = $("#price").val();
    var discount_price = $("#discount_price").val();

    
    if (name == "") {
        $("#name").css("border", "1px solid red");
        $("#name").focus();
        $("#nameErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please enter name'
        );
        str = false;
    }
    if (age == "") {
        $("#age").css("border", "1px solid red");
        $("#age").focus();
        $("#ageErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please enter age'
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
if(treeTypeIdValidations != 1){
    if (state_id == "0") {
        $("#state_id").css("border", "1px solid red");
        $("#state_id").focus();
        $("#state_idErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please select state'
        );
        str = false;
    }
    if (city_id == "0") {
        $("#city_id").css("border", "1px solid red");
        $("#city_id").focus();
        $("#city_idErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please select city'
        );
        str = false;
    }
    if (area == "0") {
        $("#area_id").css("border", "1px solid red");
        $("#area_id").focus();
        $("#area_idErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please select area'
        );
        str = false;
    }
}
    // if (price == "") {
    //     $("#price").css("border", "1px solid red");
    //     $("#price").focus();
    //     $("#priceErr").html(
    //         '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please enter price'
    //     );
    //     str = false;
    // }
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
            url: treeStore,
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
    $(
        "#name_edit,#age_edit,#description_edit,#main_image_edit,#more_imgs_edit,#state_id_edit,#city_id_edit,#area_id_edit,#price_edit,#discount_price_edit"
    ).css("border", "");
    $(
        "#name_editErr,#age_editErr,#description_editErr,#main_image_editErr,#more_imgs_editErr,#state_id_editErr,#city_id_editErr,#area_id_editErr,#price_editErr,#discount_price_editErr"
    ).html("");
    var url = treeEdit;
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
                $("#age_edit").val(response.data.age);
                $("#quantity_edit").val(response.data.quantity);
                // $("#price_edit").val(response.data.price);
                // $("#discount_price_edit").val(response.data.discount_price);
                $("#description_edit").val(
                    response.data.description
                );
                updateEditorContent(response.data.description)
                $("#price_info_edit").val(
                    response.data.price_info
                );
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

                $("#price-fields-edit").empty();
                for (var i = 0; i < response.data.price.length; i++) {
                    $("#price-fields-edit").append(
                        `<div class="price-entry"><div class="col-lg-3 col-sm-3 harish-price">
        <div class="form-group">
        <label for="duration">Duration (in years):</label>
        <select class="form-control" name="durations[]"  id="durations_edit_`+i+`" required>
            <option value="1">1 Year</option>
            <option value="2">2 Years</option>
            <option value="3">3 Years</option>
            <option value="4">4 Years</option>
            <option value="5">5 Years</option>
        </select></div></div>
        <div class="col-lg-3 col-sm-3 harish-price">
        <div class="form-group">
        <label for="price">Price:</label>
        <input class="form-control" type="number" name="prices[]" value="`+response.data.price[i].price+`" required></div></div>
        <div class="col-lg-3 col-sm-3 harish-price-top"><button type="button" class="btn btn-danger"  onclick="removePriceFieldEdit(this)">-</button></div></div>`
                    );
                $("#durations_edit_"+i).val(response.data.price[i].duration);
                }
                if(treeTypeIdValidations != 1){
                $("#state_id_edit").val(response.data.state_id);
                
                getCities(response.data.state_id, 1, response.data.city_id);
                $("#city_id_edit").val(response.data.city_id);
                getAreas(response.data.city_id, 1, response.data.area_id);
                $("#area_id_edit").val(response.data.area_id);

                }
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
   
    var url = treeUpdate;
    var update_id = $("#update_id").val();
    url = url.replace(":id", update_id);
    e.preventDefault();
    str = true;

    $(
        "#name_edit,#age_edit,#description_edit,#price_info_edit,#main_image_edit,#more_imgs_edit,#state_id_edit,#city_id_edit,#area_id_edit,#price_edit,#discount_price_edit"
    ).css("border", "");
    $(
        "#name_editErr,#age_editErr,#description_editErr,#price_info_editErr,#main_image_editErr,#more_imgs_editErr,#state_id_editErr,#city_id_editErr,#area_id_editErr,#price_editErr,#discount_price_editErr"
    ).html("");

    var name = $("#name_edit").val();
    var age = $("#age_edit").val();
  
    var description = edit_editorInstance.getData();
$('#description_edit').val(description); // For <textarea>
$('#description_edit').html(description); // For <div>

    // var more_imgs = $("#more_imgs").val();
    // var main_image = $("#main_image").val();
    var state_id = $("#state_id_edit").val();
    var city_id = $("#city_id_edit").val();
    var area = $("#area_id_edit").val();
    var price = $("#price_edit").val();
    var discount_price = $("#discount_price_edit").val();

    
    if (name == "") {
        $("#name_edit").css("border", "1px solid red");
        $("#name_edit").focus();
        $("#name_editErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please enter name'
        );
        str = false;
    }
    if (age == "") {
        $("#age_edit").css("border", "1px solid red");
        $("#age_edit").focus();
        $("#age_editErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please enter age'
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
    if (price_info == "") {
        $("#price_info_edit").css("border", "1px solid red");
        $("#price_info_edit").focus();
        $("#price_info_editErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please  Price Info'
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
    if(treeTypeIdValidations != 1){
    if (state_id == "0") {
        $("#state_id_edit").css("border", "1px solid red");
        $("#state_id_edit").focus();
        $("#state_id_editErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please select state'
        );
        str = false;
    }
    if (city_id == "0") {
        $("#city_id_edit").css("border", "1px solid red");
        $("#city_id_edit").focus();
        $("#city_id_editErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please select city'
        );
        str = false;
    }
    if (area == "") {
        $("#area_id_edit").css("border", "1px solid red");
        $("#area_id_edit").focus();
        $("#area_id_editErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please select area'
        );
        str = false;
    }
}
    if (price == "") {
        $("#price_edit").css("border", "1px solid red");
        $("#price_edit").focus();
        $("#price_editErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please enter price'
        );
        str = false;
    }
    if (discount_price == "") {
        $("#discount_price_edit").css("border", "1px solid red");
        $("#discount_price_edit").focus();
        $("#discount_price_editErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please enter price'
        );
        str = false;
    }

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

$("#state_id").on("change", function (event) {
    var stateId = $("#state_id").val();
    getCities(stateId);
});
$("#state_id_edit").on("change", function (event) {
    var stateId = $("#state_id_edit").val();
    getCities(stateId, 1);
});


$("#city_id").on("change", function (event) {
    var cityId = $("#city_id").val();
    getAreas(cityId);
});
$("#city_id_edit").on("change", function (event) {
    var cityId = $("#city_id_edit").val();
    getAreas(cityId, 1);
});

function getCities(stateId, edit, cityId = "") {
    var cityOptionId = "#city_id";
    if (edit) {
        cityOptionId = "#city_id_edit";
    }
    $('select[name="city_id"]').empty();
    $('select[name="city_id"]').append(
        '<option value="0">Please select city</option>'
    );
    var url = getCity;
    url = url.replace(":id", stateId);
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
                if (response.data.length > 0) {
                    for (var i = 0; i < response.data.length; i++) {
                        $(cityOptionId).append(
                            '<option value="' +
                                response.data[i].id +
                                '">' +
                                response.data[i].name +
                                "</option>"
                        );
                    }
                    if (edit) {
                        $("#city_id_edit").val(cityId);
                    }
                }
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
function getAreas(cityId, edit, areaId = "") {
    var areaOptionId = "#area_id";
    if (edit) {
        areaOptionId = "#area_id_edit";
    }
    $('select[name="area_id"]').empty();
    $('select[name="area_id"]').append(
        '<option value="0">Please select area</option>'
    );
    var url = getArea;
    url = url.replace(":id", cityId);
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
                if (response.data.length > 0) {
                    for (var i = 0; i < response.data.length; i++) {
                        $(areaOptionId).append(
                            '<option value="' +
                                response.data[i].id +
                                '">' +
                                response.data[i].name +
                                "</option>"
                        );
                    }
                    if (edit) {
                        $("#area_id_edit").val(areaId);
                    }
                }
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


function addPriceField(type) {
    // Create a new div element for additional duration and price fields
    let typeClass = type==2 ? '-edit': '' ;
    const newEntry = document.createElement("div");
    newEntry.classList.add("price-entry"+typeClass);

    // Add HTML for the dropdown and price inputs
    newEntry.innerHTML = `<div class="col-lg-3 col-sm-3 harish-price">
        <div class="form-group">
        <label for="duration">Duration (in years):</label>
        <select class="form-control" name="durations[]" required>
            <option value="1">1 Year</option>
            <option value="2">2 Years</option>
            <option value="3">3 Years</option>
            <option value="4">4 Years</option>
            <option value="5">5 Years</option>
        </select></div></div>
        <div class="col-lg-3 col-sm-3 harish-price">
        <div class="form-group">
        <label for="price">Price:</label>
        <input class="form-control" type="number" name="prices[]" required></div></div>
        <div class="col-lg-3 col-sm-3 harish-price-top"><button type="button" class="btn btn-danger"  onclick="removePriceField(this)">-</button></div>
    `;

    // Append the new entry to the price-fields div
    document.getElementById("price-fields"+typeClass).appendChild(newEntry);
}

function removePriceField(button) {
    // Remove the specific price-entry div when "-" button is clicked
    button.parentElement.parentElement.remove();
}
function removePriceFieldEdit(button) {
    // Remove the specific price-entry div when "-" button is clicked
    button.parentElement.parentElement.remove();
}