<template>
    <div>
        <!-- Txn detail modal id found in the user blade layout file -->
        <!-- All transactions table -->
        <table v-if="value != -1 " class="align-middle mb-0 table transactions-table bg-custom-accent table-hover">
            <thead>
                <tr>
                    <th class="text-center text-custom">ID</th>
                    <th class="text-center text-custom">Asset type</th>
                    <th class="text-center text-custom">Tran. type</th>
                    <th class="text-center text-custom">Units</th>
                    <th class="text-center text-custom">Cash value</th>
                    <th class="text-center text-custom">Date</th>
                    <th class="text-center text-custom">Status</th>

                </tr>
            </thead>
            <tbody>
                <tr v-for="t in transactions" :key="t.id">
                    <td class="text-center text-dark">{{t.uid}}</td>
                    <td class="text-center text-dark">{{t.card}}</td>
                    <td class="text-center text-dark">{{t.type}}</td>
                    <td class="text-center text-dark">{{t.amount}}</td>
                    <td class="text-center text-dark">N{{t.amount_paids}}</td>
                    <td class="text-center text-dark">{{t.created_ats}}</td>
                    <td v-if="t.status == 'waiting' " class="text-center text-dark">
                        {{t.stats}}
                        <a :href="'/user/view-transaction/' + t.id +'/' + t.uid ">
                            <button class="btn btn-success">Upload </button>
                        </a>
                    </td>
                    <td v-else class="text-center text-dark">
                        {{t.stats}}
                        <a data-toggle="modal" data-target="#txn-detail-modal" @click="showTxnDetail(t)">
                            <button class="btn btn-success">View</button>
                        </a>
                    </td>


                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th colspan="2"></th>
                    <th class="text-center">{{value}}</th>
                    <th class="text-center">N{{amount}}</th>
                    <th colspan="2"></th>
                </tr>
            </tfoot>
        </table>

        <!-- Dashboard transactions table -->
        <table v-else class="align-middle mb-0 table table-borderless bg-custom-accent">
            <thead>
                <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Asset type</th>
                    <th class="text-center">Tran. type</th>
                    <th class="text-center">Units</th>
                    <th class="text-center">Cash value</th>
                    <th class="text-center">Date</th>
                    <th class="text-center">Status</th>

                </tr>
            </thead>
            <tbody>
                <tr v-for="t in transactions" :key="t.id">
                    <td class="text-center">{{t.uid}}</td>
                    <td class="text-center">{{t.card}}</td>
                    <td class="text-center">{{t.type}}</td>
                    <td class="text-center">{{t.amount}}</td>
                    <td class="text-center">₦{{t.amount_paids}}</td>
                    <td class="text-center text-white">{{t.created_ats}}</td>
                    <td v-if="t.status == 'waiting' " class="text-center text-white">
                        {{t.stats}}
                        <a :href="'/user/view-transaction/' + t.id +'/' + t.uid ">
                            <button class="btn btn-success">Upload </button>
                        </a>
                    </td>
                    <td v-else class="text-center text-white">
                        {{t.stats}}
                        <a data-toggle="modal" data-target="#txn-detail-modal" @click="showTxnDetail(t)">
                            <button class="btn btn-success">View</button>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>


    </div>
</template>

<script>
    import Event from "../event.js";
    export default {
        props: ["trans", "val", "amt"],
        data() {
            return {
                userId: Laravel.user.id,
                transactions: this.trans,
                value: this.val,
                amount: this.amt
            };
        },

        mounted() {
            Echo.private(`user.${this.userId}`).listen("TransactionUpdated", e => {
                location.reload();
            });
        },

        methods: {
            showTxnDetail(txn) {
                console.log(txn)
                $('#d-txn-uid').text(txn.uid)
                $('#d-txn-asset-type').text(txn.card)
                $('#d-txn-txn-type').text(txn.type)
                $('#d-txn-country').text(txn.country)
                $('#d-txn-amount').text(txn.amount)
                $('#d-txn-amt-paid').text('₦' + txn.amount_paids)
                $('#d-txn-status').text(txn.status)
                $('#d-txn-date').text(txn.created_ats)
            }
        }
    };

</script>

<style>
</style>
