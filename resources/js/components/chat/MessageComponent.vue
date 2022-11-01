<template>
  <div class="message col-11" :class="[message.user_id == this.user_id ? 'self-message bg-info' : 'bg-light chat-message']">
    <img v-if="message.type == 1" :src="'/storage/pop/'+ message.message" class="img-fluid bg-white p-1" >
    <p v-else class="body">{{message.message}}</p>
    <i>{{message.humans_time}} ago</i>
  </div>
</template>

<script>
export default {
  props: ["message"],
  data() {
    return {
      user_id: Laravel.user.id
    };
  }
};
</script>
