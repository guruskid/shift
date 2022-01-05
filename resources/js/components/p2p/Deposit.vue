<template>
    <div>
        <div class="container my-2 deposit-amt-form">
            <form @submit.prevent="getAgent">
                <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center">
                        <label for="input-deposit-amount" style="color: #8D8D93;" aria-required="true" required>Input
                            amount</label>
                        <div style="color: #00B9CD;">Minimum is ₦1,000 </div>
                    </div>
                    <input type="number" class="form-control" v-model="amount" id="input-deposit-amount" placeholder="8000" />
                    <div>Please note that <span class="font-weight-bold">Pay-bridge</span> agents are verified by
                        Dantown.</div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn py-3 rounded-pill w-100"
                        style="background-color: #000070; color: #fff">
                        Continue
                    </button>
                </div>
            </form>
        </div>

        <div class="container agent-form">
            <h4 class="text-center">Kindly Make a payment of <span class="font-weight-bold">{{amount}}</span> to</h4>
            <form @submit.prevent="completeDeposit">
                <div class="form-group">
                    <label style="color: #8D8D93;" for="account-name">Name of Account</label>
                    <input type="text" class="form-control" id="account-name" placeholder="Ayoleghi"
                        v-model="this.account_name" disabled />
                </div>
                <div class="form-group">
                    <label style="color: #8D8D93;" for="account-number">Account number</label>
                    <input type="text" class="form-control" id="account-number" placeholder="Ayoleghi"
                        v-model="this.account_number" disabled />
                </div>
                <div class="form-group">
                    <label style="color: #8D8D93;" for="bank-name">Bank Name</label>
                    <input type="text" class="form-control" id="bank-name" placeholder="Ayoleghi"
                        v-model="this.bank_name" disabled />
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div style="color: #1040BA;">Payment window</div>
                    <div id="timer-d">15 Minutes</div>
                </div>
                <div class="text-center" style="color: #8D8D93;">
                    <span class="font-weight-bold" style="color: #000070;">Note:</span> Deposits must come from a name
                    that matches your Dantown’s account
                    name
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="deposit-check"
                        @change="depositCheck" />
                    <label class="form-check-label" for="deposit-check">
                        By clicking, you agree to have made payment to the Pay-bridge agent
                    </label>
                </div>
                <div class="form-group">
                    <button id="complete_deposit" type="submit"
                        class="btn py-3 rounded-pill w-100 my-3 d-flex justify-content-center"
                        style="background-color: #000070; color: #fff" disabled>
                        <span>Complete Deposit</span>
                        <span class="ml-2" id="loader-d"><img src="/images/loader.gif" width="20" height="20px"
                                id="loader" style="display: block;" alt=""></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                amount: '',
                pin: '',
                pending_withdrawal: false,
                pending_deposit: false,
                account_name: '',
                account_number: '',
                bank_name: '',
                agent_id: ''
            }
        },
        created() {
            // this.getName()
            this.getStat()
        },
        methods: {
            depositCheck($e) {
                if ($e.target.checked) {
                    $('#complete_deposit').removeAttr("disabled");
                } else {
                    $('#complete_deposit').prop("disabled", true);
                }
            },
            getStat() {
                axios.get("/trade_naira_web/user/get_stat").then(response => {
                    if (response.data) {
                        this.pending_withdrawal = response.data.pending_withdrawal
                        this.pending_deposit = response.data.pending_deposit
                    }
                });
            },

            getAgent() {
                

                if (isNaN(this.amount) || this.amount == '') {
                    swal('Error!', "Please enter the amount you want to deposit", 'error')
                    return;
                }
                if (this.amount < 1000) {
                    swal('Error!', "Minimum deposit is 1000 NGN", 'error')
                    return;
                }
                if (this.pending_deposit == true) {
                    swal('Error!', "You currently have a pending deposit", 'error')
                    return;
                }
                axios.get("/trade_naira_web/user/agents?type=deposit").then(response => {
                    if (response.data['success']) {
                        this.account_name = response.data.data[0].accounts.account_name
                        this.account_number = response.data.data[0].accounts.account_number
                        this.bank_name = response.data.data[0].accounts.bank_name
                        this.agent_id = response.data.data[0].id
                        $('.deposit-amt-form').hide()
                        $('.agent-form').show()
                        this.timer()
                    } else {
                        swal('Error!', response.data.message, 'error')
                    }
                });
            },

            completeDeposit() {
                $("#loader-d").show()
                $('#complete_deposit').prop('disabled', true)
                axios.post("/trade_naira_web/user/complete_deposit", {
                    agent_id: this.agent_id,
                    amount: this.amount,
                    // pin: this.pin
                }).then(response => {
                    if (response.data['success']) {
                        swal('Good Job!', response.data.message, 'success')
                        window.location.reload();
                    } else {
                        swal('Error!', response.data.message, 'error')
                    }
                    $("#loader-d").hide()
                    $('#complete_deposit').removeAttr('disabled')
                }).catch((error) => {
                    if (error.response) {
                        swal('An Error Occured!', error.response.message, 'error')
                    }
                    $("#loader-d").hide()
                    $('#complete_deposit').removeAttr('disabled')
                });
            },

            timer() {
                var countDownDate = new Date().getTime() + 15 * 60 * 1000;

                var x = setInterval(function () {
                    var now = new Date().getTime();
                    var distance = countDownDate - now;

                    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    document.getElementById("timer-d").innerHTML = days + "d " + hours + "h " +
                        minutes + "m " + seconds + "s ";

                    document.getElementById("timer-d").innerHTML = minutes + "m " + seconds + "s ";

                    if (distance < 0) {
                        clearInterval(x);
                        document.getElementById("timer-d").innerHTML = "EXPIRED";
                    }
                }, 1000);
            }
        }
    }

</script>
