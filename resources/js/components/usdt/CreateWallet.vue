<template>
    <div class="row">
        <div class="col-10 px-1 col-lg-4 mx-auto py-2 mt-4"
            style="box-shadow: 0px 2px 10px rgba(207, 207, 207, 0.25);border-radius: 5px;">
            <div class="d-flex align-items-center">
                <div class="mx-3">
                    <img src="/svg/tetherwallet_logo.svg" alt="">
                </div>
                <div>
                    <span class="d-block pb-0 mb-0 choosewallet_selection">USDT</span>
                    <a v-if="!loading" @click.prevent="createWallet()" href="#">Create USDT Wallet</a>
                    <a v-else href="#">Creating wallet, please
                        wait</a>
                </div>
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
                axios.post('/user/usdt/create')

                    .then((res) => {
                        if (res.data.success) {
                            swal('Great!!', 'USDT wallet created successfully', 'success');
                            window.location = '/user/usdt/wallet';
                        } else {
                            swal('oops!!', res.data.msg, 'error');
                        }
                    })
                    .catch((e) => {
                        console.log(e);
                        swal('Oops', 'An error occured, please reload and try again', 'error');
                    })
                    .finally(() => {
                        this.loading = false;
                    })
            }
        },
    }

</script>

<style lang="scss" scoped>

</style>
