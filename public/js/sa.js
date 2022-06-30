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


/* Delete Rate */
function deleteRate(id) {
    if (confirm("Are you sure you want to delete this rate?")) {
        $.ajax({
            type: 'GET',
            url: '/admin/rate/delete/' + id,
            success: function (data) {
                if (data) {
                    alert('Rate deleted');
                    location.reload();
                } else {
                    alert(data);
                }
            }
        });
    }
}

//**Create Response Data for Called Users*/
function AddResponse(data){
    $('#e_email').html(data['first_name'] + " " + data['last_name']);
    $('#e_id').val(data['id']);
}

function renameCategory(data)
{
    $('#c_id').val(data['id']);
    $('#c_category').val(data['category']);
}

function AddTarget(data)
{
    $('#to_id').val(data['id']);
    $('#to_email').html(data['email']);
}

function EditTarget(user,user_target)
{
    $('#tn_id').val(user['id']);
    $('#tn_email').html(user['email']);
    
    $('#tn_number').val(user_target['target']);
    $('#tn_number').html(user_target['target']);
}

//**View Response Data for Called Users*/
function ViewResponse(call_log,user,call_category){
    $('#v_email').html(user['first_name'] + " " + user['last_name']);
    $('#v_id').val(call_log['id']);

    $('#v_status_input').val(call_category['category']);
    
    $('#v_status').html(call_category['category']);
    $('#v_status').val(call_category['id']);

    $('#v_feedback').val(call_log['call_response']);
}

function ViewNewUserData(user,user_log)
{
    $('#v_email').html(user['email']);
    $('#v_id').val(user['id']);

    $('#v_status_input').val(user_log['status']);
    $('#v_status').val(user_log['status']);
    $('#v_status').html(user_log['status']);

    $('#v_feedback').val(user_log['comment']);
}

function showPhoneNumber(user)
{
    $('#ph_email').html(user['first_name'] + " " + user['last_name']);
    $('#ph_phoneNumber').html(user['phone']);
    
    
    var today = new Date();
    var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
    var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
    var dateTime = date+' '+time;
    $('#e_phoneNumber').val(dateTime);
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
    $('#e_amount_paid').val(parseFloat(parseFloat(data['amount_paid']) + parseFloat(data['commission'])));

    $('#e_status').html(data['status']);
    $('#e_status').val(data['status']);

    $('#e_trade_type').html(data['type']);
    $('#e_trade_type').val(data['type']);

    $('#e_date').val(data['created_at']);
    $('#e_quantity').val(data['quantity']);
    // e_quantity
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
    $('#e-card-image').attr('src', '/storage/assets/' + card.image);
    $('#e-card-name').val(card['name']);
    $('#e-card-id').val(card['id']);
    $('#e-card-wallet').val(card['wallet_id']);
    toggleCheckbox(card.is_crypto, '#e-card-crypto');
    toggleCheckbox(card.buyable, '#e-card-buyable');
    toggleCheckbox(card.sellable, '#e-card-sellable');
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

function confirmBtcTransfer(id, user, amount) {
    $('.amount').text(amount)
    $('.acct-name').text(user['first_name'] + " " + user['last_name'])
    $('#tx-id').val(id)
}

/* Confirm refund funds */
function confirmRefund(id, user, amount) {
    $('#r-amount').text(amount)
    $('#r-acct-name').text(user['first_name'] + " " + user['last_name'])
    $('#r-t-id').val(id)
}
/* add new field for new rate */
function addRateField(id) {
    var list = $('#rates-list-' + id);
    list.append(`
        <div class="media mb-2">
            <div class="media-body d-flex justify-content-between">
                <div class="input-group ">
                    <div class="input-group-prepend"> <span class="input-group-text " id="basic-addon1">$</span> </div>
                    <input type="number" name="values[]" class="form-control" >
                </div>
                <i class="fa fa-exchange-alt mx-2 align-self-center"></i>
                <div class="input-group ">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">₦</span>
                    </div>
                    <input type="number" name="rates[]" class="form-control">
                </div>
            </div>
        </div>
    `);
}

/* add new field for new rate */
function addRateField(id) {
    var list = $('#rates-list-' + id);
    list.append(`
        <div class="media mb-2">
            <div class="media-body d-flex justify-content-between">
                <div class="input-group ">
                    <div class="input-group-prepend"> <span class="input-group-text " id="basic-addon1">$</span> </div>
                    <input type="number" name="values[]" class="form-control" >
                </div>
                <i class="fa fa-exchange-alt mx-2 align-self-center"></i>
                <div class="input-group ">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">₦</span>
                    </div>
                    <input type="number" name="rates[]" class="form-control">
                </div>
            </div>
        </div>
    `);
}
/* Query a transaction from rubies */
function queryTransaction(id) {
    $('#q-id').val(id);
    $('.loader').show();
    $.get('/admin/query-transaction/' + id)
        .done(function (response) {
            // console.log(response);
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
                    text: 'Invalid Transaction',
                    icon: "error",
                    button: "OK",
                });
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



function freezeAccount(user, url) {
    // console.log(url)
    $('#user-name').text(user.first_name);
    $('#user-id').val(user.id);
    $('#freeze-form').attr('action', url);
}

