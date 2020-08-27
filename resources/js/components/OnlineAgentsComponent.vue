<template>
    <div class="tab-pane fade show " id="caChats2" role="tabpanel" aria-labelledby="caChatsTab">
        <div class="nav-style-2">
            <div class="tab-content" id="caChatsTabInsideContent">
                <div class="tab-pane fade show active" id="personal-chat" role="tabpanel"
                    aria-labelledby="personal-chat-tab">
                    <div class="sidebar-userlist">
                        <ul class="list-unstyled userSearchList">
                            <li v-for="user in users" :key="user.id" @click="userMessage(user.id)" v-if="(user.role == 888 || user.role == 999 ) && user.status == 'active' " >
                                <div class="conversation unread">
                                    <div class="user-avatar user-avatar-rounded ">
                                        <img :src="'/storage/avatar/'+user.dp" alt="User_DP">
                                    </div>
                                    <div class="conversation__details">
                                        <div class="conversation__name">
                                            <div class="conversation__name--title">
                                                {{user.first_name + " "+ user.last_name }}</div>
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
  data() {
    return {
      users: [],
      userId: Laravel.user.id
    };
  },

  mounted() {
    this.onlineUsers();

    Event.$on("new_message", message => {
      Event.$emit("reload_inbox");
    });
  },

  methods: {
    userMessage(id) {
      Event.$emit("get_user_messages", id);
    },

    onlineUsers() {
      Event.$on("users.here", users => {
        this.users = users;
      })
        .$on("users.joined", user => {
          this.users.unshift(user);
        })
        .$on("users.left", user => {
          this.users = this.users.filter(u => {
            return u.id != user.id;
          });
        });
    }
  }
};
</script>
