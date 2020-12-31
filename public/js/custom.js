$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
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
        $("#s-p").show();
        var formData = $(this).serialize();
        $.ajax({
            type: "POST",
            url: "/user/update-profile",
            data: formData,
            success: function (data) {
                $("#s-p").hide();
                if (data == true) {
                    Notify("Profile updated", null, null, "success");
                } else {
                    Notify("An error occured", null, null, "danger");
                }
            }
        });
    });

    /* Bank Details Update */
    $("#user-bank-details").submit(function (event) {
        event.preventDefault();
        $("#s-b").show();
        var formData = $(this).serialize();
        $.ajax({
            type: "POST",
            url: "/user/update-bank",
            data: formData,
            success: function (data) {
                $("#s-b").hide();
                if (data == true) {
                    Notify("Bank details added", null, null, "success");
                    location.reload();
                } else {
                    Notify("An error occured", null, null, "danger");
                }
            }
        });
    });

    $(".custom-file-input").on("change", function () {
        alert("selected");
        var fileName = $(this)
            .val()
            .split("\\")
            .pop();
        $(this)
            .siblings(".custom-file-label")
            .addClass("selected")
            .html(fileName);
    });

    //Disable form button once clicked
    $('.disable-form').submit(function (e) {
        var $form = $(this);
        var $submitBtn = $("button", $form);
        console.log($submitBtn)
        $submitBtn.html('<i class="spinner-border"></i>');
        $submitBtn.attr('disabled', 'true');

    })

});

/* Copy Wallet Id */
function copy() {
    var copyText = document.getElementById("wallet-id");
    $("#wallet-id").removeAttr("disabled");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    Notify("Copied to clipboard", null, null, "success");
    $("#wallet-id").attr("disabled", "true");
}

function copyAddress(id) {
    console.log('hi');
    /* Get the text field */
    var copyText = document.getElementById(id);
    /* Select the text field */
    copyText.select();
    copyText.setSelectionRange(0, 99999); /* For mobile devices */
    /* Copy the text inside the text field */
    document.execCommand("copy");
    /* Alert the copied text */
    swal("Address copied: " + copyText.value);
}

//Send OTP for old users
function sendOtp() {
    var otpText = $('#otp-text');
    var phone = $('#signup_phonenumber').val();
    var country_id = $('#country-id').val();
    if (!phone) {
        alert('Please enter a valid phone number');
    }
    otpText.text('sending . . .');
    $.get('send-otp/' + phone + '/' + country_id)
        .done(function (res) {
            console.log(res);
            if (res['success']) {
                otpText.text('Sent');
                setTimeout(() => {
                    otpText.text('Resend');
                }, 4000);
                swal('A new OTP has been sent to your provided number');
            } else {
                swal(res['msg']);
            }
        })

        .fail(function (xhr, err, status) {
            console.log(xhr, err, status);
            swal('An error occured, please try again');
        })
}

//Resend OTP
function resendOtp() {
    var otpText = $('#otp-text');
    otpText.text('Sending . . .');
    $.get('/resend-otp', function (res) {
        console.log(res);
        if (res['success']) {
            otpText.text('Sent');
            setTimeout(() => {
                otpText.text('Resend');
            }, 4000);
        } else {
            otpText.text(res['msg']);
            setTimeout(() => {
                otpText.text('Resend');
            }, 4000);
        }
    })
}



/* Edit Bank details */
function editBank(id) {
    $.get("/user/get-bank/" + id, function (data, status) {
        $("#e-account-id").val(data["id"]);
        $("#e-account-name").val(data["account_name"]);
        $("#e-account-number").val(data["account_number"]);
        $("#e-bank-name").val(data["bank_name"]);
        $("#e-bank-name").html(data["bank_name"]);
    });
}

/* Delete Bank Details */
function deleteBank(id) {
    if (confirm("Are you sure you want to delete these details?")) {
        $.get("/user/delete-bank/" + id, function (data, status) {
            Notify(data, null, null, "info");
            location.reload();
        });
    }
}

