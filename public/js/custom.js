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

    $("#card-name").change(function () {
        $('#country').html('')
        var card_is_crypto = parseInt($(this).find(':selected').data('is-crypto'));
        var card = $("#card-name").val();
        $('#is_crypto').val(card_is_crypto);
        getRange(card);
        if ($('#is_crypto').val() == 1 || card_is_crypto == 1) {
            $('#country').append(`
                <option value="USD" >USD</option>
                <option value="ngn" >NGN</option>
            `);
            $('#card-type-div').hide();
            $('#wallet-id').show();
            $('#wallet-id-text').show();
            if ($('#rate-type').val() == 'buy') {
                $('#pay-with-box').show()
            }
        } else {
            $('#country-div').show();
            $('#card-type-div').show();
            $('#wallet-id').hide();
            $('#wallet-id-text').hide();
            $('#pay-with-box').hide()


            $('#country').append(`
                <option value="USD" >USD</option>
                <option value="eur" >EUR</option>
                <option value="gbp" >GBP</option>
                <option value="aud" >AUD</option>
                <option value="cad" >CAD</option>
            `);
        }

        /* Add various countries */

        $('#asset-type').html($('#card-name').val());
        getType(card);
    });


    /* Get The Rae Exchange */
    $("#rate-form").submit(function (event) {
        event.preventDefault();
        getRate();
    });


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

function getType(card) {
    card = card.toLowerCase();
    if (card == 'amazon') {
        $('#card-type').html("");
        $('#card-type').append(`
            <option value= "cash">Cash</option>
            <option value= "debit">Debit</option>
            <option value="no receipt">No receipt</option>
        `);
    } else {
        $('#card-type').html("");
        $('#card-type').append(`
            <option value="physical">Physical</option>
            <option value="ecode">Ecode</option>
        `);
    }
    $('#value').removeAttr('disabled');
}

/* Function to get range details for card */
function getRange(card) {
    $.get("/user/get-card/" + card, function (data, status) {
        $("#value").attr('min', data['min']);
        $("#value").attr('max', data['max']);
    });
}


function getRate() {
    $('#loader').show();
    var formData = $('#rate-form').serialize();
    console.log('is me ' + $('#is_crypto').val())
    $.ajax({
        type: "POST",
        url: '/user/get-rate',
        data: formData,
        success: function (data) {
            $('#loader').hide();
            if (data['rate'] == null) {
                $("#conv-rate").html('Not rates available');
                $("#conv-val").html('Not rates available');
                Notify("No rates available", null, null, 'warning');
                $("#amount-paid").val('');
                $("#equi").val('');
                $('#wallet-id').val('');
                $("#equiv").text('');
            } else {
                $("#conv-rate").html(' ' + data['rate']);
                $("#conv-val").html('₦' + data['value']);
                $("#amount-paid").val(data['value']);
                $("#equiv").text(data['equiv']);
                var card = $("#card-name").val();
                $.get("/user/get-card/" + card, function (res, status) {
                    if (res['is_crypto'] == 1) {
                        if ($("#rate-type").val() == 'buy') {
                            Notify("Please add your wallet Id", null, null, 'info');
                            $('#wallet-id').removeAttr('disabled');
                            $('#wallet-id-text').show();
                            $('#wallet-id').show();
                            $('#wallet-id').val('');
                        } else {
                            getWalletId(data['card']);
                        }
                    }
                });
            }
        }
    });
};

/* Change the type o transaction buy or sell */
function changeRate(type) {
    if (type == 'sell') {
        $('#sell-trade').addClass('bg-custom-accent');
        $('#buy-trade').removeClass('bg-custom-accent');
        $('#pay-with-box').hide()
    } else {
        $('#buy-trade').addClass('bg-custom-accent');
        $('#sell-trade').removeClass('bg-custom-accent');
        $('#pay-with-box').show()
    }
    $('#rate-type').val(type);
    $('#trade-type').html(type)

}

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

/* Get the wallet id */
function getWalletId(card) {
    $.get("/user/get-wallet-id/" + card, function (data, status) {
        $('#wallet-id').val(data);
    });
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

/* Trade--> send to admin as message and add to transactions table */
function x(agent_id) {
    var walletId = '';
    var is_crypto = $('#is_crypto').val();
    var payWith = $("input[name=pay_with]:checked").val();

    if ($("#conv-val").text() == '' || $('#value').val() == '') {
        return false;
    }
    if ($("#conv-val").text() == 'Not rates available' || $('#value').val() == '' || $("#amount-paid").val() == '' || $("#conv-val").html() == 'Not rates available') {
        Notify("Trade can't be initiated: No rates available", null, null, 'warning');
        return false;
    }

    $("#t-loader").show();

    if ($('#trade-type').text() == 'buy' && is_crypto == 1) {
        walletId = $("#wallet-id").val();
        if (walletId == '') {
            Notify("Please enter your wallet ID", null, null, 'warning');
            return false
        }
    }

    var details = {
        card: $('#asset-type').text(),
        country: $('#country').val(),
        rate_type: $('#rate-type').val(),
        amount: $('#value').val(),
        amount_paid: $('#amount-paid').val(),
        agent_id: agent_id,
        wallet_id: walletId,
        pay_with: payWith,
    }

    $.post("/user/add_transaction", details, function (data, status) {
        console.log(data);
        if (data['success']) {
            Notify("Transaction added", null, null, 'success');
            console.log(data['data']['uid'])
            if (data['data']['status'] == 'success') {
                window.location.href = "/user/transactions";
                return true;
            }
            window.location.href = "/user/view-transaction/" + data['data']['id'] + "/" + data['data']['uid'];
        } else {
            Notify(data['msg'], null, null, 'warning');
            $("#t-loader").hide();
        }
    });
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
