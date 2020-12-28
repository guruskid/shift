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
                            <i class="pe-7s-timer icon-gradient bg-warm-flame">
                            </i>
                        </div>
                        <div> Transaction
                            <div class="page-title-subheading">
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-header">Transaction Images </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach ($transaction->batchPops as $pop)
                                    <div class="col-md-3">
                                        <img src="{{asset('storage/pop/'.$pop->path)}}" class="img-fluid" alt="image">
                                        <div class="d-flex justify-content-between">
                                            <a href="{{asset('storage/pop/'.$pop->path)}}">View</a>
                                            <i>By {{$pop->user->first_name}} ({{$pop->user->phone}})</i>

                                        </div>
                                    </div>
                                @endforeach
                                @foreach ($transaction->pops as $pop)
                                    <div class="col-md-3">
                                        <img src="{{asset('storage/pop/'.$pop->path)}}" class="img-fluid" alt="image">
                                        <div class="d-flex justify-content-between">
                                            <a href="{{asset('storage/pop/'.$pop->path)}}">View</a>
                                            <i>By {{$pop->user->first_name}} ({{$pop->user->phone}})</i>

                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="card-footer">
                            <form action="{{route('transaction.add-image')}} " method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="">Upload Image(s)</label>
                                    <input type="hidden" name="transaction_id" value="{{$transaction->id}}" >
                                    <input type="file" name="pops[]" accept="image/*" required multiple >
                                </div>
                                <button type="submit" class="btn btn-success">Upload</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
