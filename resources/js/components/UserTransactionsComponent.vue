<template>
    <div class="modal fade videocall-modal CallDialogFullscreen-sm" id="incomingVideoStart" tabindex="-1" role="dialog"
        aria-labelledby="incomingVideoStart" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl " role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="icvideocallwrapper">
                        <div class="icvideo-contact">
                            <table class="align-middle mb-4 table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">Asset type</th>
                                        <th class="text-center">Tran. type</th>
                                        <th class="text-center">Asset value</th>
                                        <th class="text-center">Cash value</th>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="t in transactions" :key="t.id" >
                                        <td class="text-center text-muted">{{t.uid}}</td>
                                        <td class="text-center">{{t.card}}</td>
                                        <td class="text-center">{{t.type}}</td>
                                        <td class="text-center">$ {{t.amount}}</td>
                                        <td class="text-center">N {{t.amount_paid}}</td>
                                        <td class="text-center">{{t.created_at}} </td>
                                        <td class="text-center">{{t.status}} </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="icvideo-actions">
                            <div class="icvideo-actions__middle">
                                <div class="iconbox btn-hovered-light bg-danger" data-dismiss="modal"
                                    data-toggle="tooltip" data-placement="top" title="Close">
                                    <i class="iconbox__icon text-white mdi mdi-close"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
	import Event from "../event.js";
    export default {
		props: ["recId"],
		data(){
			return {
				transactions: [],
				id: this.recId,
			}
		},
		mounted() {
			this.getTransactions(this.id);

			Event.$on("get_user_messages", id => {
				this.transactions = [];
                this.id = id;
                this.getTransactions(this.id);
            });
		},

		methods: {
			getTransactions(id){
				axios.get("/user-transactions/" + id).then(response => {
                    this.transactions = response.data;
                });
			}
		}
    }
</script>

<style>

</style>
