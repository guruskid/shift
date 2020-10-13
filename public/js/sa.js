$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("#user_email").change(function () {
        email = $('#user_email').val();
        $.ajax({
            type: "GET",
            url: '/admin/get-user/' + email,
            success: function (data) {
                $('#user_name').val(data['first_name'] + " " + data['last_name']);

            }
        });
    });

    /* Disable button after clicked */
    $('.txn-form').submit(function (e) {
        $('.txn-btn').prop('disabled', true);
        Notify('Transaction initiated, please wait', null, null, 'success');
        $('.modal').modal('hide');
    })


});


/* Functional */
function update(email) {
    $.ajax({
        type: "GET",
        url: '/admin/get-user/' + email,
        success: function (data) {
            $('#user_id').val(data['id']);
            $('#user_status').html(data['status']);
            $('#user_status').val(data['status']);
            $('#user_name').html(data['first_name'] + " " + data['last_name']);
        }
    });
}

function editRate(rate) {

    $('#rate-id').val(rate['id']);

    $('#card').html(rate['card']);
    $('#card').val(rate['card']);

    $('#r-type').html(rate['rate_type']);
    $('#r-type').val(rate['rate_type']);

    $('#usd').val(rate['usd']);
    $('#eur').val(rate['eur']);
    $('#gbp').val(rate['gbp']);
    $('#aud').val(rate['aud']);
    $('#cad').val(rate['cad']);
    $('#min').val(rate['min']);
    $('#max').val(rate['max']);
}

/* Delete Rate */
function deleteRate(id) {
    if (confirm("Are you sure you want to delete this rate?")) {
        $.ajax({
            type: 'GET',
            url: '/admin/delete-rate/' + id,
            success: function (data) {
                if (data == true) {
                    alert('Rate deleted');
                    location.reload();
                } else {
                    alert(data);
                }
            }
        });
    }
}

/* Edit Transaction */
function editTransac(id) {
    $.ajax({
        type: "GET",
        url: '/admin/get-transac/' + id,
        success: function (data) {

            $('#e_email').html(data['user_email']);
            $('#e_id').val(data['id']);

            $('#e_card').html(data['card']);
            $('#e_card').val(data['card']);

            $('#e_country').html(data['country']);
            $('#e_country').val(data['country']);

            $('#e_amount').val(data['amount']);
            $('#e_amount_paid').val(data['amount_paid']);

            $('#e_status').html(data['status']);
            $('#e_status').val(data['status']);

            $('#e_trade_type').html(data['type']);
            $('#e_trade_type').val(data['type']);

            $('#e_date').val(data['created_at']);

        }
    });
}

/* Delete Transaction */
function deleteTransac(id) {
    if (confirm("Are you sure you want to delete this transaction?")) {
        $.ajax({
            type: 'GET',
            url: '/admin/delete-transaction/' + id,
            success: function (data) {
                if (data == true) {
                    alert('Transaction deleted');
                    location.reload();
                } else {
                    alert(data);
                }
            }
        });
    }
}

/* Get card details for editing */
function editCard(card) {
    $('#e-card-image').attr('src', '/storage/assets/'+card.image);
    $('#e-card-name').val(card['name']);
    $('#e-card-id').val(card['id']);
    $('#e-card-wallet').val(card['wallet_id']);
    toggleCheckbox(card.is_crypto, '#e-card-crypto' );
    toggleCheckbox(card.buyable, '#e-card-buyable' );
    toggleCheckbox(card.sellable, '#e-card-sellable' );
}

function toggleCheckbox(value, ele) {
    if (value == 1) {
        $(ele).prop('checked', true);
    } else {
        $(ele).prop('checked', false);
    }
}

/* Delete asset */
function deleteCard(id) {
    if (confirm("Are you sure you want to delete this asset?")) {

        $.get("/admin/delete-card/" + id, function (data, status) {
            if (data == true) {
                Notify("Asset deleted", null, null, 'success');
                location.reload();
            } else {
                Notify(data, null, null, 'success');
            }
        });
    }
}

/* Get Notification */
function getNotification(id) {
    $.get("/admin/get-notification/" + id, function (data, status) {
        $('#n-id').val(data['id']);
        $('#n-title').val(data['title']);
        $('#n-body').val(data['body']);
    });
}

/* Delete Notification */
function deleteNot(id) {
    if (confirm("Are you sure you want to delete this notification?")) {

        $.get("/admin/delete-notification/" + id, function (data, status) {
            if (data == true) {
                Notify("Notification deleted", null, null, 'success');
                location.reload();
            } else {
                Notify(data, null, null, 'success');
            }
        });
    }
}

/* Deactivate Chat Agent */
function changeAgent(id, action) {
    if (confirm("Are you sure you want to perform this action")) {
        $.ajax({
            type: 'GET',
            url: '/admin/change-agent/' + id + '/' + action,
            success: function (data) {
                if (data == true) {
                    location.reload();
                } else {
                    alert(data);
                }
            }
        });
    }
}


function removeAgent(id) {
    if (confirm("Are you sure you want to remove this agent")) {
        $.ajax({
            type: 'GET',
            url: '/admin/remove-agent/' + id,
            success: function (data) {
                if (data == true) {
                    alert('Agent removed');
                    location.reload();
                } else {
                    alert(data);
                }
            }
        });
    }
}

/* Confirm transfer of funds */
function confirmTransfer(id, user, amount) {
    $('.amount').text(amount)
    $('.acct-name').text(user['first_name'] + " " + user['last_name'])
    $('#t-id').val(id)
}

/* Confirm refund funds */
function confirmRefund(id, user, amount) {
    $('#r-amount').text(amount)
    $('#r-acct-name').text(user['first_name'] + " " + user['last_name'])
    $('#r-t-id').val(id)
}
