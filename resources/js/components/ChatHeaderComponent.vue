<template>
    <div class="conversation-panel__head">
        <div class="conversation-panel__back-button ">
            <i class="mdi mdi-arrow-left"></i>
        </div>

        <div class="conversation-panel__avatar " :class="[userRole == 999 || userRole == 888 ? 'personalinfo-panel-opener' : ''] " >
            <div class="user-avatar user-avatar-rounded">
                <img :src="'/storage/avatar/' + user.dp" alt="">
            </div>
            <div class="conversation__name">
                <div class="conversation__name--title">{{user.first_name + " " + user.last_name }}</div>
            </div>
        </div>

        <div class="conversation__actions" v-if=" userRole == 999 || userRole == 888" >
            <a class="dropdown-item personalinfo-panel-opener" href="javascript:;">
                <div class="action-icon" >
                    <i class="mdi mdi-account-details"></i>
                </div>
            </a>
			<a class="dropdown-item" href="javascript:;">
                <div class="action-icon" data-toggle="modal" data-target="#incomingVideoStart">
                    <i class="mdi mdi-cards"></i>
                </div>
            </a>
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
                id: this.recId,
                userRole: Laravel.user.role,
            }

        },

        mounted() {
            this.getUserDetails(this.id);

            Event.$on("get_user_messages", id => {
                this.id = id;
                this.getUserDetails(this.id);
            });
        },

        methods: {
            getUserDetails(id) {
                axios.get("/user-details/" + id).then(response => {
                    this.user = response.data;
                });
            }
        }
    }
</script>