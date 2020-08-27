@php
$cards = App\Card::orderBy('name', 'asc')->get(['name']);
$emails = App\User::orderBy('email', 'asc' )->pluck('email');
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
                        <div> {{$segment}} Transactions
                            <div class="page-title-subheading">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-header justify-content-between ">
                            {{$segment}} Transactions
                            <form action="" class="form-inline p-2" >
                                <div class="form-group mr-2">
                                    <label for="">Start date </label>
                                    <input type="date" name="" class="ml-2 form-control">
                                </div>
                                <div class="form-group mr-2">
                                    <label for="">End date </label>
                                    <input type="date" name="" class="ml-2 form-control">
                                </div>
                                <button class="btn btn-outline-primary"><i class="fa fa-filter"></i></button>
                            </form>
                        </div>
                        <div class="table-responsive p-3">

                            <table class="align-middle mb-4 table table-bordered table-striped transactions-table ">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Reff</th>
                                        <th>User Name</th>
                                        <th>Trans. Type</th>
                                        <th>Amount</th>
                                        <th>Prev. Bal  </th>
                                        <th>Cur. Bal</th>
                                        <th>Charge</th>
                                        <th>Cr. Acct.</th>
                                        <th>Debit Acct.</th>
                                        <th>Narration</th>
                                        <th>Date</th>
                                        {{-- <th>Status</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $t)
                                    <tr>
                                        <td>{{$t->id}} </td>
                                        <td>{{$t->reference}} </td>
                                        <td>
                                            <a href="{{route('admin.user', [$t->user->id, $t->user->email ] )}}">
                                                {{$t->user->first_name}}
                                            </a>
                                        </td>
                                        <td>{{$t->transactionType->name}} </td>
                                        <td>₦{{number_format($t->amount) }} </td>
                                        <td>₦{{number_format($t->previous_balance) }}</td>
                                        <td>₦{{number_format($t->current_balance) }} </td>
                                        <td>₦{{number_format($t->charge) }} </td>
                                        <td>{{$t->cr_acct_name}} </td>
                                        <td>{{$t->dr_acct_name}} </td>
                                        <td>{{$t->narration}} </td>
                                        <td>{{$t->created_at->format('d M Y')}} </td>
                                        {{-- <td>{{$t->status}} </td> --}}
                                    </tr>
                                    @endforeach
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
