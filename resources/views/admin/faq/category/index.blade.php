{{-- @php
$emails = App\User::orderBy('email', 'asc' )->pluck('email');
@endphp --}}
@extends('layouts.app')
@section('content')
<div class="app-main">
    <div class="app-sidebar sidebar-shadow">
        <div class="app-header__logo">
            <div class="logo-src"></div>
            <div class="header__pane ml-auto">
                <div>
                    <button type="button" class="hamburger close-sidebar-btn hamburger--elastic"
                        data-class="closed-sidebar">
                        <span class="hamburger-box">
                            <span class="hamburger-inner"></span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
        <div class="app-header__mobile-menu">
            <div>
                <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>
            </div>
        </div>
        <div class="app-header__menu">
            <span>
                <button type="button" class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                    <span class="btn-icon-wrapper">
                        <i class="fa fa-ellipsis-v fa-w-6"></i>
                    </span>
                </button>
            </span>
        </div>
        {{-- User Side bar --}}
        @include('layouts.partials.admin')
    </div>

    {{-- Content Starts here --}}
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="app-page-title">
                <div class="page-title-wrapper">
                    <div class="page-title-heading">
                        <div class="page-title-icon">
                            <i class="pe-7s-users icon-gradient bg-sunny-morning">
                            </i>
                        </div>
                        <div>Faq Categories</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mx-auto mb-3">
                    <div class="card">
                        <div class="card-header">Faq Cateegories</div>
                        <div class="card-body">
							<form action=" {{route('faq.category.create')}} " method="post">
								@csrf
                                <div class="form-group">
                                    <label for="">Category Name</label>
                                    <input type="text" class="form-control" name="name"2>
                                    {{-- <select name="email" class="form-control">
                                        <option value=""></option>
                                        @foreach ($emails as $e)
                                        <option value="{{$e}}"> {{ ucfirst($e) }} </option>
                                        @endforeach
                                    </select> --}}
								</div>
								<button class="btn btn-success">Add</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 pb-3 card">
                        <div class="table-responsive">
                            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">First name</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($faq_category as $category_data)
                                    <tr>
                                        <td class="text-center text-muted">{{$category_data->id}}</td>
                                        <td class="text-center">{{ucwords($category_data->name)}}</td>
                                        <td class="text-center">
                                            <a href="#" class="my-2 mr-2" data-toggle="modal" data-target="#editCategory" onclick="EditFaqCategory({{$category_data}})">
                                                <span class="btn btn btn-warning">Edit Category</span>
                                            </a>
                                            <a href="#" class="my-2 mr-2" data-toggle="modal" data-target="#deleteCategory" onclick="DeleteFaqCategory({{$category_data}})">
                                                <span class="btn btn btn-danger">Delete Category</span>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="m-2"><span> {{$faq_category->links()}} </span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade  item-badge-rightm" id="editCategory" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{route('faq.category.update')}} " method="POST" class="mb-3">
                {{ csrf_field() }}
                <div class="modal-header">
                    <div class="media">
                        <div class="media-body">
                            <h4 class="media-heading">Edit Categories</h4>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <input type="hidden" readonly name="id" id="cat_id">
                    </div>
                    <div class="row">
                        <div class="col-12" id="feedback-textarea">
                            <div class="form-group">
                            <label for="feedback">Category Name</label>
                            <input type="text"  class="form-control" id="cat_value" name="name" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button> --}}
                    <button type="submit" class="btn btn-warning">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>



<div class="modal fade  item-badge-rightm" id="deleteCategory" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{route('faq.category.delete')}} " method="POST" class="mb-3">
                {{ csrf_field() }}
                <div class="modal-header">
                    <div class="media">
                        <div class="media-body">
                            <h4 class="media-heading" id="cat_text">.................</h4>
                            <div class="form-group">
                                <input type="hidden" readonly name="id" id="cat_del_id">
                            </div>
                        </div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-footer">
                    {{-- <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button> --}}
                    <button type="submit" class="btn btn-danger">YES</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">NO</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
