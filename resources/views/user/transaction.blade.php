@extends('layouts.user')
@section('title', 'Transaction |' )
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
        @include('layouts.partials.user')
    </div>

    {{-- Content Starts here --}}
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="main-card card">
                        <div class="card-body">
                            <div class="row">
                                @foreach ($transaction->pops as $pop)
                                    <div class="col-md-3 col-sm-6 col-6  ">
                                        <img src="{{asset('storage/pop/'.$pop->path)}}" class="img-fluid" alt="image">
                                        <div class="d-flex justify-content-between">
                                            <a href="{{asset('storage/pop/'.$pop->path)}}">View</a><br>
                                            {{-- <i>By {{$pop->user->first_name}}</i> --}}
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

            @include('layouts.partials.live-feeds')
        </div>
    </div>
</div>

@if(session()->has('success'))
    <script>
       if (!confirm('You want to add more images?')) {
        window.location.href = '/user/transactions'
       }

    </script>
    @endif
@endsection



