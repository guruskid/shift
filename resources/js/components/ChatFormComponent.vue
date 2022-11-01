<template>

    <div class="conversation-panel__footer">
        <div class="composer">
            <div class="composer__middle">
                <input type="hidden" v-model="id" />
                <textarea class="form-control" rows="1" placeholder="Type a message..." v-model="body"
                    @keydown="typing"></textarea>

                <div class="composer__middle--photo">
                    <label for="file-upload"><i class="mdi mdi-camera"></i></label>
                </div>
            </div>

            <div class="composer__right">
                <div class="composer__right--send" @click.prevent="sendMessageType">
                    <i class="mdi mdi-send"></i>
                </div>
            </div>
            <div class="d-flex">

                <input required type="file" id="file-upload" ref="pop" @change="fileChange"
                    class="form-control input-round d-none" name="pop" accept="image/*">
            </div>
            <!-- </form> -->
        </div>
    </div>
</template>

<script>
    import Event from "../event.js";

    export default {
        props: ["recId"],
        data() {
            return {
                csrf: "",
                seen: true,
                body: null,
                id: this.recId,
                pop: '',
                messageType: 0,
            };
        },
        mounted() {
            this.csrf = window.Laravel.csrfToken;

            Event.$on("get_user_messages", id => {
                this.id = id;
            });
        },
        methods: {
            typing(e) {
                if (e.keyCode === 13 && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessageType();
                }
            },
            sendMessageType() {
                if (this.messageType == 0) {
                    this.sendMessage();
                } else if(this.messageType == 1) {
                    this.uploadImage();
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

            fileChange(e) {
                var is_validated = false;
                this.seen = true;
                this.pop = this.$refs.pop.files[0];
                if (this.$refs.pop.files[0]) {
                    is_validated = this.validateImage(this.pop);
                    if (is_validated == true) {
                        Event.$emit("file_change", URL.createObjectURL(this.pop));
                        this.messageType = 1;
                    } else {
                        Event.$emit("cancel_preview");
                        this.messageType = 0;
                        alert(is_validated);
                    }

                } else {
                    this.messageType = 0;
                }

            },

            validateImage(pic){
                var error = 0;
                var msg = '';
                if (pic['type'] == "image/png" || pic['type'] == "image/jpg" || pic['type'] == "image/jpeg" || pic['type'] == "image/svg" ) {
                } else {
                    error = 1;
                    msg = 'File type not supported ';
                }

                if (pic['size'] > 7000000 ) {
                    error = 1;
                    msg += 'File too large';
                }

                if (error == 1 ) {
                    return msg;
                } else {
                    return true;
                }
            },

            uploadImage() {
                let messageObj = {
                    id: Date.now(),
                    message: URL.createObjectURL(this.pop),
                    selfMessage: true,
                    type: 3,
                    first_name: Laravel.user.first_name,
                    user_id: Laravel.user.id,
                    humans_time: "seconds"
                }
                Event.$emit("new_message", messageObj);
                this.messageType = 0;

                let formData = new FormData();
                formData.append('pop', this.pop);
                formData.append('user_id', this.id);

                const config = {
                    headers: {
                        'content-type': 'multipart/form-data'
                    }
                }
                axios
                    .post("/pop", formData, config)
                    .then(data => {

                        this.$refs.pop.files[0] == '';
                    })
                    .catch(data => {
                        alert('An error occured while uploading Image');
                    });
            }
        }
    };
</script>

<style>
    .form {
        padding: 8px;
    }

    .form-input {
        width: 100%;
        border: 1px solid #d3e0e9;
        padding: 5px 10px;
        outline: none;
    }

    .notice {
        color: #aaa;
    }
</style>