/* Read notifications */
function readNot(id) {
    $.get("/user/read-not/" + id, function (res) {
        if (res["success"]) {
            $("#action-" + id).hide();
            $("#envelope-" + id).attr(
                "class",
                "fa fa-2x mr-3 fa-envelope-open  text-custom"
            );
        } else {}
    });
}

/* Notifications switch */
function notSw(id) {
    var value = "";
    if ($("#" + id).is(":checked")) {
        value = 1;
    } else {
        value = 0;
    }

    var formData = {
        value: value,
        name: id
    };

    $.post("/user/notification-switch", formData, function (data) {
        if (!data["success"]) {
            Notify("Oops! An error occured", null, null, "warning");
        }
    });
}

/* Show Naira wallet transaction details */
function showWalletTxnDetail(txn) {
    $("#d-w-txn-ref").text(txn.reference);
    $("#d-w-txn-type").text(txn.trans_type);
    $("#d-w-txn-cat").text(txn.transaction_type.name);
    $("#d-w-txn-amount").text("₦" + txn.amount_paid);
    $("#d-w-txn-charge").text("₦" + txn.charge);
    $("#d-w-txn-cr").text(txn.cr_acct_name);
    $("#d-w-txn-dr").text(txn.dr_acct_name);
    $("#d-w-txn-narration").text(txn.narration);
    $("#d-w-txn-status").text(txn.status);
    $("#d-w-txn-date").text(txn.created_at);
}

$(".accordion_cards").on("click", function () {
    $(this)
        .find(".accordion_content")
        .css("display", "none");
    if (
        $(this)
        .find(".accordion_arrow")
        .hasClass("accordion_arrow_rotate")
    ) {
        $(this)
            .find(".accordion_arrow")
            .removeClass("accordion_arrow_rotate")
            .addClass("accordion_arrow_return");
        if ($(this).hasClass("phoneVerificationCard")) {
            $("#phoneVerification").css("display", "none");
        } else if ($(this).hasClass("addressVerificationCard")) {
            $("#AddressVerification").css("display", "none");
        } else if ($(this).hasClass("bvnVerificationCard")) {
            $("#bvnVerification").css("display", "none");
        } else if ($(this).hasClass("idVerificationCard")) {
            $("#idVerification").css("display", "none");
        }
    } else {
        $(this)
            .find(".accordion_arrow")
            .removeClass("accordion_arrow_return")
            .addClass("accordion_arrow_rotate");
        if ($(this).hasClass("phoneVerificationCard")) {
            $("#phoneVerification").css("display", "block");
        } else if ($(this).hasClass("addressVerificationCard")) {
            $("#AddressVerification").css("display", "block");
        } else if ($(this).hasClass("bvnVerificationCard")) {
            $("#bvnVerification").css("display", "block");
        } else if ($(this).hasClass("idVerificationCard")) {
            $("#idVerification").css("display", "block");
        }
    }
});
// Hide upload address input
$("#uploadPhotoInput").hide();
$("#uploadAddressVerification").on("click", function () {
    document.getElementById("uploadPhotoInput").click();
});
// Hide Front photo card input
$("#frontPhotoIdInput").hide();
$("#frontPhotoID").on("click", function () {
    document.getElementById("frontPhotoIdInput").click();
});
// Hide BAck photo card input
$("#backPhotoIdInput").hide();
$("#backPhotoID").on("click", function () {
    document.getElementById("backPhotoIdInput").click();
});


$("#quickTopUpLink").on("click", function () {
    if ($("#quickTopUpModal").css("display") == "none") {
        $("#quickTopUpModal").css("display", "block");
    } else {
        $("#quickTopUpModal").css("display", "none");
    }
});
$("#closeQuickTopUp").on("click", function () {
    $("#quickTopUpModal").css("display", "none");
});
$("#quickWithdrawalLink").on("click", function () {
    $("#quickwithdrawalModal").css("display", "block");
});
$("#closeQuickWithdrawal").on("click", function () {
    $("#quickwithdrawalModal").css("display", "none");
});
$("#closeQuickTopUp").on("click", function () {
    $("#quickwithdrawalModal").css("display", "none");
});

