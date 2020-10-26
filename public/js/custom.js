$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


   /*  window.oncontextmenu = function () {
        return false;
    }
    $(document).keydown(function (event) {
        if (event.keyCode == 123) {
            return false;
        }
        else if ((event.ctrlKey && event.shiftKey && event.keyCode == 73) || (event.ctrlKey && event.shiftKey && event.keyCode == 74)) {
            return false;
        }
    }); */


    /* Submiting the user profile form */
    $("#user-profile-form").submit(function (event) {
        event.preventDefault();
        $('#s-p').show();
        var formData = $(this).serialize();
        $.ajax({
            type: "POST",
            url: '/user/update-profile',
            data: formData,
            success: function (data) {
                $('#s-p').hide();
                if (data == true) {
                    Notify("Profile updated", null, null, 'success');
                } else {
                    Notify("An error occured", null, null, 'danger');
                }

            }
        });
    });

    /* Bank Details Update */
    $("#user-bank-details").submit(function (event) {
        event.preventDefault();
        $('#s-b').show();
        var formData = $(this).serialize();
        $.ajax({
            type: "POST",
            url: '/user/update-bank',
            data: formData,
            success: function (data) {
                $('#s-b').hide();
                if (data == true) {
                    Notify("Bank details added", null, null, 'success');
                    location.reload();
                } else {
                    Notify("An error occured", null, null, 'danger');
                }

            }
        });
    });


    $(".custom-file-input").on("change", function () {
        alert('selected');
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });




});

/* Copy Wallet Id */
function copy() {
    var copyText = document.getElementById("wallet-id");
    $("#wallet-id").removeAttr("disabled");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    Notify("Copied to clipboard", null, null, 'success');
    $("#wallet-id").attr("disabled", 'true');
}


/* Edit Bank details */
function editBank(id) {
    $.get("/user/get-bank/" + id, function (data, status) {
        $("#e-account-id").val(data['id']);
        $("#e-account-name").val(data['account_name']);
        $("#e-account-number").val(data['account_number']);
        $("#e-bank-name").val(data['bank_name']);
        $("#e-bank-name").html(data['bank_name']);
    });
}

/* Delete Bank Details */
function deleteBank(id) {
    if (confirm("Are you sure you want to delete these details?")) {
        $.get("/user/delete-bank/" + id, function (data, status) {
            Notify(data, null, null, 'info');
            location.reload();
        });
    }
}

/* Read notifications */
function readNot(id) {
    $.get('/user/read-not/' + id, function (res) {
        if (res['success']) {
            $('#action-' + id).hide();
            $('#envelope-' + id).attr('class', 'fa fa-2x mr-3 fa-envelope-open  text-custom')
        } else {

        }
    })
}


/* Notifications switch */
function notSw(id) {
    var value = '';
    if ($('#' + id).is(':checked')) {
        value = 1;
    } else {
        value = 0;
    }

    var formData = {
        value: value,
        name: id
    }

    $.post("/user/notification-switch", formData, function (data) {
        if (!data['success']) {
            Notify('Oops! An error occured', null, null, 'warning');
        }
    })

}

/* Show Naira wallet transaction details */
function showWalletTxnDetail(txn) {
    $('#d-w-txn-ref').text(txn.reference)
    $('#d-w-txn-type').text(txn.trans_type)
    $('#d-w-txn-cat').text(txn.transaction_type.name)
    $('#d-w-txn-amount').text('₦'+txn.amount_paid)
    $('#d-w-txn-charge').text('₦'+txn.charge)
    $('#d-w-txn-cr').text(txn.cr_acct_name)
    $('#d-w-txn-dr').text(txn.dr_acct_name)
    $('#d-w-txn-narration').text(txn.narration)
    $('#d-w-txn-status').text(txn.status)
    $('#d-w-txn-date').text(txn.created_at)


}
