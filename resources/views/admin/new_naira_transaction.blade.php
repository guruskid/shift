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
                        <div> Add New Transaction
                        </div>
                    </div>
                </div>
            </div>

            @if (in_array(Auth::user()->role, [999, 889]))
            <div class="row">
                <div class="col-md-12">
                    @foreach ($errors->all() as $err)
                        <p class="text-danger">{{ $err }}</p>
                    @endforeach
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <form action="{{ route('admin.naira-transaction.store') }}" method="POST">@csrf
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <label for="">Account Number</label>
                                        <input type="number" id="account-number"  name="account_number" class="form-control" >
                                        <input type="hidden" id="wallet-id" name="wallet_id"  >
                                        <input type="hidden" value="{{ \Str::random(2) . time() }}" name="reference" >
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label for="">Account Name</label>
                                        <input type="text" id="account-name"  class="form-control" readonly >
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label for="">Email</label>
                                        <input type="text" id="email"  class="form-control" readonly >
                                    </div>
                                    <div class="col-md-3">
                                        <label for="">Amount</label>
                                        <input type="number" name="amount" class="form-control" required >
                                    </div>
                                    <div class="col-md-3">
                                        <label for="">Description</label>
                                        <input type="text" name="narration" class="form-control" required >
                                    </div>
                                    <div class="col-md-3">
                                        <label for="">Type</label>
                                        <select name="transaction_type" class="form-control">
                                            @foreach ($transaction_types as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="">Pin</label>
                                        <input type="password" name="pin" maxlength="4" class="form-control" required >
                                    </div>
                                </div>
                                <button class="mt-2 btn btn-primary">Add Transaction</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-header justify-content-between ">
                            Transactions
                            <div>
                                <strong>Balance: </strong><span id="wallet-balance" class="text-primary"></span>
                            </div>
                        </div>
                        <div class="table-responsive p-3">

                            <table class="align-middle mb-4 table table-bordered table-striped transactions-table ">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Reff</th>
                                        <th>Trans. Type</th>
                                        <th>Amount Paid</th>
                                        <th>Total Charge</th>
                                        <th>Total</th>
                                        <th>Prev. Bal </th>
                                        <th>Cur. Bal</th>
                                        <th>Cr. Acct.</th>
                                        <th>Debit Acct.</th>
                                        <th>Narration</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="transactions-list">

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
