<template>
    <div class="col-md-6">
        <div class="card ">
            <div class="p-2 bg-plum-plate text-white">
                <span class="card-title text-white ml-2 ">
                    Live Chat
                </span>
            </div>
            <div class="card-body">
                <!-- {{recId}} -->
                <chat-messages-component :recId=r></chat-messages-component>
            </div>
                <chat-form-component :recId=r ></chat-form-component>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['recId'],
        mounted() {

        },
        data(){
            return {
                r: this.recId
            }
        }
    }

</script>
