<button  class="btn-message d-md-none d-lg-none btn  shadow-lg px-2 py-1 btn-info" title="Message">
        <img src=" {{asset('live.png')}}" height="50px" alt="">
</button>


<div class="col-md-2 col-11 d-none d-md-block btn-message" >
    <div class="card card-body bg-info p-2">
        <div class="media">
            <img src=" {{asset('live.png')}}" class="mr-2 my-auto" height="30px" alt="">
            <div class="media-body">
                <strong>Live Chat</strong>
            </div>
        </div>
    </div>
</div>

<div class="col-md-3 col-11   animated slideInUp" id="box-message">
    <div class="row justify-content-center">
        <messages-component></messages-component>
    </div>
</div>
