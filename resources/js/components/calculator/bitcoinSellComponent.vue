<template>
    <div class="tab-pane fade show active mx-auto p-3 calculator_form" id="home" role="tabpanel"
        aria-labelledby="home-tab">
        <form action="/user/trade-bitcoin" class="disable-form" method="post">
            <input type="hidden" name="card_id" v-model="card_id">
            <input type="hidden" name="type" value="sell">
            <input type="hidden" name="_token" :value="csrf">
            <input type="hidden" name="current_rate" v-model="btcToUsd">
            <div class="form-group mb-4">
                <label for="inlineFormInputGroupUsername2" style="color: rgba(0, 0, 112, 0.75);">USD equivalent</label>
                <div class="input-group mb-2 mr-sm-2">
                    <div class="input-group-prepend" style="border-radius: 30px;">
                        <div class="input-group-text input_label"> USD</div>
                    </div>
                    <input type="number" required step="any" min="0" name="amount"
                    v-model="usd" @keyup="getRateUsd()"
                     class="form-control bitcoin-input-radius"  >
                </div>
            </div>
            <div class="form-group mb-4">
                <label for="inlineFormInputGroupUsername2" style="color: rgba(0, 0, 112, 0.75);">Bitcoin
                    equivalent</label>
                <div class="input-group mb-2 mr-sm-2">
                    <div class="input-group-prepend" style="border-radius: 30px;">
                        <div class="input-group-text input_label">
                            BTC</div>
                    </div>
                    <input type="number" required step="any" min="0" name="quantity"
                    v-model="btc" @keyup="getRateBtc()"
                        class="form-control bitcoin-input-radius"  >
                </div>
            </div>
            <div class="form-group mb-4">
                <div class="d-flex justify-content-between">
                    <label for="inlineFormInputGroupUsername2" style="color: rgba(0, 0, 112, 0.75);">Naira
                        equivalent</label>
                    <label for="inlineFormInputGroupUsername2"
                        style="color: rgba(0, 0, 112, 0.75);">{{ usdToNaira }}/$</label>
                </div>
                <div class="input-group mb-2 mr-sm-2">
                    <div class="input-group-prepend" style="border-radius: 30px;">
                        <div class="input-group-text input_label">
                            NGN</div>
                    </div>
                    <input type="number" required name="amount_paid" step="any" min="0"
                    v-model="naira" @keyup="getRateNgn()"
                        class="form-control bitcoin-input-radius"  >
                </div>
            </div>

            <div class="d-flex justify-content-around mb-2">
                <span class="text-primary">Charges</span>
                <span class="text-primary">{{ chargeBtc.toFixed(5) }}</span>
                <span class="text-primary">{{ charge }}%</span>
                <span class="text-primary">${{ chargeNgn.toLocaleString() }}</span>
            </div>

            <button class="sell_submit_btn btn w-100 text-white mt-2 bitcoin_calculator_btn">Sell</button>
        </form>
    </div>
</template>

<script>
    export default {
        props: ['rate', 'real_btc', 'card_id', 'charge'],
        data() {
            return {
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                //Input fields
                naira: '',
                usd: '',
                btc: '',

                chargeBtc: 0,
                chargeNgn: 0,

                //rates
                btcToUsd:  this.real_btc,
                usdToNaira: this.rate.sell[0].rate, //our rate
                btcToNaira: '',

            }
        },
        mounted () {
            //console.log(this.real_btc);
            this.btcToNaira = this.btcToUsd * this.usdToNaira;
        },

        methods: {
            //When USD field is updated
            getRateUsd() {
                this.naira = this.usd * this.usdToNaira
                this.btc = this.usd / this.btcToUsd
            },

            /* When btc is updated */
            getRateBtc(){
                this.usd = this.btcToUsd * this.btc
                this.naira = this.btc * this.btcToNaira
            },

            /* When ngn is updated */
            getRateNgn(){
                this.btc = this.naira / this.btcToNaira;
                this.usd = this.naira / this.usdToNaira;
            }
        },


        updated () {
            this.chargeBtc = (this.charge/100) * this.btc
            this.chargeNgn = this.chargeBtc * this.btcToUsd
        },
    }

</script>

<style>

</style>
