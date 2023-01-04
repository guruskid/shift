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
                    <div class="col-md-4">
                        <div class="card card-body shadow">
                            <h4 class="text-center">Negative Ledger</h4>
                            <a href="{{ route('admin.negative-ledger') }}" class="btn btn-primary">View</a>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card card-body shadow">
                            <h4 class="text-center">Resolve Ledger Txns</h4>
                            <a href="{{ route('admin.resolve-transactions') }}" class="btn btn-primary">View</a>
                        </div>
                    </div>

                    @foreach ($extra_data as $data)
                        <div class="col-md-4">
                            <div class="card card-body shadow">
                                <div class="d-flex justify-content-between">
                                    <h4 class="text-center">{{ $data['name'] }}</h4>
                                    <h5 class="text-center">{{ $data['value'] }}</h5>
                                </div>
                                <a href="{{ $data['url'] }}" class="btn btn-primary">View</a>
                            </div>
                        </div>
                    @endforeach
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
                                            <td>₦{{ number_format($u->ledger->cr) }}</td>
                                            <td>₦{{ number_format($u->ledger->dr) }}</td>
                                            <td>₦{{ number_format($u->ledger->balance) }}</td>
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
