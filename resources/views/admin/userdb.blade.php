@php
// $cards = App\Card::orderBy('name', 'asc')->get(['name', 'id']);
// $emails = App\User::orderBy('email', 'asc' )->pluck('email');
// $primary_wallets = App\BitcoinWallet::where(['type' => 'primary', 'user_id' => 1])->get();
@endphp
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
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-header justify-content-between">Download Users
                            <form action="{{route('admin.userdbsearch')}}" class="form-inline p-2"
                                method="POST">
                                @csrf
                                <div class="form-group mr-2">
                                    <label for="">Start date </label>
                                    <input type="date" required name="start" class="ml-2 form-control">
                                </div>
                                <div class="form-group mr-2">
                                    <label for="">End date </label>
                                    <input type="date" required name="end" class="ml-2 form-control">
                                </div>
                                <button class="btn btn-outline-primary"><i class="fa fa-filter"></i></button>
                            </form>
                        </div>
                        <div class="table-responsive p-3">
                            
                            <table class="align-middle mb-4 table table-bordered table-striped transactions-table ">
                                <thead>
                                    <tr>
                                        <th class="text-center">SN</th>
                                        <th class="text-center">First_name</th>
                                        <th class="text-center">Last_name</th>
                                        <th class="text-center">Email</th>
                                        <th class="text-center">Phone</th>
                                        <th class="text-center">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @php
                                    $sn = 1; 
                                    $users;  
                                    @endphp

                                    @foreach ($users as $user)
                                    <tr>
                                        <td>{{$sn++}}</td>
                                        <td>{{ $user->first_name }}</td>
                                        <td>{{ $user->last_name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone }}</td>
                                        <td>{{ $user->created_at }}</td>
                                    </tr> 
                                    @endforeach                                    --}}
                                </tbody>
                            </table>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
