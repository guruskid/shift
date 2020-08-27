<template>
    <div class="dropdown ml-2">
        <a href="#" data-toggle="dropdown">
            <i class="fa fa-bell mx-2 fa-2x text-warning"></i>
            <span v-if="unreadCount > 0"
                class="notification-counter  rounded-circle bg-custom px-2 py-1">{{unreadCount}} </span>
        </a>
        <div class="dropdown-menu notifications">
            <div class="d-flex justify-content-between">
                <h5><span class="text-warning">{{unreadCount}}</span> unread notification(s) </h5>
                <a href="/user/notifications" class="text-white">
                    <h5>View All</h5>
                </a>
            </div>
            <div class="notifications-list p-3">
                <div v-for="n in nots" :key="n.id">
                    <hr>
                    <div class="row">
                        <div class="col-8">
                            <div class="media align-items-start ">
                                <i v-if="n.is_seen" class="fa fa-2x fa-envelope-open mr-3 text-white"></i>
                                <i v-else class="fa fa-2x fa-envelope mr-3 text-warning"></i>
                                <div class="media-body">
                                    <h6>{{n.title}}</h6>
                                    <p v-if="n.body.length < 50">{{ n.body }}</p>
                                    <p v-else>{{ n.body.substring(0,50)+". . . ." }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <span class="badge badge-block badge-warning text-white ">{{n.date}}</span>
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
        props: ['notifications', 'unread'],
        data() {
            return {
                convId: 0,
                user_id: Laravel.user.id,
                conv: [],
                nots: this.notifications,
                unreadCount: this.unread
            };
        },

        mounted() {
            this.listen();
            Echo.private(`transaction.${this.user_id}`).listen("NewTransaction", e => {
                this.playSound();
                alert('New transaction initiated');
            });

            Echo.private(`user.${this.user_id}`).listen("TransactionUpdated", e => {
                this.playSound();
                alert("Transaction updated");
            });

        },
        methods: {
            listen() {
                Echo.join("last-message").listen("LastMessage", data => {
                    this.convId = data.message.conversation_id;
                    this.getConversationDetails(this.convId);
                });
            },
            getConversationDetails(id) {
                axios.get("/conversation-details/" + this.convId).then(response => {
                    this.conv = response.data;
                    if (
                        this.conv.user_one == this.user_id ||
                        this.conv.user_two == this.user_id
                    ) {
                        this.playSound();
                        /* alert('You have a new message'); */
                    }
                });
            },

            playSound() {
                var audio = new Audio("/sound/alert.wav"); // path to file
                audio.play();
            }
        }
    };

</script>
