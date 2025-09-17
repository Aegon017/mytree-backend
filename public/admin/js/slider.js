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
            url: sliderUrl,
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
                data: "title",
            },
            {
                data: "status",
            },

            {
                data: "actions",
            }
        ],
        columnDefs: [
            {
                targets: [0, 3],
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
        "#name,#main_image"
    ).css("border", "");
    $(
        "#nameErr,#main_imageErr"
    ).html("");

    var name = $("#name").val();
    var main_image = $("#main_image").val();

    
    if (name == "") {
        $("#name").css("border", "1px solid red");
        $("#name").focus();
        $("#nameErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please enter name'
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
    if (str) {
        // Loader.show();
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: sliderStore,
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
        "#name_edit,#main_image_edit"
    ).css("border", "");
    $(
        "#name_editErr,#main_image_editErr"
    ).html("");
    var url = sliderEdit;
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
                $("#name_edit").val(response.data.title);
                     
                $("#main_image_display").html(
                    '<img src="' +
                        response.data.main_image_url +
                        '" style="width:100px;height :100px" />'
                );
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
   
    var url = sliderUpdate;
    var update_id = $("#update_id").val();
    url = url.replace(":id", update_id);
    e.preventDefault();
    str = true;

    $(
        "#name_edit,#main_image_edit"
    ).css("border", "");
    $(
        "#name_editErr,#main_image_editErr"
    ).html("");

    var name = $("#name_edit").val();

    if (name == "") {
        $("#name_edit").css("border", "1px solid red");
        $("#name_edit").focus();
        $("#name_editErr").html(
            '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please enter name'
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

