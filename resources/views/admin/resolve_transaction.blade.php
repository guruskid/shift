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
                            <div>Resolve transaction</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card card-body shadow">
                            <form action="{{ route('admin.transaction.index') }}" method="POST"> @csrf
                                @foreach ($errors->all() as $err)
                                    <p class="text-center text-danger">{{ $err }}</p>
                                @endforeach
                                <div class="form-group">
                                    <label for="">Transaction reference</label>
                                    <input name="reference" type="text" class="form-control">
                                </div>
                                <button class="btn btn-primary">Fetch details</button>
                            </form>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card card-body">
                            <h5>Transaction details</h5>
                            @if ($transaction != null)
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td>User</td>
                                            <td>{{ $transaction->user->first_name }}</td>
                                        </tr>
                                        <tr>
                                            <td>Date</td>
                                            <td>{{ $transaction->created_at->format('d M Y, h:ia') }}</td>
                                        </tr>
                                        <tr>
                                            <td>Currency</td>
                                            <td>{{ $transaction->asset->name }}</td>
                                        </tr>
                                        <tr>
                                            <td>Amount (NGN)</td>
                                            <td>₦{{ number_format($transaction->amount_paid) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Amount (USD)</td>
                                            <td>${{ number_format($transaction->amount) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Status</td>
                                            <td>{{ $transaction->status }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <hr>
                                <form action="{{ route('admin.transaction.resolve', $transaction) }}" method="POST">@csrf
                                    <div class="form-group">
                                        <label for="">Pin</label>
                                        <input type="password" name="pin" class="form-control col-4">
                                    </div>
                                    <button class="btn btn-primary">Credit User</button>

                                </form>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
