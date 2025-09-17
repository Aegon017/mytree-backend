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
    return $(".serversideDatatableForItems").DataTable({
        //dom: 'frt',
        serverSide: true,
        processing: true,
        responsive: true,
        bDestroy: true,
        pageLength: $rows,
        ajax: {
            url: locationUrl,
            data: function (d) {
                d.trash = $("#trash").val();
            },
        },
        order: [], //disable default sort ordering

        drawCallback: function () {},
        language: {},
        columns: [
            {
                data: "srno",
            },
            {
                data: "area",
            },
            {
                data: "city",
            },
            {
                data: "state",
            },
            {
                data: "status",
            },

            {
                data: "actions",
            },
        ],
        columnDefs: [
            {
                targets: [0, 5],
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
    $("#city_id,#state_id,#area_id").css("border", "");
    $("#city_idErr,#state_idErr,#area_idErr").html("");

    var state_id = $("#state_id").val();
    var city_id = $("#city_id").val();
    var area_id = $("#area_id").val();
    // var main_img = $("#main_img").val();

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
    if (area_id == "0") {
        $("#area_id").css("border", "1px solid red");
        $("#area_id").focus();
        $("#area_idErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please select area'
        );
        str = false;
    }


    if (str) {
        // Loader.show();
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: locationStore,
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
    $("#name_edit,#city_id_edit").css("border", "");
    //$("#name_editErr,#city_id_editErr,#main_img_editErr,").html("");
    var url = locationEdit;
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
                getCities(response.data.state_id, 1, response.data.city_id);
                getAreas(response.data.city_id, 1, response.data.area_id);
                $("#state_id_edit").val(response.data.state_id);
                $("#city_id_edit").val(response.data.city_id);
                $("#area_id_edit").val(response.data.area_id);
                // $("#main_img_display").html(
                //     '<img src="' +
                //         response.data.main_img_url +
                //         '" style="width:100px;height :100px" />'
                // );

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

$("#update_item").on("submit", function (e) {
    $("#state_edit,#area_edit,#city_id_edit").css("border", "");
    $("#state_editErr,#area_editErr,#city_id_editErr").html("");
    var url = locationUpdate;
    var update_id = $("#update_id").val();
    url = url.replace(":id", update_id);
    e.preventDefault();
    str = true;

    var state = $("#state_edit").val();
    var area = $("#area_edit").val();
    var city_id = $("#city_id_edit").val();

    //var main_img = $("#main_img_edit").val();
    if (state == 0) {
        $("#state_edit").css("border", "1px solid red");
        $("#state_edit").focus();
        $("#state_editErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please enter state'
        );
        str = false;
    }
    if (area == 0) {
        $("#area_edit").css("border", "1px solid red");
        $("#area_edit").focus();
        $("#area_editErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please enter area'
        );
        str = false;
    }

    if (city_id == 0) {
        $("#city_id_edit").css("border", "1px solid red");
        $("#city_id_edit").focus();
        $("#city_id_editErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please select city'
        );
        str = false;
    }

    // if (main_img == "") {
    //     $("#main_img_edit").css("border", "1px solid red");
    //     $("#main_img_edit").focus();
    //     $("#main_img_editErr").html(
    //         '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please select image'
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