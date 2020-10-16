const data = {
    usd: {
        key: "usd",
        country: "usa",
        physical: [{ id: 1, price: "$25", ppc: 1500 }]
        // ecode: [
        //     { id: 1, min: "31", max: "40", rate: "490/$" },
        //     { id: 2, min: "41", max: "50", rate: "760/$" },
        //     { id: 3, min: "51", max: "60", rate: "910/$" },
        // ],
        // large: [
        //     { id: 100, min: "31", max: "40", rate: "490/$" },
        //     { id: 122, min: "41", max: "50", rate: "760/$" },
        //     { id: 343, min: "51", max: "60", rate: "910/$" },
        // ],
        // small: [
        //     { id: 1, min: "31", max: "40", rate: "490/$" },
        //     { id: 2, min: "41", max: "50", rate: "760/$" },
        //     { id: 3, min: "51", max: "60", rate: "910/$" },
        // ],
    }
};

let basket = [];
$("#addcard_button").on("click", function() {
    let cardprice = $("#cardprice")
        .val()
        .trim();
    let price_per_card = $("#price_per_card")
        .text()
        .trim();
    let quantity = $("#quantity")
        .val()
        .trim();
    let total = price_per_card.slice(1) * quantity;

    const template = `<tr class="my-2">
                        <td>${cardprice}</td>
                        <td>${price_per_card}</td>
                        <td>x${quantity}</td>
                        <td id="totalprice">${total}</td>
                        <td id="removeitem" class="removeitem">Remove</td>
                    </tr>`;

    basket.push(total);

    $("#selectedcardslist").append(template);
    $("#total_price").addClass("d-flex");
    $("#nocardavailable").hide();
    $("#totalAmount").html("N " + basket.reduce(addtotals));
});

function addtotals(total, num) {
    return total + num;
}

//Preview image before upload
$("#proceedtoupload").on("click", function() {
    $("#uploadcardimage").trigger("click");
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
}

$("table").on("click", "td.removeitem", function() {
    const pricetoremove = $(this)
        .siblings("#totalprice")
        .text()
        .trim();

    const matchingitem = basket.findIndex(price => price == pricetoremove);
    basket.splice(matchingitem, 1);

    $(this)
        .parent()
        .remove();

    if (basket.length == 0) {
        $("#totalAmount").html("N " + 0);
        $("#total_price").removeClass("d-flex")
        $("#nocardavailable").show();
    } else {
        $("#totalAmount").html("N " + basket.reduce(addtotals));
    }
});