$(".quickcard_networks").on("click", function () {
    $(".quickcard_networks").css("border", "0px");
    $(this).css("border", "3px solid #000070");
    $("#airtime_network").val($(this).attr("id"));
});

$("#showwdpin").on("click", function () {
    if ($("#wdpin").attr("type") != "text") {
        $("#wdpin").attr("type", "text");
    } else {
        $("#wdpin").attr("type", "password");
    }
});

function copywalletaddress(receipientAddress) {
    var copyText = document.getElementById(receipientAddress);
    /* Select the text field */
    copyText.select();
    copyText.setSelectionRange(0, 99999); /*For mobile devices*/
    /* Copy the text inside the text field */
    document.execCommand("copy");
    /* Alert the copied text */
    alert("Copied the text: " + copyText.value);
}

function copyAcctNumber(acct_number_input) {
    var copyText = document.getElementById(acct_number_input);
    /* Select the text field */
    copyText.select();
    copyText.setSelectionRange(0, 99999); /*For mobile devices*/
    /* Copy the text inside the text field */
    document.execCommand("copy");
    /* Alert the copied text */
    alert("Copied the text: " + copyText.value);
}

$("#togglepinvisibility").on("click", function () {
    if ($("#pinfortrx").attr("type") == "password") {
        $("#pinfortrx").attr("type", "text");
    } else {
        $("#togglepinvisibility").css("display", "none");
        $("#togglepinvisibility").css("display", "none");
        $("#pinfortrx").attr("type", "password");
    }
});
$("#removeobscure_pwd").on("click", function () {
    if ($("#password_field").attr("type") == "password") {
        $("#password_field").attr("type", "text");
        $("#toggleshowpassword").attr("src", "svg/showpassword.svg");
    } else {
        $("#password_field").attr("type", "password");
        $("#toggleshowpassword").attr("src", "svg/obscure-password.svg");
    }
});
$(".bvnVerificationCard").on("click", function () {
    if ($("#bvnVerification").css("display") != "none") {
        $(".accordion_full_container").css("height", "603px");
    } else {
        $(".accordion_full_container").css("height", "520px");
    }
});
$(".idVerificationCard").on("click", function () {
    if ($("#idVerification").css("display") != "none") {
        $(".accordion_full_container").css("height", "730px");
    } else {
        $(".accordion_full_container").css("height", "520px");
    }
});

//==============|| Bitcoin Wallet page tabs =======\\

$("#bitcoin_send").on("click", function () {
    $(".wallet_trx_tabs").css("display", "none");
    $("#bitcoin_receive").removeClass("walletpage_menu-active");
    $(this).addClass("walletpage_menu-active");
    if ($("#bitcoin_wallet_send_tab").css("display") == "none") {
        $("#bitcoin_wallet_send_tab").css("display", "block");
    }
});
$("#bitcoin_receive").on("click", function () {
    $(".wallet_trx_tabs").css("display", "none");
    $("#bitcoin_send").removeClass("walletpage_menu-active");
    $(this).addClass("walletpage_menu-active");
    if ($("#bitcoin_wallet_receive_tab").css("display") == "none") {
        $("#bitcoin_wallet_receive_tab").css("display", "block");
    }
});
/* Naira Wallet Starts here */

//Dantown to dantown modal
$("#naira_d_to_d").on("click", function () {
    $("#dantownTodantownModal").css("display", "block");
});
$("#closedantownTodantownModal").on("click", function () {
    $("#dantownTodantownModal").css("display", "none");
});

// Dantown to other account
$("#naira_d_to_o").on("click", function () {
    $("#dantownToOtherModal").css("display", "block");
});
$("#closedantownToOtherModal").on("click", function () {
    $("#dantownToOtherModal").css("display", "none");
});

//Naira transfer
$("#naira_transfer").on("click", function () {
    $("#nairaWithdrawTab").css("display", "none");
    $("#nairaDepositTab").css("display", "none");
    $("#nairawallet_trx_type_list").addClass("d-flex");
    $(".naira_menu").removeClass("walletpage_menu-active");
    $(this).addClass("walletpage_menu-active");
});

