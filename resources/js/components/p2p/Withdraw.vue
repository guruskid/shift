<template>
    <div>
        <h3 class="text-center">Withdraw Naira</h3>
        <div id="w-naira-form">
            <div class="form-group">
                <div class="d-flex justify-content-between align-items-center">
                    <label style="color: #8D8D93;" for="input-withdraw-amount">Input amount</label>
                    <div style="color: #00B9CD;">Minimum is ₦1,000</div>
                </div>
                <input type="number" class="form-control" id="input-withdraw-amount" placeholder="8000" v-model="amount" />
            </div>
            <div class="mt-2 mb-4" style="color: #8D8D93;">
                <div>Kindly note that you are receiving the sum of <span class="font-weight-bold"><span
                            id="amt">{{ amount >= 1000 ? amount - 100 : '0' }}</span> NGN</span> from</div>
                <div style="color: #8D8D93;">
                    Pay-bridge agent: <span class="font-bold" style="color: #000070;">{{account_name}}</span>
                </div>
                <div style="color: #8D8D93;">Bank: <span class="font-bold" style="color: #000070;">{{bank_name}}</span>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn py-3 rounded-pill w-100" style="background-color: #000070; color: #fff"
                    @click="processWithdrawal">Continue</button>
            </div>
        </div>

        <div id="account-list">
            <div class="container">
                <h4 class="text-center">
                    Kindly select the account to receive payment
                </h4>
                <div v-for="account in accounts" :key="account.id" class="border rounded p-2 my-2"
                    style="border-color: #000070">
                    <div class="form-check">
                        <input class="form-check-input acct-check" type="radio" name="bank-account"
                            :id="'bank-account1'+account.id" :value="account.id" @change="onSelectAcct" />
                        <label class="form-check-label" :for="'bank-account1'+account.id">
                            {{account.bank_name}} <br />
                            {{account.account_number}}, {{account.account_name}}
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label style="color: #8D8D93;" for="bank-name">Pin</label>
                    <input type="password" maxlength="4" v-model="pin" class="form-control" id="pin" placeholder="Pin" />
                </div>

               <a href="/user/account">
                    <div class="text-right" v-if="accounts.length == 0" >Add new account</div>
               </a>

                <div class="form-group">
                    <button id="complete_withdrawal" type="submit"
                        class="btn py-3 rounded-pill w-100 my-3 d-flex justify-content-center"
                        style="background-color: #000070; color: #fff" @click="completeWithdrawal">
                        <span>Complete Withdrawal</span>
                        <span class="ml-2" id="loader"><img src="/images/loader.gif" width="20" height="20px"
                                id="loader" style="display: block;" alt=""></span>
                    </button>
                </div>
            </div>
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
                agent_id: '',
                accounts: [],
                account_id: ''
            }
        },
        created() {
            this.getStat()
            this.getAgent()
        },
        methods: {
            withdraw() {
                alert('process withdrawal');
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
                axios.get("/trade_naira_web/user/agents?type=withdrawal").then(response => {
                    if (response.data['success']) {
                        console.log(response.data);
                        this.account_name = response.data.data[0].accounts.account_name
                        this.account_number = response.data.data[0].accounts.account_number
                        this.bank_name = response.data.data[0].accounts.bank_name
                        this.agent_id = response.data.data[0].id
                        // $('.deposit-amt-form').hide()
                        // $('.agent-form').show()
                    } else {
                        swal('Error!', response.data.message, 'error')
                    }
                });
            },


            processWithdrawal() {
                if (isNaN(this.amount) || this.amount == '') {
                    swal('Error!', "Please enter the amount you want to withdrawal", 'error')
                    return;
                }
                if (this.amount < 1000) {
                    swal('Error!', "Minimum withdrawal is ₦1,000", 'error')
                    return;
                }
                if (this.pending_withdrawal == true) {
                    swal('Error!', "You currently have a pending withdrawal", 'error')
                    return;
                }
                axios.get("/trade_naira_web/user/accounts").then(response => {
                    this.accounts = response.data.data;
                    $('#w-naira-form').hide()
                    $('#account-list').show()
                }).catch((error) => {
                    if (error.response) {
                        swal('An Error Occured!', error.response.message, 'error')
                    }
                });
            },


            completeWithdrawal($e) {
                $("#loader").show()
                $('#complete_withdrawal').prop('disabled', true)
                if (isNaN(this.pin) || this.pin == '') {
                    swal('Error!', "Please enter the pin", 'error')
                    $("#loader").hide()
                    $('#complete_withdrawal').removeAttr('disabled')
                    return;
                }
                axios.post("/trade_naira_web/user/complete_withdrawal", {
                    agent_id: this.agent_id,
                    amount: this.amount,
                    pin: this.pin,
                    account_id: this.account_id,
                    platform:"web"
                }).then(response => {
                    console.log(response);
                    if (response.data['success']) {
                        swal('Good Job!', response.data.message, 'success')
                        window.location.reload()
                    } else {
                        if (response.data['msg']) {
                            swal('Error!', response.data.msg, 'error')
                        }
                        if (response.data['message']) {
                            swal('Error!', response.data.message, 'error')
                        }
                    }
                    $("#loader").hide()
                    $('#complete_withdrawal').removeAttr('disabled')
                }).catch((error) => {
                    console.log(error);
                    if (error.response) {
                        swal('An Error Occured!', error.response.message, 'error')
                    }
                    $("#loader").hide()
                    $('#complete_withdrawal').removeAttr('disabled')
                });
            },


            onSelectAcct($e) {
                this.account_id = $e.target.value
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

                    document.getElementById("timer").innerHTML = days + "d " + hours + "h " +
                        minutes + "m " + seconds + "s ";

                    document.getElementById("timer").innerHTML = minutes + "m " + seconds + "s ";

                    if (distance < 0) {
                        clearInterval(x);
                        document.getElementById("timer").innerHTML = "EXPIRED";
                    }
                }, 1000);
            }
        }
    }

</script>
