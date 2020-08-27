// ChatMessagesComponent.vue
<template>
  <div class="conversation-panel__body" ref="message">
    <div class="container">
      <div class="chatstyle-01">
        <message-component v-for="message in messages" :key="message.id" :message="message"></message-component>

        <div class="ca-send" v-if="is_seen">
          <div class="ca-send__msg-group">
            <div class="ca-send__msgwrapper">
              <div class="ca-send__msg bg-white">
                <div class="ca-send__msg-media">
                  <i class="text-dark">Preview</i>
                  <img :src="src" alt />
                  <a href="#">
                    <span class="badge badge-danger badge-sm" @click="closePreview">Cancel</span>
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
<style>
</style>
<script>
import Event from "../event.js";

export default {
  props: ["recId"],
  data() {
    return {
      messages: [],
      id: this.recId,
      convId: 0,
      is_seen: false,
      src: ""
    };
  },
  mounted() {
    this.getMessages(this.id);

    Event.$on("new_message", message => {
      this.messages.push(message);
      this.readMessages(this.id);
      this.closePreview();
      this.scroll();

    });

    Event.$on("get_user_messages", id => {
      this.messages = [];
      this.id = id;
      Echo.leave("chat." + this.convId);
      this.getMessages(id);
      this.scrollDown();
    });

    Event.$on("file_change", src => {
      this.src = src;
      this.is_seen = true;
      this.scroll();
    });

    Event.$on("cancel_preview", e => {
      this.closePreview();
    });

    Event.$on("scroll_down", id => {
      this.scrollDown();
    });
  },

  methods: {
    listen() {
      Echo.join("chat." + this.convId).listen("MessageCreated", data => {
        if (data.message.user_id == this.id) {
          this.messages.push(data.message);
        }
        this.readMessages(this.id);
      });
    },

    readMessages(id) {
      axios.get("/read-messages/" + id).then(response => {
        /* Emit an event to reload the chats */
        Event.$emit("reload_inbox");
      });
    },

    getMessages(id) {
      axios.get("/message/" + id).then(response => {
        if (response.data != false) {
          this.messages = response.data[0];
          this.convId = this.messages[0].conversation_id;
          this.listen();
          this.readMessages(id);
        } else {
          this.messages = [
            {
              id: 1,
              message: "Hi, no messages with this contact yet",
              humans_time: "seconds"
            }
          ];
        }
      });
    },
    scrollDown() {
      var elem = this.$refs.message;
      var length = this.messages.length;
      elem.scrollTop = length * 2500;
    },

    scroll() {
      var elem = this.$refs.message;
      elem.scrollTop = elem.scrollHeight;
    },

    closePreview() {
      this.src = "";
      this.is_seen = false;
    }
  },
  updated() {
    this.scrollDown();
  }
};
</script>
