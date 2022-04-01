<template>
   <div class="container my-3 mt-lg-5 wallet_trx_tabs" id="bitcoin_wallet_send_tab">
    <form @submit.prevent="send()" >
        <div class="row">
            <div class="col-12 col-md-10 col-lg-8 mx-auto" style="border: 1px solid rgba(0, 0, 112, 0.25);">
                <div class="input-group">
                    <!-- <input type="number" step="any" @keyup="getRateUsd()" v-model="usd" required class="form-control" placeholder="0.00"
                        style="border: 0px;">
                    <div class="input-group-append">
                        <span class="input-group-text usd_bg_text pr-1">USD</span>
                        <span class="input-group-text usd_bg_text">
                            <img src="/svg/conversion-arrow.svg" alt="">
                        </span>
                    </div> -->
                    <input type="number" required step="any" @keyup="getRateTrx()" v-model="trx" placeholder="0" class="form-control"
                        style="border: 0px;border-right:0px;">
                    <div class="input-group-prepend">
                        <span class="input-group-text usd_bg_text">TRX</span>
                    </div>
                </div>
            </div>
            <div class="container mt-3m mt-lg-5">
                <div class="row">
                    <div class="col-6 col-md-4 mx-0 p-0 ml-md-auto">
                        <div class="form-group">
                            <label for="" class="networkfee_text">Network fee</label>
                            <select class="custom-select" style="height: 42px;border-radius:0px;">
                                <option selected>Network fee</option>

                            </select>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 mr-md-auto">
                        <div class="d-flex flex-column mx-auto networkfee_container">
                            <span class="d-block align-self-end ethtext">{{ fee }} TRX</span>
                            <span class="d-block align-self-end customfee">Transaction Fee</span>
                        </div>
                    </div>
                    <div class="col-12 col-md-10 mx-auto">
                        <span class="address_input_label">Address</span>
                        <div class="input-group col-12 col-md-7 mx-auto mb-3 mt-4">
                            <input type="text" class="form-control" required v-model="address" @change="getFees()">
                            <div class="input-group-append">
                                <span class="input-group-text" onclick="copywalletaddress('receipientAddress')"
                                    style="cursor:pointer;background: #000070;" id="basic-addon2"><svg width="17"
                                        height="19" viewBox="0 0 17 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M12.0909 0H1.72727C0.777273 0 0 0.777273 0 1.72727V13.8182H1.72727V1.72727H12.0909V0ZM14.6818 3.45455H5.18182C4.23182 3.45455 3.45455 4.23182 3.45455 5.18182V17.2727C3.45455 18.2227 4.23182 19 5.18182 19H14.6818C15.6318 19 16.4091 18.2227 16.4091 17.2727V5.18182C16.4091 4.23182 15.6318 3.45455 14.6818 3.45455ZM14.6818 17.2727H5.18182V5.18182H14.6818V17.2727Z"
                                            fill="white" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-10 mx-auto">
                        <span class="address_input_label">Pin</span>
                        <div class="input-group col-7 mx-auto mb-3 mt-4">
                            <input type="password" v-model="pin" maxlength="4" required class="form-control" >
                        </div>
                    </div>
                    <button type="submit" v-if="!loading" class="btn walletpage_btn text-white mt-3 mt-lg-5">Send </button>
                    <button disabled type="submit" v-else class="btn walletpage_btn text-white mt-3 mt-lg-5">Loading... </button>
                </div>
            </div>
        </div>
    </form>
</div>
</template>

<script>
    export default {
        props: ['usd_trx'],

        data() {
            return {
                trxToUsd : this.usd_trx,
                trx: '',
                usd: '',
                address: '',
                pin: '',
                fee: 0,
                loading: false,
            }
        },

        mounted () {

        },

        methods: {
            //When USD field is updated
            getRateUsd() {
                this.trx = this.usd / this.trxToUsd;
                this.getFees();
            },

            /* When eth is updated */
            getRateTrx(){
                this.usd = this.trxToUsd * this.trx
                this.getFees();
            },

            //Get transfer fees
            getFees(){
                if(this.trx <= 0 || this.address == '' ){
                    return false;
                }
                this.loading = true;
                axios.get(`/user/tron/fees/${this.address}/${this.trx}`)
                .then((res) =>{
                    let x = parseFloat(res.data.fee);
                    this.fee = x.toFixed(5);
                })
                .finally(()=>{
                    this.loading = false;
                });
            },

            send(){
                if (this.trx <= 0) {
                    swal('Oops', 'TRX amount should be greater than 0', 'error');
                    return false;
                }

                this.loading = true;
                axios.post('/user/tron/send', {
                    "amount" : this.trx,
                    "address" : this.address,
                    "pin" : this.pin,
                    "fees" : this.fee
                    })
                .then((res)=>{
                    console.log(res)
                    if (res.data.success) {
                        swal('Great!!', 'Tron sent successfully', 'success');
                        window.location = '/user/tron/wallet';
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
    }
</script>

<style lang="scss" scoped>

</style>
