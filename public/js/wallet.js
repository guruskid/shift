$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('#bank-name').change(function () {
        getAccountName();
    });
    $('#account-number').change(function () {
        getAccountName();
    });
    /* Update amount for paytv change in selected package */
    $('#packages').change(function () {
        var amount = $(this).find(':selected').data('amount');
        $('#amount').val(amount);
        $('#d-amount').text('₦' + amount);
        $('#d-package').text($(this).find(':selected').data('name'))
    });

    /* hide transfer modal after submit */
    $('#transfer-form').submit(function (e) {
        Notify('Transaction initiated, please wait', null, null, 'success');
        $('#transfer-btn').prop('disabled', true);
        $('#confirm-modal').modal('hide');
        $('#send-modal').modal('hide');

    });

    /* disable btn after buying airtime */
    $('#airtime-form').submit(function (e) {
        $('#recharge-btn').prop('disabled', true);
        Notify('Transaction initiated, please wait', null, null, 'success');
        $('#airtime-recharge-modal').modal('hide');
    });

    /* Disable cable btn */
    $('#cable-form').submit(function (e) {
        $('#cable-btn').prop('disabled', true);
        Notify('Transaction initiated, please wait', null, null, 'success');
        $('#paytv-modal').modal('hide');
    });

    /* Disable electricity btn */
    $('#electricity-form').submit(function (e) {
        $('#electricity-btn').prop('disabled', true);
        Notify('Transaction initiated, please wait', null, null, 'success');
        $('#electricity-modal').modal('hide');
    });
});
/* Get the name of associated to the account number */
function getAccountName() {
    var bankName = $('#bank-name').val();
    var acctNumber = $('#account-number').val();
    if (!bankName || !acctNumber) {
        return false;
    }
    document.getElementById("sign-up-btn").disabled = true;
    $('.acct-name').val('Loading...');
    var details = {
        acct_num: acctNumber,
        bank_name: bankName
    };
    $.post('/get-bank-details', details, function (data) {
        console.log(data);
        if (data['success']) {
            $('.acct-name').val(data['acct']);
            $('.acct-name').text(data['acct']);
            document.getElementById("sign-up-btn").disabled = false;
        } else {
            $('.acct-name').val('');
            $('.acct-name').text('Invalid Account');
            alert(data['msg']);
        }
    });
}
/* Update the modal for withdraw txn */ /* type ; 1 = transfer; 2 = withdraw */
function tnsType(type) {
    $('#add-acct-details').hide();
    if (type == 1) {
        $('.tns-title').text('Transfer');
        $('#trns-type').val(1);
        $('#add-acct-details').show();
        $('#transfer-dest').show();
        $('.user-accts').hide();
        $('input[name="account_id"]').prop("checked", false);
        $('#dantown-bank').text('Dantown');
        $('#dantown-bank').val('090175');
        $('.other-banks').hide();
        $('.t-info').text('Transactions within Dantown are free');
    } else if (type == 2) {
        $('.tns-title').text('Withdraw');
        $('#trns-type').val(2);
        $('#add-acct-details').hide();
        $('#transfer-dest').hide();
        $('.user-accts').show();
        $('#dantown-bank').text('Select Bank');
        $('#dantown-bank').val('');
        $('.t-info').text('Note! a charge of ₦80.00 will be added for each transaction outside Dantown wallet');
    }
} /* Show input for new account */
function addAcct() {
    $('#add-acct-details').show();
    $('input[name="account_id"]').prop("checked", false)
} /* Pay bills functions */
function getDecoderUser() {
    var acctName = $('#acct-name');
    var decNum = $('#dec-num').val();
    var biller = $('#biller').val();
    if (!decNum || !biller) {
        return false;
    }
    acctName.val('Loading, please wait...');
    var formData = {
        dec_num: decNum,
        biller: biller
    };
    $.post('/user/get-dec-user', formData, function (res) {
        if (res['responsecode'] == 00) {
            getPackages(decNum, biller);
            acctName.val(res['data']['name']);
            $('#d-acct-name').text(res['data']['name']);
            $('#d-dec-num').text(decNum);
        } else {
            acctName.val(res['responsemessage']);
            $('#d-acct-name').text(res['responsemessage']);
            Notify(res['responsemessage'], null, null, 'warning');
        }
    })
} /* Get the diffeerent packages for cable subscription */
function getPackages(decNum, biller) {
    $('#packages').html(`<option value="" id="package-loader" ></option> `);
    var pl = $('#package-loader');
    pl.text('Loading please wait...');
    var formData = {
        dec_num: decNum,
        biller: biller
    };
    $.post('/user/get-tv-packages', formData, function (res) {
        if (res['responsecode'] == 00) {
            pl.text('Select Package');
            res['productcategories'].forEach(i => {
                $('#packages').append(` <option data-name="` + i['name'] + `"  data-amount="` + i['amount'] + `" value="` + i['bundleCode'] + `">` + i['name'] + ` (₦` + i['amount'] + `)</option> `);
            });
        } else {
            acctName.val(res['responsemessage']);
            Notify(res['responsemessage'], null, null, 'warning');
        }
    });
} /* Change transfer type for transfers on wallet */
function changeTransferType(type) {
    if (type == 'dantown') {
        $('#dantown-transfer').addClass('bg-custom-accent');
        $('#other-transfer').removeClass('bg-custom-accent');
        $('#dantown-bank').text('Dantown');
        $('#dantown-bank').val('090175');
        $('.other-banks').hide();
        $('.t-info').text('Transactions within Dantown are free');
    } else {
        $('#other-transfer').addClass('bg-custom-accent');
        $('#dantown-transfer').removeClass('bg-custom-accent');
        $('#dantown-bank').text('Select bank name');
        $('#dantown-bank').val('');
        $('.other-banks').show();
        $('.t-info').text('Note! a charge of ₦80.00 will be added for each transaction');
    }
} /* Get airtime details */
function getAirtimedetails() {
    var txnType = 'Airtime';
    var netwkPrvdr = $('#a-network-provider').val();
    var phone = $('#a-phone').val();
    var amount = $('#a-amount').val();
    showDetails(txnType, netwkPrvdr, phone, amount);
} /* Update details for aitime purchase details */
function showDetails(txnType, netwkPrvdr, phone, amount) {
    $('#d-txn-type').text(txnType);
    $('#d-network-provider').text(netwkPrvdr);
    $('#d-phone').text(phone);
    $('#d-amount').text('₦' + amount);
    $('#d-amount-payable').text('₦' + amount);
} /* Get the details for electricitypurchase */
function getElectUser() {
    var providerId = $('#provider').find(':selected').data('scid');
    var account = $('#metrenumber').val();
    var productcode = $('#billercode').val();
    console.log(account,productcode)
    var acctName = $('#acct-name');
    if (productcode != '' && account != '') {
        acctName.val('Loading, please wait..');
        //use in the previous design
        $('#d-provider').text($('#provider').find(':selected').text());
        $('#d-meter-no').text(account);
        $('#scid').val(providerId);
        //end of usage

        $.ajax({
            type: "post",
            url: "/user/get-elect-user",
            dataType: 'json',
            data: {
                "billercode": productcode,
                "billercustomerid":account
            },
            success: function (result){
                console.log('hi')
                console.log(result)
                if (result == undefined) {
                    acctName.val('No account found');
                    $('#d-acct-name').text('No account found');
                } else {
                    acctName.val(result);
                    $('#d-acct-name').text(result);
                }
            }
        });


    }

}

function getElectPrice() {
    $('#d-amount').text('₦' + $('#amount').val());
}
function getAirtimeAmount() {
    var amount = $('#airtime_amount').val();
    $('#new-amount').text('₦' +(0.7 * amount));

}
function getCableUser() {
    var productcode = $('#dec-billercode').val();
    console.log(productcode)
    var decoder = $('#decoder').val();
    console.log(decoder)
    var acctName = $('#owner-name');
    if (productcode != '' && decoder != '') {
        acctName.val('Loading, please wait..');
        $.ajax({
            type: "post",
            url: "/user/get-elect-user",
            dataType: 'json',
            data: {
                "billercode": productcode,
                "billercustomerid":decoder
            },
            success: function (result){
                if (result != undefined) {
                    acctName.val(result);
                    $('#owner-name').text(result);
                }
                // acctName.val('No account found');
                // $('#owner-name').text('No account found');
            }
        });


    }

}
