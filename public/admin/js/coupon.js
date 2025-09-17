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
            url: couponUrl,
            data: function (d) {
                d.trash = $("#trash").val();
            },
        },
        order: [], //disable default sort ordering

        drawCallback: function () {},
        language: {},
        columns: [
            { data: "srno" },
            { data: "code" },
            { data: "type" },
            { data: "discount_value" },
            { data: "usage_limit" },
            { data: "used_count" },
            { data: "valid_from" },
            { data: "valid_to" },
            { data: "status" },
            {
                data: "actions",
            },
        ],
        columnDefs: [
            {
                targets: [0, 9],
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
    $("#code,#type,#discount_value,#usage_limit,#valid_from,#valid_to").css("border", "");
    $("#codeErr,#typeErr,#discount_valueErr,#usage_limitErr,#valid_fromErr,#valid_toErr").html("");

    var code = $("#code").val();
    var type = $("#type").val();
    var discount_value = $("#discount_value").val();
    var usage_limit = $("#usage_limit").val();
    var valid_from = $("#valid_from").val();
    var valid_to = $("#valid_to").val();
    
    if (code == "") {
        $("#code").css("border", "1px solid red");
        $("#codeErr").html(
            "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Please enter code"
        );
        str = false;
    }
    if(type == "") {
        $("#type").css("border", "1px solid red");
        $("#typeErr").html(
            "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Please enter type"
        );
        str = false;
    }
    if(discount_value == "") {
        $("#discount_value").css("border", "1px solid red");
        $("#discount_valueErr").html(
            "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Please enter discount value"
        );
        str = false;
    }
    if(usage_limit == "") {
        $("#usage_limit").css("border", "1px solid red");
        $("#usage_limitErr").html(
            "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Please enter usage limit"
        );
        str = false;
    }
    if(valid_from == "") {
        $("#valid_from").css("border", "1px solid red");
        $("#valid_fromErr").html(
            "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Please enter valid from"
        );
        str = false;
    }
    if(valid_to == "") {
        $("#valid_to").css("border", "1px solid red");
        $("#valid_toErr").html(
            "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Please enter valid to"
        );
        str = false;
    }
    // if (valid_from < valid_to) {
    //         $("#valid_from").css("border", "1px solid red");
    //         $("#valid_fromErr").html(
    //             "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Please enter valid from"
    //         );
    //         str = false;
    //     } else {
    //         if (valid_from < new Date().toISOString().split("T")[0]) {
    //             $("#valid_from").css("border", "1px solid red");
    //             $("#valid_fromErr").html(
    //                 "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Please enter valid from"
    //             );
    //             str = false;    
    //        }
    // }
    if (str) {
        // Loader.show();
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: couponStore,
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
    var url = couponEdit;
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
                $("#code_edit").val(response.data.code);
                $("#type_edit").val(response.data.type);
                $("#discount_value_edit").val(response.data.discount_value);
                $("#usage_limit_edit").val(response.data.usage_limit);
                $("#valid_from_edit").val(response.data.valid_from);
                $("#valid_to_edit").val(response.data.valid_to);
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
    var url = couponUpdate;
    var update_id = $("#update_id").val();
    url = url.replace(":id", update_id);
    e.preventDefault();
    str = true;

    $("#code_edit,#type_edit,#discount_value_edit,#usage_limit_edit,#valid_from_edit,#valid_to_edit").css("border", "");
    $("#code_editErr,#type_editErr,#discount_value_editErr,#usage_limit_editErr,#valid_from_editErr,#valid_to_editErr").html("");

    var code = $("#code_edit").val();
    var type = $("#type_edit").val();
    var discount_value = $("#discount_value_edit").val();
    var usage_limit = $("#usage_limit_edit").val();
    var valid_from = $("#valid_from_edit").val();
    var valid_to = $("#valid_to_edit").val();
    
    if (code == "") {
        $("#code_edit").css("border", "1px solid red");
        $("#code_editErr").html(
            "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Please enter code"
        );
        str = false;
    }
    if(type == "") {
        $("#type_edit").css("border", "1px solid red");
        $("#type_editErr").html(
            "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Please enter type"
        );
        str = false;
    }
    if(discount_value == "") {
        $("#discount_value_edit").css("border", "1px solid red");
        $("#discount_value_editErr").html(
            "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Please enter discount value"
        );
        str = false;
    }
    if(usage_limit == "") {
        $("#usage_limit_edit").css("border", "1px solid red");
        $("#usage_limit_editErr").html(
            "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Please enter usage limit"
        );
        str = false;
    }
    if(valid_from == "") {
        $("#valid_from_edit").css("border", "1px solid red");
        $("#valid_from_editErr").html(
            "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Please enter valid from"
        );
        str = false;
    }
    if(valid_to == "") {
        $("#valid_to_edit").css("border", "1px solid red");
        $("#valid_to_editErr").html(
            "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Please enter valid to"
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