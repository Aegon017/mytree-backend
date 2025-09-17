function updateActivationStatus(s) {
    var listarray = new Array();
    $('input[name="multiple[]"]:checked').each(function () {
        listarray.push($(this).val());
    });
    var checklist = "" + listarray;
    if (!isNaN(s) && (s == "1" || s == "0" || s == "2") && checklist != "") {
        $("#fail").hide();
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            dataType: "JSON",
            type: "POST",
            data: {
                updatelist: checklist,
                activity: s,
            },
            url: statusUpdate,
            success: function (response) {
                if (response.code == 200) {
                    itemDataTable();
                    showAlertMessage(
                        response.data + " " + response.message,
                        "success"
                    );
                    $("#multiAction").prop("checked", false);
                } else {
                    showAlertMessage(response.message, "danger");
                }
            },
            error: function (er) {
                console.log(er);
            },
        });
    } else {
        showAlertMessage("*  Please select at least one record", "danger");
    }
}

function commonDelete(s) {
    var listarray = new Array();
    $('input[name="multiple[]"]:checked').each(function () {
        listarray.push($(this).val());
    });
    var checklist = "" + listarray;
    if (!isNaN(s) && (s == "1" || s == "0") && checklist != "") {
        $("#fail").hide();
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            dataType: "JSON",
            type: "POST",
            data: {
                updatelist: checklist,
                activity: s,
            },
            url: deleteUpdate,
            success: function (response) {
                if (response.code == 200) {
                    itemDataTable();
                    showAlertMessage(
                        response.data + " " + response.message,
                        "success"
                    );
                    $("#multiAction").prop("checked", false);
                } else {
                    showAlertMessage(response.message, "danger");
                }
            },
            error: function (er) {
                console.log(er);
            },
        });
    } else {
        showAlertMessage("*  Please Select at least one record", "danger");
    }
}

function bookingCancel() {
    var listarray = new Array();
    $('input[name="multiple[]"]:checked').each(function () {
        listarray.push($(this).val());
    });
    var checklist = "" + listarray;
    if (checklist != "") {
        $("#fail").hide();
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            dataType: "JSON",
            type: "POST",
            data: {
                updatelist: checklist,
            },
            url: cancelBooking,
            success: function (response) {
                if (response.code == 200) {
                    itemDataTable();
                    showAlertMessage(
                        response.data + " " + response.message,
                        "success"
                    );
                    $("#multiAction").prop("checked", false);
                } else {
                    showAlertMessage(response.message, "danger");
                }
            },
            error: function (er) {
                console.log(er);
            },
        });
    } else {
        showAlertMessage("*  Please Select at least one record", "danger");
    }
}
