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
                                    {{-- <option value="" disabled>Choose Category</option> --}}
                                    <option value="finance">Finance</option>
                                    <option value="tech">Tech</option>
                                    <option value="transactions">Transactions</option>
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
                            <li class="active"><a href="#home" class="btn btn-sm btn-primary" data-toggle="tab">Finance</a></li>
                            <li><a class="btn btn-sm btn-primary" href="#profile" data-toggle="tab">Tech</a></li>
                            <li><a class="btn btn-sm btn-primary" href="#messages" data-toggle="tab">Transactions</a></li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div class="tab-pane active" id="home">

                                <div class="panel-group" id="accordion">
                                    <div class="panel panel-default">
                                      @foreach ($finances as $finance)
                                      <div class="panel-heading">
                                        <h5 class="panel-title">
                                            <a href="{{route('admin.edit-faq', [$finance->id, $finance->title])}}" class="badge badge-primary"> <ion-icon name="create-outline"></ion-icon></a>
                                            <a href="{{route('admin.deletefaq', [$finance->id, $finance->title])}}" class="badge badge-danger"> <ion-icon name="trash"></ion-icon></a>
                                            <a data-toggle="collapse" data-parent="#accordion" data-toggle="collapse"href="#collapseOne{{$finance->id}}">
                                            {{ $finance->title }}
                                          </a>
                                        </h5>
                                      </div>
                                      <div id="collapseOne{{$finance->id}}" class="panel-collapse collapse">
                                        <div class="panel-body">
                                          {{ $finance->body }}
                                        </div>
                                      </div>
                                      @endforeach
                                    </div>
                                  </div>
                            </div>

                            <div class="tab-pane" id="profile">
                                <div class="panel-group" id="accordion">
                                    <div class="panel panel-default">
                                      @foreach ($techs as $tech)
                                      <div class="panel-heading">
                                        <h5 class="panel-title">
                                            <a href="{{route('admin.edit-faq', [$tech->id, $tech->title])}}" class="badge badge-primary"> <ion-icon name="create-outline"></ion-icon></a>
                                            <a href="{{route('admin.deletefaq', [$tech->id, $tech->title])}}" class="badge badge-danger"> <ion-icon name="trash"></ion-icon></a>
                                            <a data-toggle="collapse" data-parent="#accordion" data-toggle="collapse"href="#collapseOne{{$tech->id}}">
                                            {{ $tech->title }}
                                          </a>
                                        </h5>
                                      </div>
                                      <div id="collapseOne{{$tech->id}}" class="panel-collapse collapse">
                                        <div class="panel-body">
                                          {{ $tech->body }}
                                        </div>
                                      </div>
                                      @endforeach
                                    </div>
                                  </div>
                            </div>
                            <div class="tab-pane" id="messages">
                                <div class="panel-group" id="accordion">
                                    <div class="panel panel-default">
                                      @foreach ($transactions as $transaction)
                                      <div class="panel-heading">
                                        <h5 class="panel-title">
                                            <a href="{{route('admin.edit-faq', [$transaction->id, $transaction->title])}}" class="badge badge-primary"> <ion-icon name="create-outline"></ion-icon></a>
                                            <a href="{{route('admin.deletefaq', [$transaction->id, $transaction->title])}}" class="badge badge-danger"> <ion-icon name="trash"></ion-icon></a>
                                            <a data-toggle="collapse" data-parent="#accordion" data-toggle="collapse"href="#collapseOne{{$transaction->id}}">
                                            {{ $transaction->title }}
                                          </a>
                                        </h5>
                                      </div>
                                      <div id="collapseOne{{$transaction->id}}" class="panel-collapse collapse">
                                        <div class="panel-body">
                                          {{ $transaction->body }}
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

    </div>

</div>
@endsection
