<template>
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-6 col-lg-6 my-0 py-3">

                <div class="d-flex flex-column">

                    <div class="p-lg-2 allcards-container">
                        <div
                            class="d-flex flex-column flex-lg-row align-items-center align-items-between-lg justify-content-between">
                            <div class="ml-lg-3 transaction-title">
                                Transaction</div>
                            <div class="d-block mr-lg-3"
                                style="font-size: 20px;color: rgba(0, 0, 112, 0.75); opacity: 0.7;">
                                {{ buy_sell == 1 ? 'Buy ' + card.name + ' from us' : 'Sell ' + card.name + ' to us' }}
                            </div>
                        </div>
                        <div class="mx-auto mb-4 mt-3" style="border: 1px solid #EFEFF8;width: 90%;">
                        </div>
                        <div
                            class="d-flex flex-row justify-content-around align-items-center flex-wrap flex-lg-nowrap p-2 p-lg-3 pl-lg-4">
                            <div class="card-image mr-lg-2">
                                <img src="/cards/steam.png" alt="card" style="width: 170px">
                            </div>
                            <div class="d-flex flex-column mx-1 mt-4 mt-lg-0 cctype_container">
                                <div class="d-flex flex-column align-items-around">
                                    <label class="label-style">Currency</label>
                                    <select id="countries_list" v-model="selectedCurrency" name="country"
                                        @change="onCurrencyChange($event)"
                                        class="form-control custom-select select-country-custom-select">
                                        <option disabled value="">Select currency</option>
                                        <option :class=" currencies[i- 1].buy_sell == buy_sell ? '' : 'd-none' "
                                            v-for="i in currencies.length" :key="i" :value="i - 1">
                                            {{ currencies[i- 1].name }} </option>
                                    </select>
                                </div>
                                <div id="card_type" class="mt-4 flex-column">
                                    <label class="label-style">Card type</label>
                                    <select id="cardtype_list" v-model="selectedType" @change="onTypeChange($event)"
                                        class="form-control custom-select select-country-custom-select cardtypelist">
                                        <option disabled value="">Select Card Type</option>
                                        <option v-for="i in types.length" :key="i" :value="i - 1">{{ types[i- 1].name }} </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <span class="d-block mt-3 text-center" style="color: #C4C4C4;line-height: 30px;">Select country and card type to proceed</span>
                    </div>

                    <div class="text-center flex-column mt-4 p-2 align-items-center allcards-container card-price-qty"
                        style="height:285px;">
                        <div class="d-flex flex-row justify-content-around flex-wrap">
                            <div class="text-center">
                                <label for="country" class="label-style">Card
                                    price</label>
                                <select id="cardprice" v-model="selectedQuantity" @change="onQuantityChange($event)" class="custom-select select-country-custom-select cardprice">
                                    <option disabled value="">Select Quantity</option>
                                    <option v-for="i in quantities.length" :key="i" :value="i - 1">{{ quantities[i - 1].value }} </option>
                                </select>
                            </div>
                            <div class="ml-lg-4 mt-4 mt-md-0 text-center">
                                <label for="country" class="label-style">Quantity</label>
                                <div class="d-flex justify-content-center align-items-center">
                                    <div class="text-center" @click="updateQuantity('subtract')"
                                        style="cursor:pointer;width:30px;height:24px;border-radius:40px;background: #000070;">
                                        <svg width="10" height="2" viewBox="0 0 12 2" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0 0H12V2H0V0Z" fill="white" />
                                        </svg>
                                    </div>
                                    <input v-model="cardQuantity" min="1" readonly style="width: 50px;padding:2px;border:0px;" type="number"
                                        class="mx-1 form-control text-center">
                                    <div class="text-center" @click="updateQuantity('add')"
                                        style="cursor:pointer;width:30px;height:24px;border-radius:40px;background: #000070;">
                                        <svg width="10" height="10" viewBox="0 0 14 14" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M14 8H8V14H6V8H0V6H6V0H8V6H14V8Z" fill="white" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-block text-center my-3">
                            <span style="font-size: 16px;font-weight: 500;color: rgba(0, 0, 112, 0.75);">Price
                                Per card:</span> <span>₦{{ price }} </span>
                        </div>
                        <button class="select-card-add-button"  id="addcard_buon" @click="addTrade()" >Add</button>
                        <span class="d-block my-2 my-lg-3 text-center"
                            style="color: rgba(71, 71, 71, 0.5);font-size: 15px;">Scroll up
                            to see
                            the card you just added</span>
                    </div>

                </div>

            </div>
            <div id="list_added_cards" class=" col-12 col-md-6 col-lg-6 my-0">
                <div class="row">
                    <div id="addedCards" class="col-12 mt-3 mb-2 py-3 allcards-container"
                        style="height: 270px !important;width:100%;overflow-y: scroll;">

                        <table class="table table-borderless table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Card Value</th>
                                    <th scope="col">Price per card (₦)</th>
                                    <th scope="col">Qty</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody id="selectedcardslist" class="selectedcardslist disable-scrollbars"
                                style="overflow-y: scroll;">
                                <tr class="my-2" v-for="(trade, index) in trades" :key="trade.key">
                                    <td>{{ trade.cardValue }}</td>
                                    <td>₦{{ trade.cardPrice.toLocaleString() }}</td>
                                    <td>{{ trade.cardQuantity }}</td>
                                    <td>₦{{ trade.cardTotal.toLocaleString() }}</td>
                                    <td @click="removeTrade(index)" class="removeitem"><span>
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 2C6.47 2 2 6.47 2 12C2 17.53 6.47 22 12 22C17.53 22 22 17.53 22 12C22 6.47 17.53 2 12 2ZM17 15.59L15.59 17L12 13.41L8.41 17L7 15.59L10.59 12L7 8.41L8.41 7L12 10.59L15.59 7L17 8.41L13.41 12L17 15.59Z" fill="#CD0B0B"/>
                                    </svg>
                                    </span></td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-if="trades.length == 0" id="nocardavailable" class="text-center"
                            style="color: #C4C4C4;line-height: 30px;font-size: 16px;">No
                            card has
                            been added
                            yet &#x1F615;</div>

                    </div>
                    <div id="total_price"
                        class="col-12 text-center my-3 allcards-container flex-column justify-content-center align-items-center"
                        style="height: 285px;">
                        <span class="d-block text-center gradient-text">
                            {{ buy_sell == 1 ? 'You are paying ' : 'You are getting' }}
                        </span>

                        <span class="d-block text-center mt-3 gradient-text"
                            style="font-size: 38px;">₦{{ total.toLocaleString() }} </span>

                        <button @click="proceed()" :disabled="trades.length == 0 ? true : false" class="btn mt-3 mt-lg-4 proceed-button" id="proceedtoupload">Proceed</button>

                        <span class="d-block mt-4 gradient-text">Turn up time: 5 -
                            20minutes</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
