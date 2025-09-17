// $(document).ready(function() {
//     var table = itemDataTable();
//     table.on('draw.dt', function() {
//         var info = table.page.info();
//     });
// });
// $("#trash").on('change',function( event ) {
//     ($('#trash').val() == 1 ? $('#restore_btn').show():$('#restore_btn').hide());
//     event.preventDefault();
//     itemDataTable();
// });

$('#store_item').on('submit', function(e) {
    e.preventDefault();
    str = true;
    $('#name').css('border', '');
    $('#nameErr').html('');

    var oldPassword = $("#old_password").val();
    var newPassword = $("#new_password").val();
    var confirmPassword = $("#confirm_password").val();
    if (oldPassword == '') {
        $('#old_password').css('border', '1px solid red');
        $('#old_password').focus();
        $('#old_passwordErr').html('<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please enter Old Password');
        str = false;
    }
    if (newPassword == '') {
        $('#new_password').css('border', '1px solid red');
        $('#new_password').focus();
        $('#new_passwordErr').html('<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please enter New Password');
        str = false;
    }
    if (confirmPassword == '') {
        $('#confirm_password').css('border', '1px solid red');
        $('#confirm_password').focus();
        $('#confirm_passwordErr').html('<i class="fa fa-exclamation-circle" aria-hidden="true"></i> Please enter Confirm Password');
        str = false;
    }

    if (str) {
        // Loader.show();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: changePasswordStore,
            data: new FormData(this),
            dataType: 'json',
            type: "POST",
            contentType: false,
            cache: false,
            processData: false,
            success: function(response) {
                if (response.code == 201) {
                    showAlertMessage(response.message, 'success');
                } else {
                    showAlertMessage(response.message, 'danger');
                }
                // Loader.hide();
            },
            error: function(response, data) {
                // Loader.hide();
                $('#addItem').modal('hide');
                showAlertMessage(response.responseJSON.message, 'danger');
            }
        });
    }
});


$('#multiAction').click(function() {
    if ($('#multiAction').is(':checked')) {
        $('#multiAction').prop('checked', true);
        $('[name="multiple[]"]').prop('checked', true);
    } else {
        $('#multiAction').prop('checked', false);
        $('[name="multiple[]"]').prop('checked', false);
    }
});
function addItemModel(){
    $('#store_item').trigger("reset");
    $('#addItem').modal('toggle');
}
