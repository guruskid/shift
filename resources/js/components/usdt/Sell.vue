<template>
    <div class="tab-pane fade show active mx-auto p-3 calculator_form" id="home" role="tabpanel" aria-labelledby="home-tab">
        <form @submit.prevent="sell()"  mtrxod="post">
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
               <div class="d-flex justify-content-between" >
                    <label for="inlineFormInputGroupUsername2" style="color: rgba(0, 0, 112, 0.75);">Tether
                    equivalent</label>
                </div>

                <div class="input-group mb-0 mr-sm-2">
                    <div class="input-group-prepend" style="border-radius: 30px;">
                        <div class="input-group-text input_label">
                            USDT</div>
                    </div>
                    <input type="number" required step="any" min="0" name="quantity"
                    v-model="amt" @keyup="getRateAmt()"
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
                        class="form-control bitcoin-input-radius">
                </div>
            </div>

            <div class="d-flex justify-content-around mb-2">
                <span class="text-primary">Charges</span>
                <span class="text-primary">{{ chargeAmt.toFixed(5) }}</span>
                <span class="text-primary">{{ charge }}%</span>
                <span class="text-primary">${{ chargeNgn.toLocaleString() }}</span>
            </div>

            <button v-if="!loading" class="sell_submit_btn btn w-100 text-white mt-2 bitcoin_calculator_btn">Sell</button>
            <button v-else class="sell_submit_btn btn w-100 text-white mt-2 bitcoin_calculator_btn" disabled><i class="spinner-border"></i> </button>
        </form>
    </div>
</template>

<script>
    export default {
        props: ['rate', 'amt_usd', 'charge', 'hd'],
        data() {
            return {
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                //Input fields
                naira: '',
                usd: '',
                amt: '',
                chargeAmt: 0,
                chargeNgn: 0,
                //rates
                amtUsd:  this.amt_usd,
                usdToNaira: this.rate, //our rate
                amtToNaira: '',
                loading: false,
                fee: 0,
                total: 0,
                address: this.hd
            }
        },
        mounted () {
            this.amtToNaira = this.amtUsd * this.usdToNaira;
        },
        methods: {
            //When USD field is updated
            getRateUsd() {
                this.naira = this.usd * this.usdToNaira
                this.amt = this.usd / this.amtUsd
            },
            /* When amt is updated */
            getRateAmt(){
                this.usd = this.amtUsd * this.amt
                this.naira = this.amt * this.amtToNaira
            },
            /* When ngn is updated */
            getRateNgn(){
                this.amt = this.naira / this.amtToNaira;
                this.usd = this.naira / this.usdToNaira;
            },

            getTotal() {
                if (this.amt == 0) {
                    this.total = this.fee;
                    return true;
                }
                this.total = parseFloat(this.amt) + parseFloat(this.fee);
            },

            sell(){
                if (this.amt < 0) {
                    swal('Oops', 'USDT amount should be greater than 0', 'error');
                    return false;
                }

                this.loading = true;
                axios.post('/user/usdt/sell', {"amount" : this.amt })
                .then((res)=>{
                    if (res.data.success) {
                        swal('Great!!', 'USDT traded successfully', 'success');
                        window.location = '/user/transactions';
                    } else {
                        swal('oops!!', res.data.msg, 'error');
                    }
                })
                .catch((e)=>{
                    console.log(e);
                    swal('Oops', 'An error occured, please reload and try again', 'error');
                })
                .finally(()=>{
                    this.loading = false;
                })

            }
        },
        updated () {
            this.chargeAmt = (this.charge/100) * this.amt
            this.chargeNgn = this.chargeAmt * this.amtUsd
        },
    }
</script>

<style>
</style>
