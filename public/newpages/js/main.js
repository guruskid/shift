//Preview image before upload
$("#proceedtoupload").on("click", function() {
    $("#uploadCardImageModal").show();
});

function preview(input) {
    $("#upload_text_desc").hide();
    if (input.files && input.files[0]) {
        $(input.files).each(function() {
            var reader = new FileReader();
            reader.readAsDataURL(this);
            reader.onload = function(e) {
                $("#previewImg").append(
                    "<img class='thumb m-2 zoom' src='" + e.target.result + "'>"
                );
            };
        });
    }
}

function inputfile() {
    $("#previewImg").empty();
    $("#uploadcardsform")
        .get(0)
        .reset();
    $("#uploadCardImageModal").hide();
}

// ===== BITCOIN CALCULATOR ======
$("#myTab .nav-item").on("click", function() {
    $("#myTab .nav-item").removeClass("active-title-item");
    $("#myTab .nav-item .nav-link").removeClass("text-white");
    $(this).addClass("active-title-item");
    $("#myTab .active-title-item .nav-link").addClass("text-white");
    $("input").val("")
});

$("#copyWalletAddress").on("click", function() {
    const inputText = document.querySelector("#wallet_address");
    inputText.select();
    inputText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    $("#copied_text").css("display", "block");
    $("#inputText").css("box-shadow", "none !important");
    $("#inputText").css("outline", "none !important");
    setTimeout(function() {
        $("#copied_text").css("display", "none");
    }, 1700);
});

// === Bitcoin calculator onchange logic ===
const sell_usd_per_btc = 10500;
const sell_ngn_per_usd = 400;

const sellusdfield = $("#sell_usd_field");
const sellbtcfield = $("#sell_btc_field");
const sellngnfield = $("#sell_ngn_field");

sellusdfield.on("keyup", function() {
    let value = $(this).val();

    //btc equivalent
    let btc = value / sell_usd_per_btc;
    sellbtcfield.val(btc);

    //naira equivalent
    let naira = value * sell_ngn_per_usd;
    sellngnfield.val(naira);
});

sellbtcfield.on("keyup", function(e) {
    let value = $(this).val();

    //dollar equivalent
    let dollars = value * sell_usd_per_btc;
    sellusdfield.val(dollars);

    //naira equivalent
    let naira = dollars * sell_ngn_per_usd;
    sellngnfield.val(naira);
});

/*===== BUY BITCOIN =====*/
const buy_usd_per_btc = 10465.6;
const buy_ngn_per_usd = 381.5;

const btcfield = $("#buy_btc_field");
const usdfield = $("#buy_usd_field");
const ngnfield = $("#buy_ngn_field");

btcfield.on("keyup", function(e) {
    let value = $(this).val();

    //dollar equivalent
    let dollars = value * buy_usd_per_btc;
    usdfield.val(dollars);

    //naira equivalent
    let naira = dollars * buy_ngn_per_usd;
    ngnfield.val(naira);
});

usdfield.on("keyup", function(e) {
    let value = $(this).val();

    //btc equivalent
    let btc = value / buy_usd_per_btc;
    btcfield.val(btc);

    //naira equivalent
    let naira = value * buy_ngn_per_usd;
    ngnfield.val(naira);
});

ngnfield.on("keyup", function(e) {
    let value = $(this).val();

    //dollar equivalent
    let dollar = value / buy_ngn_per_usd;
    usdfield.val(dollar);

    //naira equivalent
    let btc = dollar / buy_usd_per_btc;
    btcfield.val(btc);
});
/*===== END BUY CRYPTO =====*/

$("#sell_submit_btn").on("click", function(e) {
    e.preventDefault();
    const getAlt = $(this).attr("alt");
    const uploadText = `
    <span class="d-block primary-color text-center" style="font-size:14px;">Please place your Image (proof of payment) here</span>
    <span class="d-block text-center" style="font-size:13px;color: rgba(0, 0, 112, 0.7);letter-spacing: 0.01em;">If you do not have a proof of payment click trade to continue</span>
    `;
    if (getAlt == "sell") {
        $("#upload_text_desc").empty();
        $("#upload_text_desc").append(uploadText);
        $("#upload_card_btn").text("Trade");
        // $("#upload_text_desc")

        $("#uploadCardImageModal").show();
    }
});

$("#upload_pop_success").on("click", function() {
    $("#uploadPopModal").css("display", "none");
});
/*===== END BUY BITCOIN =====*/


const sell_usd_per_eth = 970;
const sell_ngn_per_usd_eth = 381.5;

//Convert USD to ETH, naira equivalent
//Sell ETH
$("#sell_usd_field_eth").on("keyup", function(){
    let value = $(this).val();

    //eth equivalent
    let eth = value / sell_usd_per_eth;
    $("#sell_eth_equiv_field").val(eth);

    // //naira equivalent
    let naira = value * sell_ngn_per_usd_eth;
    $("#sell_ngn_eth_field").val(naira);
})


