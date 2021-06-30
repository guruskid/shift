

const __james_id = (Values) => document.getElementById(Values)


const btcusdapi = () => {
	const ajax = new XMLHttpRequest;
	setTimeout(()=>{
		const current = JSON.parse(ajax.responseText)
		document.getElementById("currentBtcRate").value = current.data.amount
	}, 1000)

	// setTimeout(()=>{
	// 	document.write(ajax.responseText)
	// }, 500)

	ajax.open("GET", "http://api.coinbase.com/v2/prices/spot?currency=USD")
	ajax.send()

}

btcusdapi()

const btccharge = () => {
	const btcRate = parseInt(__james_id("currentBtcRate").value)
	const amount = parseInt(__james_id("amount").value)
	let ngnRate = 500;

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


