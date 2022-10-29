<template>
  <div class="card">
    <div class="py-2 bg-info">
      <div class="media">
        <!-- <i class="fa fa-user-circle fa-3x ml-3 mr-1 icon-gradient bg-asteroid"></i> -->
        <img src="/live.png" height="50px" class="ml-3 mr-2"  alt="here">
        <div class="media-body">
          <strong class="m-0 text-white">Dantown Assets</strong>
          <p class="m-0 text-light">Consultant</p>
        </div>
      </div>
    </div>
    <div class="card-body scroll-area-lg" id="scroll-msg" ref="message">
      <main-message-component v-for="message in messages" :key="message.id" :message="message">

      </main-message-component>
    </div>
    <form-component></form-component>
  </div>
</template>

<script>
import Event from "../../event.js";

export default {
  data() {
    return {
      messages: [],
      convId: 0
    };
  },
  mounted() {
    axios.get("/message/1").then(response => {
      if(response.data != false){
        this.messages = response.data[0];
        this.convId = this.messages[0].conversation_id;
        this.listen();
      }else{
        this.messages = [
          {
            id: 1,
            message: 'Hi there, welcome to dantown assets, kindly send a message to start chating with a consultant now.',
            humans_time: 'seconds',
          }
        ]
      }
    });

    Event.$on("new_message", message => {
      this.messages.push(message);
    });
  },
  methods: {
    listen() {
      Echo.join("chat." + this.convId).listen("MessageCreated", data => {
        this.messages.push(data.message);
        if (data.message.user_id != Laravel.user_id ) {
            alert('Hi,you have a new message');
        }
      });
    }
  },
  updated() {
    var elem = this.$refs.message;
    elem.scrollTop = elem.scrollHeight;
  }
};
</script>
