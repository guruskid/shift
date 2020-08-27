@extends('layouts.app')
@section('content')
@section('title', 'User Profile -' )
@include('layouts.partials.user')
<div class="col-md-7 col-lg-8 col-xl-8">
    <div class="page-header bordered ml-3 ">
        <h1>My profile </h1>
        @if (Auth::user()->status == 'not verified' )
        <a href="#" data-toggle="modal" data-target="#idcard"><button class="btn btn-sm btn-primary">Verify
                account</button></a>
        @else
        <span class="badge text-white p-2 mt-1">{{ucfirst(Auth::User()->status)}}</span>
        @endif
        @if (Auth::user()->status == 'declined' )
        <span class="badge text-white p-2 mt-1">{{ucfirst(Auth::User()->status)}}</span>
        <a href="#" data-toggle="modal" data-target="#idcard"><button class="btn btn-sm btn-primary">Verify
                account</button></a>


        @endif
    </div>
    @if (count($errors) > 0)
    <div class="alert alert-danger">
        <strong>Whoops!</strong> There were some problems with your input.<br><br>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <form action="{{route('user.change_password')}}" class="ml-3" method="POST">
        {{ csrf_field() }}
        <div class="form-group">
            <label>Old Password</label>
            <input type="password" class="form-control form-control" name="old_password" required>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" class="form-control form-control" name="new_password" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input name="new_password_confirmation" type="password" class="form-control"  required>
                </div>
            </div>
        </div>
        <div class="form-group"><input type="submit" class="btn btn-primary" value="Update"></div>
        <hr>
    </form>
</div>
</div>
</div>
</div>

@endsection
