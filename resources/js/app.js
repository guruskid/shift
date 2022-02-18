/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

//const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));
Vue.component('test-component', require('./components/TestComponent.vue').default);

Vue.component('chat-component', require('./components/ChatComponent.vue').default);
Vue.component('online-users-component', require('./components/OnlineUsersComponent.vue').default);
Vue.component('chat-messages-component', require('./components/ChatMessagesComponent.vue').default);
Vue.component('chat-form-component', require('./components/ChatFormComponent.vue').default);
Vue.component('message-component', require('./components/MessageComponent.vue').default);
Vue.component('users-component', require('./components/UsersComponent.vue').default);
Vue.component('chat-header-component', require('./components/ChatHeaderComponent.vue').default);
Vue.component('user-details-component', require('./components/UserDetailsComponent.vue').default);
Vue.component('user-transactions-component', require('./components/UserTransactionsComponent.vue').default);
Vue.component('agents-component', require('./components/AgentsComponent.vue').default);
Vue.component('online-agents-component', require('./components/OnlineAgentsComponent.vue').default);

Vue.component('notifications-component', require('./components/NotificationsComponent.vue').default);
Vue.component('assigned-transactions', require('./components/AssignedTransactionsComponent.vue').default);
Vue.component('transactions-component', require('./components/TransactionsComponent.vue').default);


/* Not useful */
Vue.component('messages-component', require('./components/chat/MessagesComponent.vue').default);
Vue.component('main-message-component', require('./components/chat/MessageComponent.vue').default);
Vue.component('form-component', require('./components/chat/FormComponent.vue').default);

Vue.component('gift-card-component', require('./components/calculator/giftCardCalculatorComponent.vue').default);
Vue.component('upload-modal-component', require('./components/calculator/uploadModalComponent.vue').default);

Vue.component('bitcoin-sell-component', require('./components/calculator/bitcoinSellComponent.vue').default);
Vue.component('bitcoin-buy-component', require('./components/calculator/bitcoinBuyComponent.vue').default);
Vue.component('bitcoin-send-component', require('./components/calculator/bitcoinSendComponent.vue').default);

Vue.component('ethereum-create-component', require('./components/ethereum/CreateWallet.vue').default);
Vue.component('ethereum-send-component', require('./components/ethereum/Send.vue').default);
Vue.component('ethereum-sell-component', require('./components/ethereum/Sell.vue').default);


Vue.component('tron-create-component', require('./components/tron/CreateWallet.vue').default);
Vue.component('tron-send-component', require('./components/tron/Send.vue').default);
Vue.component('tron-sell-component', require('./components/tron/Sell.vue').default);

Vue.component('tab', require('./components/p2p/Tab.vue').default);
Vue.component('tabs', require('./components/p2p/Tabs.vue').default);

Vue.component('deposit-component', require('./components/p2p/Deposit.vue').default);
Vue.component('withdraw-component', require('./components/p2p/Withdraw.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
