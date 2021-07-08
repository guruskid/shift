const __james_id = (Values) => document.getElementById(Values)

const btcusdapi = () => {
	const ajax = new XMLHttpRequest;
	setTimeout(()=>{
		const current = JSON.parse(ajax.responseText)
		__james_id("currentBtcRate").value = current.data.amount
	}, 10000)
	ajax.open("GET", "http://api.coinbase.com/v2/prices/spot?currency=USD")
	ajax.send()
}

// btcusdapi()

const btccharge = (enterAmount) => {

	const btcRate = parseInt(__james_id("currentBtcRate").value)

    let amount = 0

    if(enterAmount == "btcNairaAmount"){
        amount = parseInt(__james_id("nairaBtcAmount").value)
    }else{
        amount = parseInt(__james_id("amount").value)
    }

    let ngnRate = parseInt(__james_id("nairaRate").value);

	const usdRate = amount / ngnRate
	const charge = usdRate / btcRate

	if (!amount) {
		let zeros = 0
		__james_id("rate").innerHTML = zeros.toFixed(8)
		__james_id("msg").innerHTML = "Please insert amount"
    /////// BTC DIV /////////////
        __james_id("btcrate").innerHTML = zeros.toFixed(8)
		__james_id("btcMsg").innerHTML = "Please insert amount"
		return

	}else{
        __james_id("btcMsg").innerHTML = ""
		__james_id("msg").innerHTML = ""
	}

	__james_id("btcrate").innerHTML = charge.toFixed(8) +  " (BTC)"
	__james_id("rate").innerHTML = charge.toFixed(8) +  " (BTC)"

}

const showRate = (rType) => {

    __james_id("rate").innerHTML = ''
    __james_id("btcrate").innerHTML = ''

    __james_id("btcMsg").innerHTML = ""
    __james_id("msg").innerHTML = ""

    if(rType == "btcRecharge"){
        __james_id("btc_show").classList.add("d-block")
        __james_id("btcAirtimePurchase").classList.add("d-block")
        __james_id("btcAirtimePurchase").classList.remove("d-none")
        __james_id("nairaAirtimePurchase").classList.add("d-none")
        __james_id("nairaAirtimePurchase").classList.remove("d-block")
    }else{
        __james_id("btc_show").classList.remove("d-block")

        __james_id("btcAirtimePurchase").classList.add("d-none")
        __james_id("btcAirtimePurchase").classList.remove("d-block")
        __james_id("nairaAirtimePurchase").classList.add("d-block")
        __james_id("nairaAirtimePurchase").classList.remove("d-none")

    }

    // Where it will be submitted will be here

}

const showFeedback = (feedback) => {
    swal({
        title: "Feedback",
        text: feedback,
        icon: "warning"
    })
}




