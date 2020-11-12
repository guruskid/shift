$(document).ready(function() {
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
    $("#user-profile-form").submit(function(event) {
        event.preventDefault();
        $("#s-p").show();
        var formData = $(this).serialize();
        $.ajax({
            type: "POST",
            url: "/user/update-profile",
            data: formData,
            success: function(data) {
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
    $("#user-bank-details").submit(function(event) {
        event.preventDefault();
        $("#s-b").show();
        var formData = $(this).serialize();
        $.ajax({
            type: "POST",
            url: "/user/update-bank",
            data: formData,
            success: function(data) {
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

    $(".custom-file-input").on("change", function() {
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

/* Edit Bank details */
function editBank(id) {
    $.get("/user/get-bank/" + id, function(data, status) {
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
        $.get("/user/delete-bank/" + id, function(data, status) {
            Notify(data, null, null, "info");
            location.reload();
        });
    }
}

/* Read notifications */
function readNot(id) {
    $.get("/user/read-not/" + id, function(res) {
        if (res["success"]) {
            $("#action-" + id).hide();
            $("#envelope-" + id).attr(
                "class",
                "fa fa-2x mr-3 fa-envelope-open  text-custom"
            );
        } else {
        }
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

    $.post("/user/notification-switch", formData, function(data) {
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

$(".accordion_cards").on("click", function() {
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
$("#uploadAddressVerification").on("click", function() {
    document.getElementById("uploadPhotoInput").click();
});
// Hide Front photo card input
$("#frontPhotoIdInput").hide();
$("#frontPhotoID").on("click", function() {
    document.getElementById("frontPhotoIdInput").click();
});
// Hide BAck photo card input
$("#backPhotoIdInput").hide();
$("#backPhotoID").on("click", function() {
    document.getElementById("backPhotoIdInput").click();
});


/* var chart = new CanvasJS.Chart("chartContainer", {
    animationEnabled: true,
    title: {
        text: "Email Categories",
        horizontalAlign: "left"
    },
    data: [
        {
            type: "doughnut",
            startAngle: 60,
            //innerRadius: 60,
            indexLabelFontSize: 17,
            indexLabel: "{label} - #percent%",
            toolTipContent: "<b>{label}:</b> {y} (#percent%)",
            dataPoints: [
                { y: 67, label: "Inbox" },
                { y: 28, label: "Archives" },
                { y: 10, label: "Labels" },
                { y: 7, label: "Drafts" },
                { y: 15, label: "Trash" },
                { y: 6, label: "Spam" }
            ]
        }
    ]
});
chart.render(); */

$("#quickTopUpLink").on("click", function(){
    if($("#quickTopUpModal").css("display") == "none") {
        $("#quickTopUpModal").css("display","block")
    } else {
        $("#quickTopUpModal").css("display","none")
    }
})
$("#closeQuickTopUp").on("click", function(){
    $("#quickTopUpModal").css("display","none")
})
$("#quickWithdrawalLink").on("click", function(){
    $("#quickwithdrawalModal").css("display","block")
})
$("#closeQuickWithdrawal").on("click", function(){
    $("#quickwithdrawalModal").css("display","none")
})
$("#closeQuickTopUp").on("click", function(){
    $("#quickwithdrawalModal").css("display","none")
})

$(".quickcard_networks").on("click", function(){
    $(".quickcard_networks").css("border","0px")
    $(this).css("border","3px solid #000070")
    $("#airtime_network").val($(this).attr('id'))
})

$("#showwdpin").on("click", function(){
    if($("#wdpin").attr('type') != 'text') {
    $("#wdpin").attr('type', 'text')
    } else {
        $("#wdpin").attr('type', 'password')
    }
})


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
