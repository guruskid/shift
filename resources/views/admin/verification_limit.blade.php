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
            <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing d-none">
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

            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                <div class="widget widget-table-two">
                    <div class="widget-heading">
                        <h5 class=""> Verification Limit </h5>
                    </div>

                    <div class="widget-content">

                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#home" class="btn btn-sm btn-primary" data-toggle="tab">Level 1</a></li>
                            <li><a class="btn btn-sm btn-primary" href="#profile" data-toggle="tab">Level 2</a></li>
                            <li><a class="btn btn-sm btn-primary" href="#messages" data-toggle="tab">Level 3</a></li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div class="tab-pane active" id="home">

                                <div class="panel-group" id="accordion">
                                    <div class="panel panel-default">
                                        <div class="widget widget-table-two mb-4 mt-4">
                                            <div class="widget-heading">
                                                <h5 class="">level 1</h5>
                                            </div>

                                            <div class="widget-content">
                                                <form action="{{ route('admin.add_verification_limit') }}" enctype="multipart/form-data" method="POST"> @csrf
                                                    <div class="form-group">
                                                        @foreach ($errors->all() as $error)
                                                            <p class="alert alert-danger">{{$error}}</p>
                                                        @endforeach
                                                    </div>
                                                    <input type="hidden" name="level" value="1" class="form-control">
                                                    <div class="row">
                                                        <div class="form-group col-md-6">
                                                            <label for="">Daily widthdrawal limit</label>
                                                            <input type="number" name="daily_widthdrawal_limit" class="form-control" value="{{$levelOne[0]->daily_widthdrawal_limit ?? 0}}">
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label for="">Monthly widthdrawal limit</label>
                                                            <input type="number" name="monthly_widthdrawal_limit" class="form-control" value="{{$levelOne[0]->monthly_widthdrawal_limit ?? 0}}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="">Crypto widthdrawal limit</label>
                                                        <input type="number" name="crypto_widthdrawal_limit" class="form-control" value="{{$levelOne[0]->crypto_widthdrawal_limit ?? 0}}">
                                                    </div>
                                                    <div class="row">

                                                        <div class="form-group col-md-6">
                                                            <label for="">Crypto deposit </label>
                                                            <input type="text" name="crypto_deposit" class="form-control" value="{{$levelOne[0]->crypto_deposit ?? "Unlimited"}}">
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label for="">transactions</label>
                                                            <input type="text" name="transactions" class="form-control" value="{{$levelOne[0]->transactions ?? "Unlimited"}}">
                                                          </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <button class="btn btn-primary">Submit</button>
                                                    </div>
                                                </form>

                                            </div>
                                        </div>
                                    </div>
                                  </div>
                            </div>

                            <div class="tab-pane" id="profile">
                                <div class="panel-group" id="accordion">
                                    <div class="panel panel-default">
                                        <div class="widget widget-table-two mb-4 mt-4">
                                            <div class="widget-heading">
                                                <h5 class="">level 2</h5>
                                            </div>

                                            <div class="widget-content">
                                                <form action="{{ route('admin.add_verification_limit') }}" enctype="multipart/form-data" method="POST"> @csrf
                                                    <div class="form-group">
                                                        @foreach ($errors->all() as $error)
                                                            <p class="alert alert-danger">{{$error}}</p>
                                                        @endforeach
                                                    </div>
                                                    <input type="hidden" name="level" value="2" class="form-control">
                                                    <div class="row">
                                                        <div class="form-group col-md-6">
                                                            <label for="">Daily widthdrawal limit</label>
                                                            <input type="number" name="daily_widthdrawal_limit" class="form-control" value="{{$levelTwo[0]->daily_widthdrawal_limit ?? 0}}">
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label for="">Monthly widthdrawal limit</label>
                                                            <input type="number" name="monthly_widthdrawal_limit" class="form-control" value="{{$levelTwo[0]->monthly_widthdrawal_limit ?? 0}}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="">Crypto widthdrawal limit</label>
                                                        <input type="number" name="crypto_widthdrawal_limit" class="form-control" value="{{$levelTwo[0]->crypto_widthdrawal_limit ?? 0}}">
                                                    </div>
                                                    <div class="row">

                                                        <div class="form-group col-md-6">
                                                            <label for="">Crypto deposit </label>
                                                            <input type="text" name="crypto_deposit" class="form-control" value="{{$levelTwo[0]->crypto_deposit ?? "Unlimited"}}">
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label for="">transactions</label>
                                                            <input type="text" name="transactions" class="form-control" value="{{$levelTwo[0]->transactions ?? "Unlimited"}}">
                                                          </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <button class="btn btn-primary">Submit</button>
                                                    </div>
                                                </form>

                                            </div>
                                        </div>
                                    </div>
                                  </div>
                            </div>
                            <div class="tab-pane" id="messages">
                                <div class="panel-group" id="accordion">
                                    <div class="panel panel-default">
                                        <div class="widget widget-table-two mb-4 mt-4">
                                            <div class="widget-heading">
                                                <h5 class="">level 3</h5>
                                            </div>

                                            <div class="widget-content">
                                                <form action="{{ route('admin.add_verification_limit') }}" enctype="multipart/form-data" method="POST"> @csrf
                                                    <div class="form-group">
                                                        @foreach ($errors->all() as $error)
                                                            <p class="alert alert-danger">{{$error}}</p>
                                                        @endforeach
                                                    </div>
                                                    <input type="hidden" name="level" value="3" class="form-control">
                                                    <div class="row">
                                                        <div class="form-group col-md-6">
                                                            <label for="">Daily widthdrawal limit</label>
                                                            <input type="number" name="daily_widthdrawal_limit" class="form-control" value="{{$levelThree[0]->daily_widthdrawal_limit ?? 0}}">
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label for="">Monthly widthdrawal limit</label>
                                                            <input type="number" name="monthly_widthdrawal_limit" class="form-control" value="{{$levelThree[0]->monthly_widthdrawal_limit ?? 0}}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="">Crypto widthdrawal limit</label>
                                                        <input type="number" name="crypto_widthdrawal_limit" class="form-control" value="{{$levelThree[0]->crypto_widthdrawal_limit ?? 0}}">
                                                    </div>
                                                    <div class="row">

                                                        <div class="form-group col-md-6">
                                                            <label for="">Crypto deposit </label>
                                                            <input type="text" name="crypto_deposit" class="form-control" value="{{$levelThree[0]->transactions ?? "Unlimited"}}">
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label for="">transactions</label>
                                                            <input type="text" name="transactions" class="form-control" value="{{$levelThree[0]->transactions ?? "Unlimited"}}">
                                                          </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <button class="btn btn-primary">Submit</button>
                                                    </div>
                                                </form>

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

    </div>

</div>
@endsection
