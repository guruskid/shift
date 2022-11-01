<template>
    <div class="modal" id="uploadCardImageModal" tabindex="-1" style="background-color: #a9a9a994">
        <div class="modal-dialog">
            <div class="modal-content modal-content-custom" style="margin-top: 100px">
                <div id="modal_container_content" class="container py-4">
                    <div class="d-flex justify-content-between mb-4">
                        <span class="d-block"
                            style="color: #000000; letter-spacing: 0.01em; font-size: 18px">{{ buy_sell == 2 ? 'Upload cards' : '' }}
                        </span>
                        <span class="d-block" data-dismiss="modal" style="cursor: pointer" onclick="inputfile()">
                            <svg width="18" height="18" viewBox="0 0 34 34" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <g opacity="0.4">
                                    <path
                                        d="M34 5.63477L28.3653 0L17 11.3652L5.63477 0L0 5.63477L11.3652 17L0 28.3652L5.63477 34L17 22.6348L28.3653 34L34 28.3652L22.6348 17L34 5.63477ZM31.1827 28.3652L28.3653 31.1826L17 19.8174L5.63477 31.1826L2.81742 28.3653L14.1826 17L2.81742 5.63477L5.63477 2.81742L17 14.1826L28.3653 2.81742L31.1827 5.63477L19.8174 17L31.1827 28.3652Z"
                                        fill="#000070" fill-opacity="0.75" />
                                </g>
                            </svg>
                        </span>
                    </div>
                    <form action="/user/trade" method="POST" id="uploadcardsform" enctype="multipart/form-data">
                        <div class="p-2 mx-auto dashed-border">
                            <div v-if="buy_sell == 2" id="upload_text_desc" class="mb-2">
                                <span class="d-block primary-color text-center">Place your Images card receipts
                                    here</span>

                            </div>
                            <div v-if="buy_sell == 2" class="row">
                                <label for="file" class="mx-auto">
                                    <span class="badge badge-primary p-2">Choose images</span>
                                </label>
                                <input type="file" class="d-none" id="file" name="card_images[]" multiple onchange="preview(this);" accept="image/*">
                            </div>
                            <input type="hidden" name="_token" :value="csrf">
                            <div v-for="trade in trades" :key="trade.key">
                                <input type="hidden" v-model="trade.cardName" name="cards[]">
                                <input type="hidden" v-model="trade.currency" name="currencies[]">
                                <input type="hidden" v-model="trade.cardType" name="card_types[]">
                                <input type="hidden" v-model="trade.cardPrice" name="prices[]">
                                <input type="hidden" v-model="trade.cardValue" name="values[]">
                                <input type="hidden" v-model="trade.cardQuantity" name="quantities[]">
                                <input type="hidden" v-model="trade.cardTotal" name="totals[]">
                            </div>
                            <input type="hidden" name="buy_sell" v-model="buy_sell">
                            <!-- <input v-if="buy_sell == 2" type="file" class="form-control " name="card_images[]"
                                onchange="preview(this);" multiple="multiple"
                                style="border: 0px; outline: none !important" accept="image/*" > -->
                            <div v-if="buy_sell == 2" id="previewImg"
                                class="my-3 previewImg d-flex d-lg-block justify-content-center flex-wrap align-items-around">
                            </div>
                        </div>
                        <button id="upload_card_btn" type="submit" class="btn text-white mt-4 mt-lg-5 card-upload-btn">
                            Trade
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Event from "../../event.js";
    export default {
        data() {
            return {
                trades: [],
                buy_sell: [],
                csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        },

        mounted() {
            Event.$on("process_trades", data => {
                this.trades = [];
                console.log(data);
                this.trades = data[0];
                this.buy_sell = data[1];
            });
        },
    }

</script>
