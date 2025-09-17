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
            url: cityUrl,
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
                data: "name",
            },
            {
                data: "city",
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
                targets: [0, 4],
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
    $("#name,#state_id").css("border", "");
    $("#nameErr,#state_idErr").html("");

    var name = $("#name").val();
    var state_id = $("#state_id").val();
    // var main_img = $("#main_img").val();

    if (name == "") {
        $("#name").css("border", "1px solid red");
        $("#name").focus();
        $("#nameErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please enter name'
        );
        str = false;
    }

    if (state_id == "0") {
        $("#state_id").css("border", "1px solid red");
        $("#state_id").focus();
        $("#state_idErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please select city'
        );
        str = false;
    }

    // if (main_img == "") {
    //     $("#main_img").css("border", "1px solid red");
    //     $("#main_img").focus();
    //     $("#main_imgErr").html(
    //         '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please select image'
    //     );
    //     str = false;
    // }

    if (str) {
        // Loader.show();
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: cityStore,
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
    $("#name_edit,#state_id_edit").css("border", "");
    //$("#name_editErr,#state_id_editErr,#main_img_editErr,").html("");
    var url = cityEdit;
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
                $("#state_id_edit").val(response.data.state_id);

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
    $("#name_edit,#state_id_edit").css("border", "");
    $("#name_editErr,#state_id_editErr").html("");
    var url = cityUpdate;
    var update_id = $("#update_id").val();
    url = url.replace(":id", update_id);
    e.preventDefault();
    str = true;

    var name = $("#name_edit").val();
    var state_id = $("#state_id_edit").val();

    //var main_img = $("#main_img_edit").val();

    if (name == "") {
        $("#name_edit").css("border", "1px solid red");
        $("#name_edit").focus();
        $("#name_editErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please enter name'
        );
        str = false;
    }

    if (state_id == 0) {
        $("#state_id_edit").css("border", "1px solid red");
        $("#state_id_edit").focus();
        $("#state_id_editErr").html(
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