//Naira withdraw
$("#naira_withdraw").on("click", function () {
    $(".naira_menu").removeClass("walletpage_menu-active");
    $("#nairawallet_trx_type_list").removeClass("d-flex");
    $("#nairaDepositTab").css("display", "none");
    $(this).addClass("walletpage_menu-active");
    $("#nairaWithdrawTab").css("display", "block");
    $("#content_bg").css("height", "800px");
});

//Naira deposit
$("#naira_deposit").on("click", function () {
    $(".naira_menu").removeClass("walletpage_menu-active");
    $("#nairawallet_trx_type_list").removeClass("d-flex");
    $("#nairaWithdrawTab").css("display", "none");
    $("#nairaDepositTab").css("display", "block");
    $(this).addClass("walletpage_menu-active");
    // $("#nairaWithdrawTab").css('display','block')
    // $("#content_bg").css('height','800px')
});

$(".airtime_network_card").on("click", function () {
    $(".airtime_network_card").removeClass("active_airtime_choice");
    $(this).addClass("active_airtime_choice");
    $("#airtimechoice").val($(this).attr("alt"));
});

$("#swapcountrycode").on("change", function () {
    let dialcodeval = $(this)
        .children("option:selected")
        .val()
        .trim();
    $("#dcode").val(dialcodeval);
});
$("#rechargebtn").on("click", function () {
    let code = $("#dcode").val();
    let phone = $("#phonenumber").val();
    let fullNo = code + phone.substring(1);
    $("#fullphonenumber").val(fullNo);
});
$("#buydata").on("change", function () {
    $("#otherphonenumber").css("display", "none");
});
$("#buyother").on("change", function () {
    $("#otherphonenumber").css("display", "block");
});

$("#mobile_phone_verification_card").on("click", function () {
    if ($("#mobile_phone_verification_card_content").hasClass("d-none")) {
        $("#mobile_phone_verification_card_content").removeClass("d-none");
        $("#mobile_phone_verification_card_content").addClass("d-flex");
    } else {
        $("#mobile_phone_verification_card_content").addClass("d-none");
        $("#mobile_phone_verification_card_content").removeClass("d-flex");
    }
});

$("#mobile_address_verification_card").on("click", function () {
    if ($("#mobile_address_verification_card_content").hasClass("d-none")) {
        $("#mobile_address_verification_card_content").removeClass("d-none");
        $("#mobile_address_verification_card_content").addClass("d-flex");
        $(".accordion_full_container").css("height", "540px");
    } else {
        $("#mobile_address_verification_card_content").addClass("d-none");
        $("#mobile_address_verification_card_content").removeClass("d-flex");
        $(".accordion_full_container").css("height", "520px");
    }
});

$("#mobile_bvn_verification_card").on("click", function () {
    if ($("#bvn_verification_card_content").hasClass("d-none")) {
        $("#bvn_verification_card_content").removeClass("d-none");
        $("#bvn_verification_card_content").addClass("d-flex");
        $(".accordion_full_container").css("height", "550px");
    } else {
        $("#bvn_verification_card_content").addClass("d-none");
        $("#bvn_verification_card_content").removeClass("d-flex");
        // $(".accordion_full_container").css("height","120px")
    }
});

$("#mobile_id_verification_card").on("click", function () {
    if ($("#id_verification_card_content").hasClass("d-none")) {
        $("#id_verification_card_content").removeClass("d-none");
        $("#id_verification_card_content").addClass("d-flex");
        $(".accordion_full_container").css("height", "550px");
    } else {
        $("#id_verification_card_content").addClass("d-none");
        $("#id_verification_card_content").removeClass("d-flex");
        // $(".accordion_full_container").css("height","120px")
    }
});

$("#mobile_front_photo_click").on("click", function () {
    document.getElementById("uploadFrontPhotoInputMobile").click();
});

$("#mobile_back_photo_click").on("click", function () {
    document.getElementById("uploadBackPhotoInputMobile").click();
});

