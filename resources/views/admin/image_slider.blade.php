@extends('layouts.admin')
@section('content')
<div id="content" class="main-content">
    <div class="layout-px-spacing">
        <div class="dashboard-title d-flex">
            <ion-icon name="home-outline"></ion-icon>
            <div class="description">
                <h5>Dashboard Home</h5>
                <P>Hi DANTOWN ADMIN, good to see you again Boss.</P>
            </div>
        </div>
        <div class="row">

            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                <div class="widget widget-table-two">
                    <div class="widget-content">
                        <div class="row">
                        <div class="col-md-6 p-5 ">
                            <form enctype="multipart/form-data" action="{{route('admin.upload_image_slider')}}" method="POST">@csrf
                                <h3>Add new image slider</h3>
                                <p class="mb-5">
                                    All image should have the same aspect ratio
                                </p>
                                <div class="form-group">
                                    <label>Upload Image</label>
                                    <input type="file" name="image" class="form-control" id="">
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary">Upload</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6">
                            @php
                              $active = 0;
                              $to = 0;
                            @endphp
                            <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                                <ol class="carousel-indicators">
                                    @foreach ($slider as $lid)
                                    <li data-target="#carouselExampleIndicators" data-slide-to="{{$to++}}" class="{{$to == 1 ? 'active' : ''}}"></li>
                                    @endforeach
                                </ol>
                                <div class="carousel-inner">
                                    @foreach ($slider as $slide)
                                    @php
                                        $active++
                                    @endphp
                                    <div class="carousel-item {{$active == 1 ? 'active' : ''}}">
                                        <img class="d-block w-100" src="{{asset('storage/slider/'.$slide->image)}}" alt="Slider">
                                        <div class="carousel-caption    d-md-block">
                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#exampleModal{{$slide->id}}">
                                                Change
                                              </button>
                                            <a href="{{route('admin.delete_image_slider', [$slide->id])}}" class="btn btn-danger btn-sm">Delete</a>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button"
                                    data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#carouselExampleIndicators" role="button"
                                    data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        </div>
                        </div>
                        <!-- Tab panes -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




<!-- Button trigger modal -->
{{-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
    Launch demo modal
  </button> --}}

  <!-- Modal -->
  @foreach ($slider as $slideChange)
  <div class="modal fade" id="exampleModal{{$slideChange->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Update Image</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="image">
            <div class="w-100">
              <img class="d-block w-100" src="{{asset('storage/slider/'.$slideChange->image)}}">
            </div>
            <div class="form">
                <form action="{{route('admin.update_image_slider')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="">upload image</label>
                        <input type="hidden" name="image_id" value="{{$slideChange->id}}">
                        <input type="file" name="image" id="" class="form-control">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <span type="button" class="btn btn-primary" data-dismiss="modal">Close</span>
        {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
      </div>
    </div>
  </div>
</div>
@endforeach

@endsection
