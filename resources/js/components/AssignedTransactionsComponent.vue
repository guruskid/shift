<template>
  <table class="align-middle mb-4 table table-bordered table-striped">
    <thead>
      <tr>
        <th class="text-center">ID</th>
        <th class="text-center">Asset type</th>
        <th class="text-center">Tran. type</th>
        <th class="text-center">Asset value</th>
        <th class="text-center">Cash value</th>
        <th class="text-center">User</th>
        <th class="text-center">Date</th>
        <th class="text-center">Status</th>
        <th class="text-center">Action</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="t in transactions" :key="t.id">
        <td class="text-center text-muted">{{t.uid}}</td>
        <td class="text-center">{{t.card}}</td>
        <td class="text-center">{{t.type}}</td>
        <td class="text-center">{{t.amount}}</td>
        <td class="text-center">N {{t.amount_paid}}</td>
        <td class="text-center">
          <a href="#">{{t.user.first_name+" "+t.user.last_name}}</a>
        </td>
        <td class="text-center">{{t.created_at}}</td>
        <td class="text-center">
          <p class="text-info">{{t.status}}</p>
        </td>
        <td>
          <a :href="'/admin/view-transaction/' + t.id +'/' + t.uid ">
            <button class="btn btn-success">View / Upload</button>
          </a>
          <button class="btn btn-success btn-sm" @click="update(t.id, t, 'in progress')">Accept</button>
          <button class="btn btn-danger btn-sm" @click="update(t.id, t, 'declined')">Decline</button>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
import Event from "../event.js";
export default {
  props: ["trans"],
  data() {
    return {
      userId: Laravel.user.id,
      transactions: this.trans
    };
  },

  mounted() {
    Echo.private(`transaction.${this.userId}`).listen("NewTransaction", e => {
      this.transactions.unshift(e.transaction);
      alert('New transaction initiated');
    });
  },

  methods: {
    update(id, t, status) {
      axios
        .get("/admin/update-transaction/" + id + "/" + status)
        .then(response => {
          if (response.data["success"]) {
            this.transactions.splice(this.transactions.indexOf(t), 1);
            alert('Trade accepted');
          } else {
            alert("An error occured");
          }
        });
    }
  }
};
</script>



<style>
</style>