$("#sell_eth_equiv_field").on("keyup", function(e) {
    let value = $(this).val();

    //dollar equivalent
    let dollars = value * sell_usd_per_eth;
    $("#sell_usd_field_eth").val(dollars);

    //naira equivalent
    let naira = dollars * sell_ngn_per_usd_eth;
    $("#sell_ngn_eth_field").val(naira);
});

//Buy ETH
const buy_usd_per_eth = 5465;
const buy_ngn_per_usd_eth = 280;

$("#buy_usd_field_eth").on("keyup", function() {
    let value = $(this).val();

    //btc equivalent
    let eth = value / buy_usd_per_eth;
    $("#buy_eth_field").val(eth);

    //naira equivalent
    let naira = value * buy_ngn_per_usd_eth;
    $("#buy_ngn_field_eth").val(naira);
});

$("#buy_eth_field").on("keyup", function() {
    let value = $(this).val();

    //dollar equivalent
    let dollars = value * buy_usd_per_eth;
    $("#buy_usd_field_eth").val(dollars);

    //naira equivalent
    let naira = dollars * buy_ngn_per_usd_eth;
    $("#buy_ngn_field_eth").val(naira);
});

//Disable submit button if fields are empty in sell btc tab
$("#wallet_address").on("keyup", function(){
    if($(this).val().trim().length > 10 && $("#sell_ngn_field").val().trim().length > 1) {
        $("#sell_submit_btn").removeAttr('disabled')
    } else {
        $("#sell_submit_btn").attr('disabled',"true")
    }
})
$("#sell_btc_field").on("keyup", function(){
    if($("#wallet_address").val().trim().length > 10 && $("#sell_ngn_field").val().trim().length > 1) {
        $("#sell_submit_btn").removeAttr('disabled')
    } else {
        $("#sell_submit_btn").attr('disabled',"true")
    }
})
$("#sell_usd_field").on("keyup", function(){
    if($("#wallet_address").val().trim().length > 10 && $("#sell_ngn_field").val().trim().length > 1) {
        $("#sell_submit_btn").removeAttr('disabled')
    } else {
        $("#sell_submit_btn").attr('disabled',"true")
    }
})

//Disable submit button if fields are empty in buy btc tab
$("#buy_wallet_address").on("keyup", function(){
    if($(this).val().trim().length > 10 && $("#buy_ngn_field").val().trim().length > 1) {
        $("#buy_btc_btn").removeAttr('disabled')
    } else {
        $("#buy_btc_btn").attr('disabled',"true")
    }
})
$("#buy_btc_field").on("keyup", function(){
    if($("#wallet_address").val().trim().length > 10 && $("#buy_ngn_field").val().trim().length > 1) {
        $("#buy_btc_btn").removeAttr('disabled')
    } else {
        $("#buy_btc_btn").attr('disabled',"true")
    }
})
$("#buy_usd_field").on("keyup", function(){
    if($("#wallet_address").val().trim().length > 10 && $("#buy_ngn_field").val().trim().length > 1) {
        $("#buy_btc_btn").removeAttr('disabled')
    } else {
        $("#buy_btc_btn").attr('disabled',"true")
    }
})


//Disable submit button if fields are empty in sell eth tab
$("#eth_wallet_address").on("keyup", function(){
    if($(this).val().trim().length > 10 && $("#sell_ngn_eth_field").val().trim().length > 1) {
        $("#sell_eth_submit_btn").removeAttr('disabled')
    } else {
        $("#sell_eth_submit_btn").attr('disabled',"true")
    }
})
$("#sell_usd_field_eth").on("keyup", function(){
    if($("#eth_wallet_address").val().trim().length > 10 && $("#sell_ngn_eth_field").val().trim().length > 1) {
        $("#sell_eth_submit_btn").removeAttr('disabled')
    } else {
        $("#sell_eth_submit_btn").attr('disabled',"true")
    }
})
$("#sell_eth_equiv_field").on("keyup", function(){
    if($("#eth_wallet_address").val().trim().length > 10 && $("#sell_ngn_eth_field").val().trim().length > 1) {
        $("#sell_eth_submit_btn").removeAttr('disabled')
    } else {
        $("#sell_eth_submit_btn").attr('disabled',"true")
    }
})

//Disable submit button if fields are empty in buy eth tab
$("#buy_wallet_address").on("keyup", function(){
    if($(this).val().trim().length > 10 && $("#buy_ngn_field_eth").val().trim().length > 1) {
        $("#buy_eth_submit_btn").removeAttr('disabled')
    } else {
        $("#buy_eth_submit_btn").attr('disabled',"true")
    }
})
$("#buy_usd_field_eth").on("keyup", function(){
    if($("#buy_wallet_address").val().trim().length > 10 && $("#buy_ngn_field_eth").val().trim().length > 1) {
        $("#buy_eth_submit_btn").removeAttr('disabled')
    } else {
        $("#buy_eth_submit_btn").attr('disabled',"true")
    }
})
$("#buy_eth_field").on("keyup", function(){
    if($("#buy_wallet_address").val().trim().length > 10 && $("#buy_ngn_field_eth").val().trim().length > 1) {
        $("#buy_eth_submit_btn").removeAttr('disabled')
    } else {
        $("#buy_eth_submit_btn").attr('disabled',"true")
    }
})

