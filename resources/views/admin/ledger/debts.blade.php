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
                            <div>Ledger Resolves</div>
                        </div>
                    </div>
                </div>

                <div class="card card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive p-3">
                                <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Amount</th>
                                            <th>Description</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($transactions as $txn)
                                            <tr>
                                                <td class="text-muted">{{ $txn->id }}</td>
                                                <td>{{ ucwords($txn->user->first_name) }}</td>
                                                <td>₦{{ number_format($txn->amount) }}</td>
                                                <td>{{ $txn->narration }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a class="btn btn-alternate"
                                                            href=" {{ route('admin.user', [$txn->user->id, $txn->user->email]) }} ">View user</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ $transactions->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
