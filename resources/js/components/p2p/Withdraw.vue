<template>
    <div>
        <h3 class="text-center">Withdraw Naira</h3>
        <div id="w-naira-form">
            <div class="form-group">
                <div class="d-flex justify-content-between align-items-center">
                <label style="color: #8D8D93;" for="input-deposit-amount">Input amount</label>
                <div style="color: #00B9CD;">Minimum is 1000 NGN</div>
                </div>
                <input
                type="number"
                class="form-control"
                id="input-withdraw-amount"
                placeholder="8000"
                @input="onAmountInput"/>
            </div>
            <div class="my-2" style="color: #8D8D93;">
                <div>Kindly note that you are receiving the sum of <span class="font-weight-bold"><span id="amt">0</span> NGN</span> from</div>
                <div style="color: #8D8D93;">
                Pay-bridge agent: <span class="font-bold" style="color: #000070;">{{account_name}}</span>
                </div>
                <div style="color: #8D8D93;">Bank: <span class="font-bold" style="color: #000070;">{{bank_name}}</span></div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn py-3 rounded-pill w-100" style="background-color: #000070; color: #fff" @click="processWithdrawal">Continue</button>
            </div>
        </div>

        <div id="account-list">
            <div class="container">
                <h4 class="text-center">
                Kindly select the account to receive payment
                </h4>
                <div v-for="account in accounts" v-bind:key="account.id" class="border rounded p-2 my-2" style="border-color: #000070">
                    <div class="form-check">
                        <input
                        class="form-check-input acct-check"
                        type="radio"
                        name="bank-account"
                        :id="'bank-account1'+account.id"
                        :value="account.id"
                        @change="onSelectAcct"/>
                        <label class="form-check-label" :for="'bank-account1'+account.id">
                        {{account.bank_name}} <br />
                        {{account.account_number}}, {{account.account_name}}
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label style="color: #8D8D93;" for="bank-name">Pin</label>
                    <input
                    type="text"
                    class="form-control"
                    id="pin"
                    placeholder="Pin"/>
                </div>

                <div class="text-right">Add new account</div>
                <div class="d-flex justify-content-between align-items-center">
                <div style="color: #1040BA;">Payment window</div>
                <div id="timer">15 Minutes</div>
                </div>

                <div class="form-group">
                <button
                    type="submit"
                    class="btn py-3 rounded-pill w-100 my-3"
                    style="background-color: #000070; color: #fff"
                    @click="completeWithdrawal">
                    Complete Deposit
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
                amount:100,
                pending_withdrawal:false,
                pending_deposit:false,
                account_name:'',
                account_number:'',
                bank_name:'',
                agent_id:'',
                accounts:[],
                account_id:''
            }
        },
        created() {
            this.getStat()
            this.getAgent()
        },
        methods: {
            withdraw() {
                alert('process withdrawal');
                // axios
                //     .get("/admin/update-transaction/" + id + "/" + status)
                //     .then(response => {
                //     if (response.data["success"]) {
                //         this.transactions.splice(this.transactions.indexOf(t), 1);
                //         alert('Trade accepted');
                //     } else {
                //         alert("An error occured");
                //     }
                //     });
                // }
            },
           getStat() {
                axios.get("/trade_naira_api/user/get_stat").then(response => {
                    if (response.data) {
                        this.pending_withdrawal = response.data.pending_withdrawal
                        this.pending_deposit = response.data.pending_deposit
                    }
                });
            },
            getAgent() {
                axios.get("/trade_naira_api/user/agents").then(response => {
                    if (response.data['success']) {
                        this.account_name = response.data.data[0].accounts[0].account_name
                        this.account_number = response.data.data[0].accounts[0].account_number
                        this.bank_name = response.data.data[0].accounts[0].bank_name
                        this.agent_id = response.data.data[0].id
                        // $('.deposit-amt-form').hide()
                        // $('.agent-form').show()
                    }else {
                        swal('Error!', response.data.message ,'error')
                    }
                });
            },
            processWithdrawal() {
                var amount = $('#input-withdraw-amount').val()
                if (isNaN(amount) || amount == '') {
                    swal('Error!', "Please enter the amount you want to deposit" ,'error')
                    return;
                }
                 if (amount < 1000) {
                    swal('Error!', "Minimum deposit is 1000 NGN" ,'error')
                    return;
                }
                if (this.pending_deposit == true ) {
                    swal('Error!', "You currently have a pending withdrawal" ,'error')
                    return;
                }
                axios.get("/trade_naira_api/user/accounts").then(response => {
                    this.accounts = response.data.data;
                    $('#w-naira-form').hide()
                    $('#account-list').show()
                    this.timer()
                }).catch((error) => {
                    if (error.response) {
                        swal('An Error Occured!', error.response.message, 'error')
                    }
                });
            },
            completeWithdrawal() {
                var pin = $('#pin').val()
                if (isNaN(pin) || pin == '') {
                    swal('Error!', "Please enter the pin" ,'error')
                    return;
                }
                axios.post("/trade_naira_api/user/complete_withdrawal",{
                    agent_id:this.agent_id,
                    amount:this.amount,
                    pin:$('#pin').val(),
                    account_id:this.account_id
                }).then(response => {
                    if (response.data['success']) {
                        swal('Good Job!', response.data.message,'success')
                    }else{
                        if (response.data['msg']) {
                            swal('Error!', response.data.msg ,'error')
                        }
                        if (response.data['message']) {
                            swal('Error!', response.data.message ,'error')
                        }
                    }
                }).catch((error) => {
                    if (error.response) {
                        swal('An Error Occured!', error.response.message, 'error')
                    }
                });
            },
            onAmountInput($e) {
                $('#amt').html($e.target.value)
            },
            onSelectAcct($e) {
                this.account_id = $e.target.value
            },
            timer() {
                var countDownDate = new Date().getTime() + 15 * 60 * 1000;

                var x = setInterval(function() {
                    var now = new Date().getTime();
                    var distance = countDownDate - now;

                    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    document.getElementById("timer").innerHTML = days + "d " + hours + "h "
                    + minutes + "m " + seconds + "s ";

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