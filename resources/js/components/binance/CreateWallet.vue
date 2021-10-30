<template>
    <div class="modal fade " id="new-ethereum-wallet">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content  c-rounded">

                <form @submit.prevent="createWallet">
                    <div class="modal-body p-4">
                        <h5 class="modal-title mb-3">Enter Wallet Pin</h5>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Wallet Pin </label>
                                    <input type="password" class="form-control" v-model="pin" required minlength="4"
                                        maxlength="4">
                                </div>
                            </div>

                        </div>

                        <button :disabled="loading" class="btn btn-block c-rounded bg-custom-gradient">
                            {{ loading ? 'Processing...' : 'Create BNB Wallet' }} </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                loading: false,
                pin: '',
            }
        },

        methods: {
            createWallet() {
                this.loading = true;
                axios.post('/user/binance/create', {'pin' : this.pin } )

                .then((res)=>{
                    if (res.data.success) {
                        swal('Great!!', 'Binance walet created successfully', 'success');
                        window.location = '/user/binance/wallet';
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
