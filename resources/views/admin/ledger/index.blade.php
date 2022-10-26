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
                            <div>All Users</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive p-3">
                            <table class="align-middle mb-0 table table-borderless table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Total Credit</th>
                                        <th>Total Debit</th>
                                        <th>Ledger Bal</th>
                                        <th>Naira balance</th>
                                        <th>Registered</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $u)
                                        @php
                                            $status = '';
                                            if ($u->nairaWallet) {
                                                if ($u->ledger->balance > $u->nairaWallet->amount) {
                                                    $status = 'bg-info text-white';
                                                } elseif ($u->ledger->balance < $u->nairaWallet->amount) {
                                                    $status = 'bg-danger text-white';
                                                } else {
                                                    $status = 'bg-success text-white';
                                                }
                                            }
                                        @endphp
                                        <tr class="{{ $status }} ">
                                            <td class="text-muted">{{ $u->id }}</td>
                                            <td>{{ ucwords($u->first_name) }}</td>
                                            <td>{{ $u->ledger->cr }}</td>
                                            <td>{{ $u->ledger->dr }}</td>
                                            <td>{{ $u->ledger->balance }}</td>
                                            <td>₦{{ $u->nairaWallet ? number_format($u->nairaWallet->amount) : 0 }} </td>
                                            <td>{{ $u->created_at->format('d M y') }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a class="btn btn-alternate"
                                                        href=" {{ route('admin.user', [$u->id, $u->email]) }} ">View</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
