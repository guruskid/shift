<template>
    <div class="tab-pane fade show active" id="caChats" role="tabpanel" aria-labelledby="caChatsTab">
        <div class="nav-style-2">
            <div class="tab-content" id="caChatsTabInsideContent">
                <div class="tab-pane fade show active" id="personal-chat" role="tabpanel"
                    aria-labelledby="personal-chat-tab">
                    <div class="sidebar-userlist">
                        <ul class="list-unstyled userSearchList">
                            <li v-for="user in users" :key="user.id" @click="userMessage(user.withUser.id)">
                                <div class="conversation unread">
                                    <div class="user-avatar user-avatar-rounded ">
                                        <img :src="'/storage/avatar/'+user.withUser.dp" alt="User_DP">
                                    </div>
                                    <div class="conversation__details">
                                        <div class="conversation__name">
                                            <div class="conversation__name--title">
                                                {{user.withUser.first_name + " " + user.withUser.last_name}}</div>
                                            <div class="conversation__time">{{user.thread.humans_time}}</div>
                                        </div>
                                        <div class="conversation__message">
                                            <div class="conversation__message-preview">
                                                <span class="tick">
                                                    <img v-if="user.thread.is_seen == 1 && user.thread.user_id == user_id" src="/chat/assets/images/tick/tick-read.svg" alt="">
                                                    <img v-else-if="user.thread.user_id == user_id " src="/chat/assets/images/tick/tick-delivered.svg" alt="">
                                                </span>
                                                <span>
                                                    {{user.thread.message}}
                                                </span>
                                            </div>
                                            <span class="badge badge-primary badge-rounded"
                                                v-if="user.unread > 0 ">{{user.unread}}</span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Event from "../event.js";
export default {
  props: ["inboxes"],
  data() {
    return {
      users: this.inboxes,
      user_id: Laravel.user.id
    };
  },

  mounted() {
    this.inbox();
    this.listen();

    Event.$on("new_message", message => {
      Event.$emit("reload_inbox");
    });

    Event.$on("reload_inbox", message => {
      this.inbox();
    });
  },

  methods: {
    inbox() {
      axios.get("/inbox").then(response => {
        this.users = response.data;
      });
    },

    listen() {
      Echo.join("last-message").listen("LastMessage", data => {
        this.inbox();
      });
    },
    playSound() {
      var audio = new Audio("/sound/alert.wav");
      audio.play();
    },

    userMessage(id) {
      Event.$emit("get_user_messages", id);
    }
  }
};
</script>

<style>
</style>