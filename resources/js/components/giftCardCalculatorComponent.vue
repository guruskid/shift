<template>
    <div class="col-lg-4 col-md-4 col-sm-12 col-12">
        <div class="calculator-box ">
            <form method="post" id="rate-form">
                <input type="hidden" name="is_crypto" id="is_crypto">
                <div class="d-flex p-0 my-2 bg-custom  rounded">
                    <div class="col-6 p-1 text-center bg-custom-accent rounded-left">
                        <a href="#" class="text-white">Sell to Dantown</a>
                    </div>
                    <div class="col-6 p-1 text-center rounded-right " id="buy-trade">
                        <a href="#" class="text-white">Buy from Dantown</a>
                    </div>
                </div>
                <div class="form-group mx-4">
                    <label for="asset">Asset Type</label>
                    <select class="form-control form-control-sm" name="card">
                        <option value="">{{ card.name }} </option>
                    </select>
                </div>
                <div class="form-group mx-4">
                    <label for="Currency">Currency</label>
                    <select class="form-control form-control-sm" v-model="selectedCurrency" name="country" @change="onCurrencyChange($event)">
                        <option disabled value="">Select currency</option>
                        <option :class=" currencies[i- 1].buy_sell == buy_sell ? '' : 'd-none' "
                            v-for="i in currencies.length" :key="i" :value="i - 1">{{ currencies[i- 1].name }}  </option>
                    </select>
                </div>
                <div class="form-group mx-4">
                    <label for="type">Type</label>
                    <select class="form-control form-control-sm" name="type" v-model="selectedType" @change="onTypeChange($event)">
                        <option disabled value="">Select Card Type</option>
                        <option v-for="i in types.length" :key="i" :value="i - 1">{{ types[i- 1].name }} </option>
                    </select>
                </div>
                <div class="row mx-3 mb-2">
                    <div class="col-lg-12 col-md-6 mb-2" v-for="rate in rates" :key="rate.id" >
                        <div class="card card-body shadow py-2 px-3">
                            <div class=" d-flex justify-content-between">
                                <!-- <i class="fa fa-2x fa-user align-self-center"></i> -->
                                <div class="">
                                    <h5 class="mb-0" ><strong>${{rate.value}}</strong></h5>
                                    <strong class="text-muted mb-0" >₦{{ rate.rate }} </strong>
                                        <!-- <p class="text-muted mb-0" >₦300</p> -->
                                </div>
                                <div>
                                    <span class="badge badge-rounded badge-secondary">-</span>
                                    <input type="text" id="pin" style="width: 20px" class="border-0 text-center" value="1" name="pin" maxlength="2" size="1">
                                    <span class="badge badge-rounded badge-secondary">+</span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>



                <button type="submit" class="btn bg-custom-accent c-rounded btn-block" style="font-size: unset">
                    <img src="" height="20px" id="loader" style="display: none;" alt="">
                    Rate</button>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                buy_sell: 2,
                card: [],
                currencies: [],
                types: [],
                rates: [],
                selectedType: '',
                selectedCurrency: ''
            };
        },

        mounted() {

            axios.get("/user/asset/buy/nike").then(response => {
                this.card = response.data;
                this.currencies = this.card.currencies;
            });
        },

        methods: {
            onCurrencyChange(event) {
                this.selectedType = '';
                this.rates = [];
                this.types = this.currencies[this.selectedCurrency].payment_mediums;
            },

            onTypeChange(event) {
                this.rates = [];
                this.rates = this.types[this.selectedType].pricing;
            }
        }

    }

</script>