function switchTab(f) {
    switch (f) {
        case "mobile_profile_tab":
            $(".mobile_tab_contents").css("display", "none");
            $(".profile_tab_title_mobile").removeClass("tab_active_mobile");
            $(`#${f}`).addClass("tab_active_mobile");
            $("#mobile_profile_contents").css("display", "block");
            break;

        case "mobile_security_tab":
            $(".mobile_tab_contents").css("display", "none");
            $(".profile_tab_title_mobile").removeClass("tab_active_mobile");
            $(`#${f}`).addClass("tab_active_mobile");
            $("#mobile_security_contents").css("display", "block");
            break;

        case "mobile_notifications_tab":
            $(".mobile_tab_contents").css("display", "none");
            $(".profile_tab_title_mobile").removeClass("tab_active_mobile");
            $(`#${f}`).addClass("tab_active_mobile");
            $("#mobile_notification_contents").css("display", "block");
            break;

        case "mobile_limits_tab":
            $(".mobile_tab_contents").css("display", "none");
            $(".profile_tab_title_mobile").removeClass("tab_active_mobile");
            $(`#${f}`).addClass("tab_active_mobile");
            $("#mobile_limits_contents").css("display", "block");
            break;

        default:
            break;
    }
}

$("#step_one_btn").on("click", function () {
    const dialcode = $("#dialcode_select").val().trim()
    const phoneNumber = dialcode + $('#phoneNumber').val().trim()
    $("#signup_phone").val(phoneNumber);
    $("#step_one").css("display", "none");
    $("#step_two").css("display", "block");
});

function toggleTab(param) {
    $(".smartbudget_cardmenu").css("display", "none");
    $(`#${param}`).css("display", "block");
}

function closeTab(param) {
    $(`#${param}`).css("display", "none");
    $(".smartbudget_cardmenu").css("display", "block");
}

$("#removeobscure_pwd2").on("click", function () {
    if ($("#password_field2").attr("type") == "password") {
        $("#password_field2").attr("type", "text");
        $("#toggleshowpassword2").attr("src", "svg/showpassword.svg");
    } else {
        $("#password_field2").attr("type", "password");
        $("#toggleshowpassword2").attr("src", "svg/obscure-password.svg");
    }
});

$(".network_cards").on("click", function () {
    $(".network_cards").css("border", "0");
    $(this).css("border", "3px solid #000070");
    const isp = $(this).attr("id");
    $("#isp_input").val(isp);
});

function accordion(param) {
    const num = param.split("-")[1];
    const id = $(`#${param}`);
    if (id.css("display") == "none") {
        id.css("display", "block");
        $(`#plus-${num}`).css("display", "none");
        $(`#minus-${num}`).css("display", "block");
    } else {
        id.css("display", "none");
        $(`#plus-${num}`).css("display", "block");
        $(`#minus-${num}`).css("display", "none");
    }
}

$(".faq_topic").on("click", function () {
    $(".faq_topic").removeClass("active_faq");
    $(this).addClass("active_faq");
    $(".faq_tab_contents").css("display", "none");
    if ($(this).attr("id") == "finance") {
        $("#finance_content").css("display", "block");
    } else if ($(this).attr("id") == "tech") {
        $("#tech_content").css("display", "block");
    } else if ($(this).attr("id") == "transaction") {
        $("#transaction_content").css("display", "block");
    }
});

$("#pwd_visibility_toggle").on("click", function () {
    if ($("#walletpin").attr("type") == "password") {
        $("#walletpin").attr("type", "text");
        $("#pwd_visibility_toggle2").attr("src", "svg/showpassword.svg");
    } else {
        $("#walletpin").attr("type", "password");
        $("#pwd_visibility_toggle2").attr("src", "svg/obscure-password.svg");
    }
});

$("#dialcode_select").on("click", function () {
    const dialcode = $(this).val().trim()
    const phoneNumber = dialcode + $("#phoneNumber4Power").val().trim()
    $("#phoneNumber").val(phoneNumber)
})

$("#rechargeother").on("click", function () {
    $("#quickRechargeOtherPhone").css('display', 'block')
})

$("#rechargeme").on("click", function () {
    $("#quickRechargeOtherPhone").css('display', 'none')
})

$("#filter_month").on("change", function () {
    const selectedvalue = $(this).children("option:selected").val();
    $("#filtermonthForm").trigger('submit')
})
