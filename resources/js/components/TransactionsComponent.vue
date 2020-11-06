<template>
    <div>
        <!-- Txn detail modal id found in the user blade layout file -->
        <!-- All transactions table -->
        <table v-if="value != -1 " class="align-middle mb-0 table transactions-table bg-custom-accent table-hover">
            <thead>
                <tr>
                    <th class="text-center text-custom border-0">ID</th>
                    <th class="text-center text-custom border-0">Asset type</th>
                    <th class="text-center text-custom border-0">Tran. type</th>
                    <th class="text-center text-custom border-0">Units</th>
                    <th class="text-center text-custom border-0">Quantity</th>
                    <th class="text-center text-custom border-0">Cash value</th>
                    <th class="text-center text-custom border-0">Date</th>
                    <th class="text-center text-custom border-0">Status</th>

                </tr>
            </thead>
            <tbody>
                <tr v-for="t in transactions" :key="t.id">
                    <td class="text-center bg-custom-accent">{{t.uid}}</td>
                    <td class="text-center bg-custom-accent">{{t.card}}</td>
                    <td class="text-center bg-custom-accent">{{t.type}}</td>
                    <td class="text-center bg-custom-accent">{{t.amount}}</td>
                    <td class="text-center bg-custom-accent">{{t.quantity}}</td>
                    <td class="text-center bg-custom-accent">₦{{t.amount_paids}}</td>
                    <td class="text-center bg-custom-accent">{{t.created_ats}}</td>
                    <td v-if="t.status == 'waiting' && t.type == 'sell' " class="text-center bg-custom-accent">
                        <a :href="'/user/view-transaction/' + t.id +'/' + t.uid ">
                            <button class="btn btn-info">Upload </button>
                        </a>
                    </td>
                    <td v-else-if="t.status == 'success'" class="text-center bg-custom-accent">
                        <a data-toggle="modal" data-target="#txn-detail-modal" @click="showTxnDetail(t)">
                        <button title="Click to view" class="btn btn-success text-capitalize ">
                            {{t.stats}}
                        </button>
                        </a>
                    </td>
                    <td v-else-if="t.status == 'declined'" class="text-center bg-custom-accent">
                        <a data-toggle="modal" data-target="#txn-detail-modal" @click="showTxnDetail(t)">
                        <button title="Click to view" class="btn btn-danger text-capitalize ">
                            {{t.stats}}
                        </button>
                        </a>
                    </td>
                    <td v-else-if="t.status == 'in progress'" class="text-center bg-custom-accent">
                        <a data-toggle="modal" data-target="#txn-detail-modal" @click="showTxnDetail(t)">
                        <button title="Click to view" class="btn btn-warning text-capitalize ">
                            {{t.stats}}
                        </button>
                        </a>
                    </td>
                    <td v-else class="text-center bg-custom-accent">
                        <a data-toggle="modal" data-target="#txn-detail-modal" @click="showTxnDetail(t)">
                        <button title="Click to view" class="btn btn-secondary text-capitalize ">
                            {{t.stats}}
                        </button>
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
                    <th class="text-center">Quantity</th>
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
                    <td class="text-center">{{t.quantity}}</td>
                    <td class="text-center">₦{{t.amount_paids}}</td>
                    <td class="text-center text-white">{{t.created_ats}}</td>
                    <td v-if="t.status == 'waiting' && t.type == 'sell' " class="text-center text-dark">
                        <a :href="'/user/view-transaction/' + t.id +'/' + t.uid ">
                            <button class="btn btn-info">Upload </button>
                        </a>
                    </td>
                    <td v-else-if="t.status == 'success'" class="text-center text-dark">
                        <a data-toggle="modal" data-target="#txn-detail-modal" @click="showTxnDetail(t)">
                        <button title="Click to view" class="btn btn-success text-capitalize ">
                            {{t.stats}}
                        </button>
                        </a>
                    </td>
                    <td v-else-if="t.status == 'declined'" class="text-center text-dark">
                        <a data-toggle="modal" data-target="#txn-detail-modal" @click="showTxnDetail(t)">
                        <button title="Click to view" class="btn btn-danger text-capitalize ">
                            {{t.stats}}
                        </button>
                        </a>
                    </td>
                    <td v-else-if="t.status == 'in progress'" class="text-center text-dark">
                        <a data-toggle="modal" data-target="#txn-detail-modal" @click="showTxnDetail(t)">
                        <button title="Click to view" class="btn btn-warning text-capitalize ">
                            {{t.stats}}
                        </button>
                        </a>
                    </td>
                    <td v-else class="text-center text-dark">
                        <a data-toggle="modal" data-target="#txn-detail-modal" @click="showTxnDetail(t)">
                        <button title="Click to view" class="btn btn-secondary text-capitalize ">
                            {{t.stats}}
                        </button>
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
                $('#d-txn-card-type').text(txn.card_type)
                $('#d-txn-txn-type').text(txn.type)
                $('#d-txn-country').text(txn.country)
                $('#d-txn-amount').text(txn.amount)
                $('#d-txn-rate').text(txn.card_price)
                $('#d-txn-quantity').text(txn.quantity)
                $('#d-txn-amt-paid').text('₦' + txn.amount_paids)
                $('#d-txn-status').text(txn.status)
                $('#d-txn-date').text(txn.created_ats)
            }
        }
    };

</script>

<style>
</style>
