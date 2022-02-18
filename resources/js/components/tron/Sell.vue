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
                    <label for="inlineFormInputGroupUsername2" style="color: rgba(0, 0, 112, 0.75);">Tron
                    equivalent</label>
                </div>

                <div class="input-group mb-0 mr-sm-2">
                    <div class="input-group-prepend" style="border-radius: 30px;">
                        <div class="input-group-text input_label">
                            TRX</div>
                    </div>
                    <input type="number" required step="any" min="0" name="quantity"
                    v-model="trx" @keyup="getRatetrx()"
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
                <span class="text-primary">{{ chargeTrx.toFixed(5) }}</span>
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
        props: ['rate', 'trx_usd', 'charge', 'hd'],
        data() {
            return {
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                //Input fields
                naira: '',
                usd: '',
                trx: '',
                chargeTrx: 0,
                chargeNgn: 0,
                //rates
                trxUsd:  this.trx_usd,
                usdToNaira: this.rate, //our rate
                trxToNaira: '',
                loading: false,
                fee: 0,
                total: 0,
                address: this.hd
            }
        },
        mounted () {
            this.trxToNaira = this.trxUsd * this.usdToNaira;
        },
        methods: {
            //When USD field is updated
            getRateUsd() {
                this.naira = this.usd * this.usdToNaira
                this.trx = this.usd / this.trxUsd
                // this.getFees()
            },
            /* When trx is updated */
            getRatetrx(){
                this.usd = this.trxUsd * this.trx
                this.naira = this.trx * this.trxToNaira
                // this.getFees()
            },
            /* When ngn is updated */
            getRateNgn(){
                this.trx = this.naira / this.trxToNaira;
                this.usd = this.naira / this.usdToNaira;
                // this.getFees()
            },

            getTotal() {
                if (this.trx == 0) {
                    this.total = this.fee;
                    return true;
                }
                this.total = parseFloat(this.trx) + parseFloat(this.fee);
            },

            getFees(){
                if (this.trx <= 0) {
                    return false;
                }
                this.loading = true;
                axios.get(`/user/Tron/fees/${this.address}/${this.trx}`)
                    .then((res) => {
                        this.fee = res.data.fee;
                    })
                    .finally(() => {this.getTotal(); this.loading = false});
            },

            sell(){
                if (this.trx < 0) {
                    swal('Oops', 'trx amount should be greater than 0', 'error');
                    return false;
                }

                this.loading = true;
                axios.post('/user/tron/sell', {"amount" : this.trx })
                .then((res)=>{
                    if (res.data.success) {
                        swal('Great!!', 'Tron traded successfully', 'success');
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
            this.chargeTrx = (this.charge/100) * this.trx
            this.chargeNgn = this.chargeTrx * this.trxUsd
        },
    }
</script>

<style>
</style>
