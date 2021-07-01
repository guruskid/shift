

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

btcusdapi()



const btccharge = () => {
	const btcRate = parseInt(__james_id("currentBtcRate").value)
	const amount = parseInt(__james_id("amount").value)
	let ngnRate = parseInt(__james_id("nairaRate").value);

    // alert(ngnRate)

	const usdRate = amount / ngnRate
	const charge = usdRate / btcRate

	if (!amount) {
		let zeros = 0
		__james_id("rate").innerHTML = zeros.toFixed(8)
		__james_id("msg").innerHTML = "Please insert amount"
		return
	}else{
		__james_id("msg").innerHTML = ""
	}
	__james_id("rate").innerHTML = charge.toFixed(8) +  " (BTC)"

}

const showRate = (rType) => {
    if(rType == "btcRecharge"){
        __james_id("btc_show").classList.add("d-block")
    }else{
        __james_id("btc_show").classList.remove("d-block")
    }

    // Where it will be submitted will be here

}



