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

/* get wallet details and transactions when the account number is changed *//*
$('#account-number').change(function (e) {
    getUserWalletDetails($(this).val());
}) */

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
function editTransac(data) {
    $('#e_email').html(data['user_email']);
    $('#e_id').val(data['id']);

    $('#e_card').html(data['card']);
    $('#e_card').val(data['card_id']);

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
function editCard(id) {
    $.get("/admin/get-card/" + id, function (data, status) {
        $('#e-card-name-2').html(data['name']);
        $('#e-card-id').val(data['id']);
        $('#e-card-name').val(data['name']);
        $('#e-card-wallet').val(data['wallet_id']);
        $('#e-card-min').val(data['min']);
        $('#e-card-max').val(data['max']);
        if (data['is_crypto'] == 1) {
            $('#e-card-crypto').html("Yes");
        } else {
            $('#e-card-crypto').html("No");
        }
        $('#e-card-crypto').val(data['is_crypto']);
    });
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

/* Query a transaction from rubies */
function queryTransaction(id) {
    $('#q-id').val(id);
    $('.loader').show();
    $.get('/admin/query-transaction/' + id)
        .done(function (response) {
            console.log(response);
            $('.loader').hide();
            if (response['success'] == true) {
                res = response.data;
                $('#q-ref').text(res.requestdata.reference);
                $('#q-res-code').text(res.responsecode);
                $('#q-status').text(res.transactionstatus);
                $('#q-res-msg').text(res.responsemessage);
                $('#q-amount').text('₦' + res.requestdata.amount.toLocaleString());
                $('#q-cr').text(res.craccountname);
                $('#q-dr').text(res.draccountname);
                $('#q-req-date').text(res.requestdate);
                $('#q-res-date').text(res.responsetime);


            } else {
                swal({
                    title: "Ooops!",
                    text: response.data.responsemessage,
                    icon: "error",
                    button: "OK",
                });
                console.log(response.data)
            }
        })
        .fail(function (xhr, status, error) {
            $('.loader').hide();
            swal({
                title: "Ooops!",
                text: error,
                icon: "error",
                button: "OK",
            });
        })
}


/* get transactions and user details for adding transaction, it is called from the top */
/* function getUserWalletDetails(accountNumber) {
    console.log(accountNumber);
    if (accountNumber.length < 10) {
        return;
    }
    var accountName = $('#account-name');
    var email = $('#email');
    var transactionsList = $('#transactions-list');

    accountName.val('Loading');
    email.val('Loading');
    transactionsList.html('Loading');

    $.get('/admin/get-wallet-details/'+accountNumber)
    .done(function (res) {
        if (res.success) {
            console.log(res)
            var user = res.user;
            var transactions = res.transactions;
            var wallet =  res.wallet;
            transactionsList.html('')

            accountName.val(wallet.account_name) ;
            email.val(user.email)

            $('#wallet-id').val(wallet.id);
            $('#wallet-balance').text('₦'+wallet.amount.toLocaleString());


            transactions.forEach(t => {
                transactionsList.append(`
                    <tr>
                        <td>${t.id} </td>
                        <td>${t.reference} </td>
                        <td>${t.transaction_type.name}</td>
                        <td>₦${t.amount_paid} </td>
                        <td>₦${t.charge} </td>
                        <td>₦${t.amount.toLocaleString()} </td>
                        <td>₦${t.previous_balance}</td>
                        <td>₦${t.current_balance} </td>
                        <td>${t.cr_acct_name} </td>
                        <td>${t.dr_acct_name} </td>
                        <td>${t.narration} </td>
                        <td>${t.created_at} </td>
                        <td>${t.status} </td>
                    </tr>
                `)
            });
        } else {
            accountName.val('');
            email.val('');
            $('#wallet-balance').text('')
            transactionsList.html('');
            $('#wallet-id').val('');
            alert(res.message);
        }


     })
    .fail(function (err) {  console.log(err) })
} */
