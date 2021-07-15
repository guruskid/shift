<template>
    <div class="tab-pane fade show  mx-auto p-3 calculator_form" id="profile" role="tabpanel"
        aria-labelledby="profile-tab">
        <form action="/user/trade-bitcoin" class="disable-form" method="post">
            <input type="hidden" name="card_id" v-model="card_id">
            <input type="hidden" name="type" value="buy">
            <input type="hidden" step="any" name="current_rate" v-model="btcToUsdBuy">
            <input type="hidden" name="_token" :value="csrf">
            <div class="form-group mb-4">
                <label for="inlineFormInputGroupUsername2" style="color: rgba(0, 0, 112, 0.75);">USD
                    equivalent</label>
                <div class="input-group mb-2 mr-sm-2">
                    <div class="input-group-prepend" style="border-radius: 30px;">
                        <div class="input-group-text input_label"> USD</div>
                    </div>
                    <input type="number" required step="any" min="0" name="amount" v-model="usdBuy"
                        @keyup="getRateUsdBuy()" class="form-control bitcoin-input-radius">
                </div>
            </div>
            <div class="form-group mb-4">
                <div class="d-flex justify-content-between" >
                    <label for="inlineFormInputGroupUsername2" style="color: rgba(0, 0, 112, 0.75);">Bitcoin
                    equivalent</label>

                    <div>
                        <span class="btn btn-sm btn-primary rounded-pill" @click="btcPercentage(25)"> 25% </span>
                        <span class="btn btn-sm btn-primary rounded-pill" @click="btcPercentage(50)"> 50% </span>
                        <span class="btn btn-sm btn-primary rounded-pill" @click="btcPercentage(75)"> 75% </span>
                        <span class="btn btn-sm btn-primary rounded-pill" @click="btcPercentage(100)"> 100% </span>
                    </div>

                </div>
                <div class="input-group mb-2 mr-sm-2">
                    <div class="input-group-prepend" style="border-radius: 30px;">
                        <div class="input-group-text input_label">BTC</div>
                    </div>
                    <input type="number" required step="any" min="0" name="quantity" v-model="btcBuy"
                        @keyup="getRateBtcBuy()" class="form-control bitcoin-input-radius">
                </div>
            </div>
            <div class="form-group mb-4">
                <div class="d-flex justify-content-between">
                    <label for="inlineFormInputGroupUsername2" style="color: rgba(0, 0, 112, 0.75);">Naira
                        equivalent</label>
                    <label for="inlineFormInputGroupUsername2"
                        style="color: rgba(0, 0, 112, 0.75);">{{ usdToNairaBuy }}/$</label>
                </div>
                <div class="input-group mb-2 mr-sm-2">
                    <div class="input-group-prepend" style="border-radius: 30px;">
                        <div class="input-group-text input_label">
                            NGN</div>
                    </div>
                    <input type="number" required name="amount_paid" step="any" min="0" v-model="nairaBuy"
                        @keyup="getRateNgnBuy()" class="form-control bitcoin-input-radius">
                </div>
            </div>
            <button class="sell_submit_btn btn w-100 text-white mt-2 bitcoin_calculator_btn">Buy</button>
        </form>
    </div>
</template>

<script>
    export default {
        props: ['rate', 'real_btc', 'card_id'],
        data() {
            return {
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                //Input fields
                nairaBuy: '',
                usdBuy: '',
                btcBuy: '',

                //rates
                btcToUsdBuy: this.real_btc,
                usdToNairaBuy: this.rate.buy[0].rate, //our rate
                btcToNairaBuy: '',

            }
        },
        mounted() {
            console.log(this.usdToNairaBuy);
            this.btcToNairaBuy = this.btcToUsdBuy * this.usdToNairaBuy;
        },

        methods: {
            //When USD field is updated
            getRateUsdBuy() {
                this.nairaBuy = this.usdBuy * this.usdToNairaBuy
                this.btcBuy = this.usdBuy / this.btcToUsdBuy
            },

            /* When btc is updated */
            getRateBtcBuy() {
                this.usdBuy = this.btcToUsdBuy * this.btcBuy
                this.nairaBuy = this.btcBuy * this.btcToNairaBuy
            },

            /* When ngn is updated */
            getRateNgnBuy() {
                this.btcBuy = this.nairaBuy / this.btcToNairaBuy;
                this.usdBuy = this.nairaBuy / this.usdToNairaBuy;
            },

            btcPercentage(percentage) {
                this.btcBuy = percentage;
                this.usdBuy = percentage;
                this.nairaBuy = percentage
            }
        },
    }

</script>

<style>

</style>
