<template>
    <div>
        <div class="tabs p-tabs">
            <div class="row">
                <div class="col-sm" v-for="tab in tabs" :class="{ 'is-active': tab.isActive }" v-bind:key="tab">
                    <div class="col-sm" @click="selectTab(tab)">
                        <div class="d-flex justify-content-center align-items-center rounded  tab-in mb-4">
                            <div class="text-center my-2">{{tab.name}}</div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="tabs-details">
            <slot></slot>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                tabs: []
            };
        },

        created() {

            this.tabs = this.$children;

        },
        methods: {
            selectTab(selectedTab) {
                this.tabs.forEach(tab => {
                    tab.isActive = (tab.name == selectedTab.name);
                });
            }
        }
    }

</script>
