<template>
    <div class="information-panel personal-information-panel">
        <div class="information-panel__head">
            <h5>Contact info</h5>
            <div class="information-panel__closer">
                <i class="mdi mdi-close"></i>
            </div>
        </div>

        <div class="information-panel__body">
            <div class="userprofile-avatar">
                <img class="img-fluid" :src="'/storage/avatar/'+user.dp" alt="">
            </div>

            <div class="userprofile-name">
                <h4>{{user.first_name + " " + user.last_name }}</h4>
            </div>

            <hr>

            <table class="table table-sm table-borderless user-table-info">
                <tr>
                    <td><i class="mdi mdi-cellphone-android"></i></td>
                    <td>{{user.phone}}</td>
                </tr>
                <tr>
                    <td><i class="mdi mdi-email"></i></td>
                    <td>{{user.email}}</td>
                </tr>
                <tr>
                    <td><i class="mdi mdi-account-question"></i></td>
                    <td>{{user.status}}</td>
                </tr>
            </table>

            <hr>

            <div class="accordion accordion-ungrouped mb-3" id="accordionExample">
                <div class="card">
                    <div class="card-header" id="headingOne">
                        <div class="card-title" data-toggle="collapse" role="button" data-target="#collapseOne"
                            aria-expanded="true" aria-controls="collapseOne">
                            <div class="acpanel__heading">
                                <div class="acpanel__left">
                                    <span><i class="mdi mdi-camera-outline"></i></span>
                                    <span>Photos & Media</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne"
                        data-parent="#accordionExample">
                        <div class="card-body">
                            <div class="owl-carousel">
                                <div class="item active" v-if="image.type == 1" v-for="image in images" :key="image.id" >
                                    <a :href="'/storage/pop/' + image.message ">
									<img class="img-fluid" :src="'/storage/pop/' + image.message" alt="">
									</a>
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
		data() {
            return {
                user: {
                    'first_name': '',
                    'last_name': ''
				},
				images: [],
                id: this.recId
            }

        },

        mounted() {
			this.getUserDetails(this.id);
			this.getImages(this.id);

            Event.$on("get_user_messages", id => {
				this.id = id;
				this.images = [];
				this.getUserDetails(this.id);
				this.getImages(this.id);
            });
        },

        methods: {
            getUserDetails(id) {
                axios.get("/user-details/" + id).then(response => {
                    this.user = response.data;
                });
			},
			
			getImages(id) {
                axios.get("/message/" + id).then(response => {
                    if (response.data != false) {
                        this.images = response.data[0];
                    } 
				});
            }
        }
    }
    
</script>

<style>

</style>