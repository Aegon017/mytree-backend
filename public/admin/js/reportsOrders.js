// $(document).ready(function () {
//     var table = itemDataTable();
//     table.on("draw.dt", function () {
//         var info = table.page.info();
//     });
// });


$("#startDate,#endDate").on("change", function (event) {
    event.preventDefault();
    itemDataTable();
});


function itemDataTable($rows = 31) {
    
    getDaysCount();
    return $(".serversideDatatableForItems").DataTable({
        //dom: 'frt',
        serverSide: true,
        processing: true,
        responsive: true,
        bDestroy: true,
        pageLength: $rows,
        ajax: {
            url: reportOrderUrl,
            data: function (d) {
                d.startDate = $("#startDate").val();
                d.endDate = $("#endDate").val();
                
            },
        },
        order: [], //disable default sort ordering

        drawCallback: function () {},
        language: {},
        columns: [
            // {
            //     // Serial number column
            //     render: function(data, type, row, meta) {
            //         return meta.row + 1;  // Adds serial number starting from 1
            //     },
            //     orderable: false,
            //     searchable: false
            // },
            
            {
                data: "user",
            },
            {
                data: "date",
            },
            {
                data: "order_ref",
            },
            {
                data: "amount",
            },
            {
                data: "payment_status",
            },
            {
                data: "razorpay_order_id",
            }
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


function getDaysCount() {
    var startDate = $("#startDate").val();
    var endDate = $("#endDate").val();
    var url = getCountReport;
    url = url.replace(":start", startDate);
    url = url.replace(":end", endDate);
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
                console.log(response);
                $("#paid_orders").html(response.data.paid_orders);
                $("#failed_orders").html(response.data.failed_orders);
                $("#pending_orders").html(response.data.pending_orders);
                $("#total_revenue").html(response.data.total_revenue);
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

