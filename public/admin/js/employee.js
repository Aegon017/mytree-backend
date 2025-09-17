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
            url: employeeUrl,
            data: function (d) {
                d.trash = $("#trash").val();
                d.role = $("#roleBased").val();
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
                data: "empId",
            },
            {
                data: "name",
            },
            {
                data: "email",
            },
            {
                data: "mobile",
            },

            {
                data: "login_at",
            },
            {
                data:"login_location",
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

function catChange(type, edit = 0) {
    $("#cake_type_div").hide();
    var sDivId = edit == 1 ? "#cake_type_div_edit" : "#cake_type_div";
    if (type == 2) {
        $(sDivId).show();
    }
}
$("#store_item").on("submit", function (e) {
    e.preventDefault();
    str = true;
    $("#name,#email,#mobile,#image,#password,#password").css("border", "");
    $("#nameErr,#emailErr,#passwordErr,#imageErr,#cpasswordErr").html("");

    var name = $("#name").val();
    var email = $("#email").val();
    var mobile = $("#mobile").val();
    var password = $("#password").val();
    var cpassword = $("#cpassword").val();
    var image = $("#image").val();

    var mobpattern = /^[7-9]+[0-9]+$/;
    var emailpattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

    if (name == "") {
        $("#name").css("border", "1px solid red");
        $("#nameErr").html(
            "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Please enter name"
        );
        str = false;
    }

    if (email == "") {
        $("#email").css("border", "1px solid red");
        $("#emailErr").html(
            "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Please enter email"
        );
        str = false;
    }
    if (email != "") {
        if (!emailpattern.test(email)) {
            $("#email").css("border", "1px solid red");
            $("#emailErr").html(
                "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Please enter valid email"
            );
            str = false;
        }
    }

    if (mobile == "") {
        $("#mobile").css("border", "1px solid red");
        $("#mobileErr").html(
            "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Please enter mobile number"
        );
        str = false;
    }
    if (mobile != "") {
        if (!mobpattern.test(mobile)) {
            $("#mobile").css("border", "1px solid red");
            $("#mobileErr").html(
                "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Please enter valid mobile number"
            );
            str = false;
        } else {
            if (mobile.length != 10) {
                $("#mobile").css("border", "1px solid red");
                $("#mobileErr").html(
                    "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Please enter 10 digits"
                );
                str = false;
            }
        }
    }

    if (password == "") {
        $("#password").css("border", "1px solid red");
        $("#passwordErr").html(
            "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Please enter password"
        );
        str = false;
    }
    if (password != "") {
        var plen = password.length;
        if (plen < 8) {
            $("#password").focus();
            $("#password").css("border", "1px solid red");
            $("#passwordErr").html(
                "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Password length minimum 8"
            );
            str = false;
        }
    }

    if (cpassword == "") {
        $("#cpassword").css("border", "1px solid red");
        $("#cpasswordErr").html(
            "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Please enter confirm password"
        );
        str = false;
    }
    if (cpassword.length > 0) {
        if (password != cpassword) {
            $("#cpassword").css("border", "1px solid red");
            $("#cpasswordErr").html(
                "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Password Not Match"
            );
            str = false;
        } else {
            $("#cpasswordErr").html(
                "<span style='color:green'>Password Match</span>"
            );
        }
    }
    var passwordpattern =/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{8,}$/;
    if (password != "") {
        if (!passwordpattern.test(password)) {
            var strErr =
                "At least 8 characters, At least 1 numeric character,At least 1 lowercase letter,At least 1 uppercase letter,At least 1 special character";
            $("#password").css("border", "1px solid red");
            $("#password").tooltip({ trigger: "focus", title: strErr });
            $("#passwordErr").html(
                "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Please Enter Valid Password"
            );
            str = false;
        }
    }
    if (image == "") {
        $("#image").css("border", "1px solid red");
        $("#image").focus();
        $("#imageErr").html(
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
            url: employeeStore,
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
    var url = employeeEdit;
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
                $("#email_edit").val(response.data.email);
                $("#mobile_edit").val(response.data.mobile);
                $("#roleEdit").val(response.data.role);
                if (response.data.type_id == 2) {
                    catChange(response.data.type_id, 1);
                }

                $("#image_display").empty();
                $("#image_display").html(
                    '<img src="' +
                        response.data.image_url +
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
    var url = employeeUpdate;
    var update_id = $("#update_id").val();
    url = url.replace(":id", update_id);
    e.preventDefault();
    str = true;
    $("#name_edit").css("border", "");
    $("#email_edit").css("border", "");
    $("#image_edit").css("border", "");
    $("#mobile_edit").css("border", "");

    var name = $("#name_edit").val();
    var email = $("#email_edit").val();
    var mobile = $("#mobile_edit").val();

    var mobpattern = /^[7-9]+[0-9]+$/;
    var emailpattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

    if (name == "") {
        $("#name_edit").css("border", "1px solid red");
        $("#name_editErr").html(
            "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Please enter name"
        );
        str = false;
    }

    if (email == "") {
        $("#email_edit").css("border", "1px solid red");
        $("#email_editErr").html(
            "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Please enter email"
        );
        str = false;
    }
    if (email != "") {
        if (!emailpattern.test(email)) {
            $("#email_edit").css("border", "1px solid red");
            $("#email_editErr").html(
                "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Please enter valid email"
            );
            str = false;
        }
    }

    if (mobile == "") {
        $("#mobile_edit").css("border", "1px solid red");
        $("#mobile_editErr").html(
            "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Please enter mobile number"
        );
        str = false;
    }
    if (mobile != "") {
        if (!mobpattern.test(mobile)) {
            $("#mobile_edit").css("border", "1px solid red");
            $("#mobile_editErr").html(
                "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Please enter valid mobile number"
            );
            str = false;
        } else {
            if (mobile.length != 10) {
                $("#mobile_edit").css("border", "1px solid red");
                $("#mobile_editErr").html(
                    "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Please enter 10 digits"
                );
                str = false;
            }
        }
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

$("#city_id").on("change", function (event) {
    var cityId = $("#city_id").val();
    getAreas(cityId);
});
$("#city_id_edit").on("change", function (event) {
    var cityId = $("#city_id_edit").val();
    getAreas(cityId, 1);
});
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

$(".mobile_class").on("keyup", function () {
    var cur_mobile = $(this).val();
    if (!isNaN(cur_mobile)) {
        if (parseInt(cur_mobile[0]) < 7) {
            $(this).val("");
        }
    } else {
        $(this).val("");
    }
});

function checkPwd() {
    disp();
    var password = $("#password").val();
    var cpassword = $("#cpassword").val();

    if (cpassword.length > 0) {
        if (password != cpassword) {
            $("#cpassword").css("border", "1px solid red");
            $("#cpasswordErr").html(
                "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Password Not Match"
            );
        } else {
            $("#cpassword").css("border", "");
            $("#cpasswordErr").html(
                "<span style='color:green'>Password Match</span>"
            );
        }
    }
}

function disp() {
    var password = $("#password").val();
    var passwordpattern =
        /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{8,}$/;
    if (password != "") {
        var plength = password.length;
        if (plength > 8) {
            if (!passwordpattern.test(password)) {
                var str =
                    "At least 8 characters, At least 1 numeric character,At least 1 lowercase letter,At least 1 uppercase letter,At least 1 special character";
                $("#password").css("border", "1px solid red");
                $("#password").tooltip({ trigger: "focus", title: str });
                $("#passwordErr").html(
                    "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> Please Enter Valid Password"
                );
            } else {
                $("#password").css("border", "");
                $("#passwordErr").html("");
                $("#password").tooltip({ trigger: "focus", title: " " });
            }
        } else {
            $("#password").css("border", "1px solid red");
            $("#passwordErr").html(
                "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i>Password length minimum 8 "
            );
        }
    }
}
