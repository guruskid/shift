<template>
    <div>
        <div class="ca-send" v-if="message.user_id == this.user_id ">
            <div class="ca-send__msg-group">
                <div class="ca-send__msgwrapper">

                    <div class="ca-send__msg  image" v-if="message.type == 1">
                        <div class="ca-send__msg-media ">
                            <a :href="'/storage/pop/' + message.message " target="_blank">
                                <img :src="'/storage/pop/'+ message.message" height="1000px"  alt="">
                                <span class="badge badge-light view-badge ">View</span>
                            </a>
                        </div>
                    </div>
                    <div class="ca-send__msg" v-else-if="message.type == 3">
                        <div class="ca-send__msg-media">
                            <img :src="message.message" alt="">
                        </div>
                    </div>
                    <div class="ca-send__msg" v-else>{{message.message}}</div>
                </div>
                <div class="metadata">
                    <span class="time">{{message.humans_time}} ago</span>
                </div>
            </div>
        </div>


        <div class="ca-received" v-else>
            <div class="ca-received__msg-group">
                <div class="ca-received__msg image" v-if="message.type == 1">
                    <div class="ca-received__msg-media-group ">
                        <div class="ca-received__msg-media">
                            <a :href="'/storage/pop/' + message.message " target="_blank">
                                <img :src="'/storage/pop/'+ message.message"  alt="">
                                <span class="badge badge-info view-badge">View</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="ca-received__msg" v-else>{{message.message}}</div>
                <div class="metadata">
                    <span class="time">{{message.humans_time}} ago</span>
                </div>
            </div>
        </div>



    </div>
</template>


<script>
import Event from "../event.js";
    export default {
        props: ["message"],

        data() {
            return {
                user_id: Laravel.user.id,
            };
        },

        methods: {
            shout(){
                Event.$emit("scroll_down");
            }
        }
    };
</script>

<style>
    .image {
        min-height: 350px;
    }
    /* Small devices (portrait tablets and large phones, 600px and up) */
    @media only screen and (min-width: 600px) {
        .image {
            min-height: 300px;
        }
    }

    .view-badge{
        position: absolute;
        bottom: 0;
        left: 5;
        margin-bottom: 10px;
    }
</style>
