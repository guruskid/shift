<template>
    <div class="p-2 bg-info">
        <form>
            <div class="input-group">
                <input type="hidden" v-model="id" />
                <input id="body" v-model="body" @keydown="typing" type="text" class="input-round form-control"
                    placeholder="Enter text.. hit enter to send" />
                <div class="input-group-append">
                    <button class="btn btn-success btn-round" @click.prevent="sendMessage">
                        <i class="fa fa-arrow-right"></i>
                    </button>
                </div>
                <div class="input-group-prepend">
                    <span class="btn btn-success rounded-circle pb-0">
                        <label for="customFile" ><i class="fa fa-image"></i></label>
                    </span>
                </div>
            </div>
        </form>
        <form method="POST" action="/pop" enctype="multipart/form-data" >
                <input type="hidden" name="_token" :value="csrf">
            <input type="hidden" v-model="id" name="user_id" >
            <div  class="form-group col-11 mr-0 pr-0 ">
                <input required type="file" class="form-control input-round" @change="fileSelected" style="display: none" name="pop" id="customFile" accept="image/*">
            </div>
            <button v-if="seen" class="btn btn-success btn-sm mr-2 mt-1  form-control ">Upload image <i class="fa fa-upload"></i></button>
        </form>
    </div>
</template>

<script>
    import Event from "../../event.js";

    export default {
        data() {
            return {
                seen: false,
                csrf: "",
                body: null,
                id: 1
            };
        },
        mounted() {
            this.csrf = window.Laravel.csrfToken;
        },
        methods: {
            typing(e) {
                if (e.keyCode === 13 && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }
            },
            sendMessage() {
                if (!this.body || this.body.trim() === "") {
                    return;
                }
                let messageObj = this.buildMessage();
                Event.$emit("new_message", messageObj);
                axios
                    .post("/message", {
                        user_id: this.id,
                        body: this.body.trim()
                    })
                    .then(data => {})
                    .catch(data => {
                        console.log(data);
                    });
                this.body = null;
            },
            buildMessage() {
                return {
                    id: Date.now(),
                    message: this.body,
                    selfMessage: true,
                    first_name: Laravel.user.first_name,
                    user_id: Laravel.user.id,
                    humans_time: "seconds"
                };
            },
            fileSelected(){
                this.seen = true;
                alert("File selected, click on upload icon to send image");
            }
        }
    };

</script>

<style>
    .input-round {
        border-radius: 20px 0px 0px 20px !important;
    }

    .btn-round {
        border-radius: 0px 20px 20px 0px !important;
    }

</style>