import Event from "../../event.js";
    export default {
        props: ["card", 'buy_sell'],
        data() {
            return {
                currencies: this.card.currencies,
                types: [],
                quantities: [],
                trades: [],
                selectedType: '',
                selectedCurrency: '',
                selectedQuantity: '',
                price: 0,
                cardQuantity: 1,
                total: 0,
            };
        },

        mounted(){
            console.log('DevBoy')
        },

        methods: {
            onCurrencyChange(event) {
                this.selectedType = '';
                this.selectedQuantity = '';
                this.quantities = [];
                this.price = 0;
                this.types = this.currencies[this.selectedCurrency].payment_mediums;
            },

            onTypeChange(event) {
                this.selectedQuantity = '';
                this.quantities = [];
                this.price = 0;
                this.quantities = this.types[this.selectedType].pricing;
            },

            onQuantityChange(event) {
                this.price = 0;
                this.price = this.quantities[this.selectedQuantity].rate;
            },

            updateQuantity(op){
                if (op == 'add') {
                    this.cardQuantity += 1;
                } else if(op == 'subtract' && this.cardQuantity > 1) {
                    this.cardQuantity -= 1;
                }

            },

            addTrade() {
                if (this.price == 0) {
                    console.log('fill in details');
                    return
                }
                let singleTrade = {
                    cardName: this.card.name,
                    currency: this.currencies[this.selectedCurrency].name,
                    cardType: this.types[this.selectedType].name,
                    cardPrice: this.price,
                    cardValue: this.quantities[this.selectedQuantity].value,
                    cardQuantity: this.cardQuantity,
                    cardTotal: parseInt(this.price * this.cardQuantity),
                };
                this.playSound();
                this.trades.push(singleTrade);
                this.tradeTotal();
                this.cardQuantity = 1;
                this.selectedQuantity = '';
                this.price = 0;
            },

            removeTrade(index) {
               this.trades.splice(index, 1);
               this.tradeTotal();
            },

            tradeTotal(){
                let sum = 0;
                $.each(this.trades, function (key, trade) {
                    sum += parseInt(trade.cardTotal);
                });
                this.total = sum;
                return sum;
            },

            playSound() {
                var audio = new Audio("/sound/alert.wav"); // path to file
                audio.play();
            },

            /* When the proceed btn is clicked send the trades to the upload component */
            proceed() {
                Event.$emit("process_trades", [this.trades, this.buy_sell]);
            }
        }

    }

</script>
