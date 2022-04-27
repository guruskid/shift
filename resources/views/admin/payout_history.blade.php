@php
$cards = App\Card::orderBy('name', 'asc')->get(['name', 'id']);
@endphp
@extends('layouts.app')
@section('content')
<div class="app-main">
    <div class="app-sidebar sidebar-shadow">
        <div class="app-header__logo">
            <div class="logo-src"></div>
            <div class="header__pane ml-auto">
                <div>
                    <button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar">
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
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="app-page-title">
                <div class="page-title-wrapper">
                    <div class="page-title-heading">
                        <div class="page-title-icon">
                            <i class="pe-7s-home icon-gradient bg-night-sky">
                            </i>
                        </div>
                        <div>
                            Payout  History
                            @if ($segment)
                                {{ $segment }}
                            @endif
                            <div class="page-title-subheading">Hi {{Auth::user()->first_name}}, good to see you again
                                Boss.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                {{-- Recent Transactions --}}
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-header justify-content-between"> 
                            Payout History 
                            <div class="float-right ">
                                <form class="form-inline p-2"
                                    method="GET">
                                    {{-- @csrf --}}
                                    <div class="form-group mr-2">
                                        <label for="">Start date </label>
                                        <input type="date" required name="start" value="{{app('request')->input('start')}}" class="ml-2 form-control">
                                    </div>
                                    <div class="form-group mr-2">
                                        <label for="">End date </label>
                                        <input type="date" required name="end" value="{{app('request')->input('end')}}" class="ml-2 form-control">
                                    </div>
                                    <button class="btn btn-outline-primary"><i class="fa fa-filter"></i></button>
                                </form>
                            </div>
                        </div>
                        

                        <div class="col-md-12">
                            <div class="col-md-12">
                                <div class="main-card mb-3 card">
                                    <div class="card-header d-flex justify-content-between">
                                        <span>payout History</span>
                                        <div class="page-title-subheading">
                                            <a href="{{route('admin.payout_history')}}" class="btn btn-primary"> Refresh </a>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Total Asset Volume</th>
                                                    <th>Total card volume in Naira </th>
                                                    <th> Total successful transactions</th>
                                                    <th>Worth of Traded Assets</th>
                                                    <th>Sum of Chinese Amount</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>@php
                                                $sn = 1;
                                            @endphp
                                                @foreach ($payoutHistory as $history)
                                                    <tr>
                                                        <td>{{$sn++}}</td>
                                                        <td>{{$history->card_asset_volume}}</td>
                                                        <td>N {{number_format($history->card_volume_in_naira)}}</td>
                                                        <td>{{number_format($history->success_transactions)}}</td>
                                                        <td>{{number_format($history->traded_asset_amount)}}</td>
                                                        <td>{{number_format($history->total_chinese_amount)}}</td>
                                                        <td>{{$history->created_at->format('d M y, h:ia')}}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        {{-- <tfoot>
                                            <a href=" {{route('admin.transactions-status', 'in progress')}} "><button class="m-3 btn btn-outline-info">View all</button></a>
                                        </tfoot> --}}
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



@if($usersChart)
{!! $usersChart->script() !!}
@endif
@endsection
