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
                        <h5 class="">EDIT FAQ</h5>
                    </div>

                    <div class="widget-content">
                        <form action="{{ route('admin.updatefaq') }}" enctype="multipart/form-data" method="POST"> @csrf

                            <div class="form-group">
                                <label for="">Title</label>
                                <input type="hidden" name="id" value="{{$editfaq->id}}" class="form-control">
                                <input type="text" name="title" value="{{$editfaq->title}}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="">Body</label>
                                <textarea name="body" class="form-control" id="" rows="7">{{$editfaq->body}}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="">Image Previous Uploaded</label>
                                <img src="/storage/faq/{{ $editfaq->image }}" class="img-fluid">
                              </div>
                            <div class="form-group">
                                <label for="">Image(if you dont want to change image you can leave empty)</label>
                                <input type="file" name="photo" class="form-control"/>
                              </div>
                              <div class="form-group">
                                <label for="">Link</label>
                                <input type="text" name="link" class="form-control" value="{{$editfaq->link}}">
                              </div>
                              <div class="form-group">
                                <label for="">Icon Name</label>
                                <input type="text" name="icon" class="form-control" value="{{$editfaq->icon}}">
                              </div>
                            <div class="form-group">
                                <label for="">Category</label>
                                <select name="category" id="" class="form-control">
                                    <option value="{{$editfaq->category_id}}">{{$editfaq->category->name}}</option>
                                    @foreach ($faq_categories as $category_data)
                                    <option value={{ $category_data->id }}>{{ $category_data->name }}</option>
                                  @endforeach
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
