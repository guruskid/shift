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
            <div class="col-12 layout-spacing">
                <div class="widget widget-table-two mb-4">
                    <div class="widget-heading">
                        <h5 class="">ADD FAQ</h5>
                    </div>

                    <div class="widget-content">
                        <form action="{{ route('admin.updatefaq') }}" method="POST"> @csrf

                            <div class="form-group">
                                <label for="">Title</label>
                                <input type="hidden" name="id" value="{{$editfaq[0]->id}}" class="form-control">
                                <input type="text" name="title" value="{{$editfaq[0]->title}}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="">Body</label>
                                <textarea name="body" class="form-control" id="" rows="7">{{$editfaq[0]->body}}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="">Category</label>
                                <select name="category" id="" class="form-control">
                                    <option value="{{$editfaq[0]->category}}">{{$editfaq[0]->category}}</option>
                                    <option value="finance">Finance</option>
                                    <option value="tech">Tech</option>
                                    <option value="transactions">Transactions</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary">Update</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
