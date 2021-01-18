{{-- <!-- Button trigger modal -->
<button type="button" class="btn btn-primary button-modal" id="uploadcardimage" data-toggle="modal"
    data-target="#staticBackdrop"></button>


<div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-custom">
        <div class="modal-content modal-content-custom">
            <div class="container py-4">
                <div class="d-flex justify-content-between mb-4">
                    <span class="d-block" style="color: #000000;letter-spacing: 0.01em;font-size: 18px;">Upload
                        cards</span>
                    <span class="d-block" data-dismiss="modal" style="cursor: pointer;" onclick="inputfile()">
                        <svg width="18" height="18" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g opacity="0.4">
                                <path
                                    d="M34 5.63477L28.3653 0L17 11.3652L5.63477 0L0 5.63477L11.3652 17L0 28.3652L5.63477 34L17 22.6348L28.3653 34L34 28.3652L22.6348 17L34 5.63477ZM31.1827 28.3652L28.3653 31.1826L17 19.8174L5.63477 31.1826L2.81742 28.3653L14.1826 17L2.81742 5.63477L5.63477 2.81742L17 14.1826L28.3653 2.81742L31.1827 5.63477L19.8174 17L31.1827 28.3652Z"
                                    fill="#000070" fill-opacity="0.75" />
                            </g>
                        </svg>
                    </span>
                </div>
                <div class="p-2 mx-auto dashed-border">
                    <span class="d-block text-center">
                        <svg width="120" height="120" viewBox="0 0 48 48" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0)">
                                <path
                                    d="M69.3421 37.04V77.8851C69.3421 81.2625 66.6043 84.0002 63.227 84.0002H12.9901C9.61271 84.0002 6.875 81.2624 6.875 77.8851V13.9003C6.875 10.522 9.61353 7.78516 12.9901 7.78516H40.0865L69.3421 37.04Z"
                                    fill="#BED8FB" />
                                <path
                                    d="M20.7753 76.2152C17.3979 76.2152 14.6602 73.4774 14.6602 70.1001V6.1151C14.66 2.73689 17.3985 0 20.7753 0H47.8715C52.53 0 77.1271 24.5969 77.1271 29.255V70.1001C77.1271 73.4775 74.3892 76.2152 71.012 76.2152H20.7753Z"
                                    fill="#DDEAFB" />
                                <path
                                    d="M77.1274 29.2555V31.8422C77.1274 26.3962 72.7112 21.9799 67.2651 21.9799H61.2625C57.886 21.9799 55.1474 19.2414 55.1474 15.8648V9.86229C55.1474 4.41623 50.7312 0 45.2852 0H47.8719C52.5297 0 56.9982 1.85062 60.2916 5.14418L71.9834 16.8359C75.2766 20.1293 77.1274 24.5977 77.1274 29.2555Z"
                                    fill="#BED8FB" />
                                <path
                                    d="M39.8043 46.6865V64.5652C39.8043 65.3365 40.4296 65.9617 41.2008 65.9617H50.5842C51.3555 65.9617 51.9807 65.3365 51.9807 64.5652V46.6865H55.9699C57.214 46.6865 57.8371 45.1824 56.9574 44.3027L48.0437 35.389C46.8557 34.2011 44.9295 34.2011 43.7415 35.389L34.8278 44.3027C33.9481 45.1824 34.5712 46.6865 35.8153 46.6865H39.8043Z"
                                    fill="#80B4FB" />
                                <path
                                    d="M35.0104 30.4823H56.7742C58.2772 30.4823 59.4955 29.2639 59.4955 27.761V27.6861C59.4955 26.1832 58.2772 24.9648 56.7742 24.9648H35.0104C33.5074 24.9648 32.2891 26.1832 32.2891 27.6861V27.761C32.2891 29.2638 33.5074 30.4823 35.0104 30.4823Z"
                                    fill="#80B4FB" />
                            </g>
                            <defs>
                                <clipPath id="clip0">
                                    <rect width="84" height="84" fill="white" />
                                </clipPath>
                            </defs>
                        </svg>
                    </span>
                    <div id="upload_text_desc" class="mb-2">
                        <span class="d-block primary-color text-center">Place your Images/card receipts here</span>
                        <span class="d-block text-center"
                            style="color: rgba(0, 0, 112, 0.7);letter-spacing: 0.01em;">Click to select
                            image/images</span>
                    </div>

                    <form action="" id="uploadcardsform">
                        <input type='file' class="form-control" name="card_images" onchange="preview(this);"
                            multiple="multiple" style="border:0px;outline:none !important;" />
                        <div id="previewImg"
                            class="my-3 previewImg d-flex d-lg-block justify-content-center flex-wrap align-items-around">
                        </div>
                    </form>

                </div>
                <button class="btn text-white mt-4 mt-lg-5 card-upload-btn">Upload</button>
            </div>
        </div>
    </div>
</div> --}}


<div class="modal" id="uploadCardImageModal" tabindex="-1" style="background-color: #a9a9a994;">
    <div class="modal-dialog">
        <div class="modal-content modal-content-custom" style="margin-top: 100px;">

            <div id="modal_container_content" class="container py-4">
                <div class="d-flex justify-content-between mb-4">
                    <span class="d-block" style="color: #000000;letter-spacing: 0.01em;font-size: 18px;">Upload cardssss</span>
                    <span class="d-block" data-dismiss="modal" style="cursor: pointer;" onclick="inputfile()">
                        <svg width="18" height="18" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g opacity="0.4">
                                <path
                                    d="M34 5.63477L28.3653 0L17 11.3652L5.63477 0L0 5.63477L11.3652 17L0 28.3652L5.63477 34L17 22.6348L28.3653 34L34 28.3652L22.6348 17L34 5.63477ZM31.1827 28.3652L28.3653 31.1826L17 19.8174L5.63477 31.1826L2.81742 28.3653L14.1826 17L2.81742 5.63477L5.63477 2.81742L17 14.1826L28.3653 2.81742L31.1827 5.63477L19.8174 17L31.1827 28.3652Z"
                                    fill="#000070" fill-opacity="0.75" />
                            </g>
                        </svg>
                    </span>
                </div>
                <form action="" id="uploadcardsform" method="POST" enctype="multipart/form-data"> @csrf
                    <div class="p-2 mx-auto dashed-border">
                        <span class="d-block text-center">
                            <img src="{{asset('/customizeduploadbtn.png')}}" alt="">
                        </span>

                        <div id="upload_text_desc" class="mb-2">
                            <span class="d-block primary-color text-center">Place your Images/card receipts here</span>
                            <span class="d-block text-center"
                                style="color: rgba(0, 0, 112, 0.7);letter-spacing: 0.01em;">Click to select
                                image/images</span>
                        </div>


                        <input type='file' class="form-control" name="card_images[]" onchange="preview(this);"
                            multiple="multiple" style="border:0px;outline:none !important;" accept="image/*" />
                        <div id="previewImg"
                            class="my-3 previewImg d-flex d-lg-block justify-content-center flex-wrap align-items-around">
                        </div>


                    </div>
                    <button id="upload_card_btn" type="submit"
                        class="btn text-white mt-4 mt-lg-5 card-upload-btn">Upload</button>
                </form>
            </div>

        </div>
    </div>
</div>
