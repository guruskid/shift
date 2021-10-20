<template>
    <div class="tab-pane fade show active mx-auto p-3 calculator_form" id="home" role="tabpanel" aria-labelledby="home-tab">
        <form @submit.prevent="sell()"  method="post">
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
                    <label for="inlineFormInputGroupUsername2" style="color: rgba(0, 0, 112, 0.75);">Ethereum
                    equivalent</label>
                </div>

                <div class="input-group mb-0 mr-sm-2">
                    <div class="input-group-prepend" style="border-radius: 30px;">
                        <div class="input-group-text input_label">
                            ETH</div>
                    </div>
                    <input type="number" required step="any" min="0" name="quantity"
                    v-model="eth" @keyup="getRateeth()"
                        class="form-control bitcoin-input-radius"  >
                </div>
                <div class="d-flex justify-content-between">
                    <small><strong>Fee: </strong>{{ fee.toFixed(5) }}</small>
                    <small><strong>Total: </strong>{{ total.toFixed(5) }}</small>
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
                <span class="text-primary">{{ chargeEth.toFixed(5) }}</span>
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
        props: ['rate', 'eth_usd', 'charge', 'hd'],
        data() {
            return {
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                //Input fields
                naira: '',
                usd: '',
                eth: '',
                chargeEth: 0,
                chargeNgn: 0,
                //rates
                ethUsd:  this.eth_usd,
                usdToNaira: this.rate, //our rate
                ethToNaira: '',
                loading: false,
                fee: 0,
                total: 0,
                address: this.hd
            }
        },
        mounted () {
            this.ethToNaira = this.ethUsd * this.usdToNaira;
        },
        methods: {
            //When USD field is updated
            getRateUsd() {
                this.naira = this.usd * this.usdToNaira
                this.eth = this.usd / this.ethUsd
                this.getFees()
            },
            /* When eth is updated */
            getRateeth(){
                this.usd = this.ethUsd * this.eth
                this.naira = this.eth * this.ethToNaira
                this.getFees()
            },
            /* When ngn is updated */
            getRateNgn(){
                this.eth = this.naira / this.ethToNaira;
                this.usd = this.naira / this.usdToNaira;
                this.getFees()
            },

            getTotal() {
                if (this.eth == 0) {
                    this.total = this.fee;
                    return true;
                }
                this.total = parseFloat(this.eth) + parseFloat(this.fee);
            },

            getFees(){
                if (this.eth <= 0) {
                    return false;
                }
                this.loading = true;
                axios.get(`/user/ethereum/fees/${this.address}/${this.eth}`)
                    .then((res) => {
                        this.fee = res.data.fee;
                    })
                    .finally(() => {this.getTotal(); this.loading = false});
            },

            sell(){
                if (this.eth < 0) {
                    swal('Oops', 'eth amount should be greater than 0', 'error');
                    return false;
                }

                this.loading = true;
                axios.post('/user/ethereum/sell', {"amount" : this.eth })
                .then((res)=>{
                    if (res.data.success) {
                        swal('Great!!', 'Ethereum traded successfully', 'success');
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
            this.chargeEth = (this.charge/100) * this.eth
            this.chargeNgn = this.chargeEth * this.ethUsd
        },
    }
</script>

<style>
</style>
