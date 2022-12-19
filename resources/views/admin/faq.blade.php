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
            <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                <div class="widget widget-table-two mb-4">
                    <div class="widget-heading">
                        <h5 class="">ADD FAQ</h5>
                    </div>

                    <div class="widget-content">
                        <form action="{{ route('admin.newfaq') }}" enctype="multipart/form-data" method="POST"> @csrf

                            <div class="form-group">
                                <label for="">Title</label>
                                <input type="text" name="title" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="">Body</label>
                                <textarea name="body" class="form-control" id="" rows="7"></textarea>
                            </div>
                            <div class="form-group">
                              <label for="">Image</label>
                              <input type="file" name="photo"  class="form-control"/>
                            </div>
                            <div class="form-group">
                              <label for="">Link</label>
                              <input type="text" name="link" class="form-control">
                            </div>
                            <div class="form-group">
                              <label for="">Icon Name</label>
                              <input type="text" name="icon" class="form-control">
                            </div>
                            
                            <div class="form-group">
                                <label for="">Category</label>
                                <select name="category" id="" class="form-control">
                                  @foreach ($faq_categories as $category_data)
                                    <option value={{ $category_data->id }}>{{ $category_data->name }}</option>
                                  @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary">Submit</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                <div class="widget widget-table-two">
                    <div class="widget-heading">
                        <h5 class=""> FAQs </h5>
                    </div>

                    <div class="widget-content">

                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs">
                              <li><a class="btn btn-sm btn-primary" href="#all" data-toggle="tab">ALL</a></li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div class="tab-pane active" id="all">
                              @foreach ($faq as $faqData)
                                <div class="panel-group" id="accordion">
                                    <div class="panel panel-default">
                                      <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <a href="{{route('admin.edit-faq', [$faqData->id])}}" class="badge badge-primary"> <ion-icon name="create-outline"></ion-icon></a>
                                            <a href="{{route('admin.deletefaq', [$faqData->id])}}" class="badge badge-danger"> <ion-icon name="trash"></ion-icon></a>
                                            <a data-toggle="collapse" data-parent="#accordion" data-toggle="collapse"href="#collapseOne{{$faqData->category->id}}">
                                              {{-- <img src="/storage/faq/{{ $faqData->image }}" class="img-fluid"> --}}
                                            {{ $faqData->title }}
                                            <h6>Category:<b>{{$faqData->category->name}}</b></h6>
                                          </a>
                                        </h3>
                                      </div>
                                      <div id="collapseOn{{$faqData->category->id}}" class="panel-collapse collapse">
                                        <div class="panel-body">
                                          {{ $faqData->title }}
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                              @endforeach
                            </div>
                          
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
