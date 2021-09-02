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
                        <div>Naira P2P Transactions</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 pb-3 card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="">
                                Transactions
                            </div>
                            @if ($show_limit)
                            <div>
                                <button data-toggle="modal" data-target="#limits-modal" class="btn btn-primary">Set Trade Limits</button>
                            <button data-toggle="modal" data-target="#account-modal" class="btn btn-primary">Set account details</button>
                            </div>
                            @endif
                        </div>
                        <div class="table-responsive p-3">
                            <table
                                class="align-middle mb-0 table table-borderless table-striped table-hover transactions-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Amount</th>
                                        <th>Reference</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $key => $t)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $t->user->first_name }}</td>
                                        <td>{{ $t->user->phone }}</td>
                                        <td>₦{{ number_format($t->amount) }}</td>
                                        <td>{{ $t->reference }}</td>
                                        <td>{{ $t->type }}
                                        @if($t->type == 'sell')
                                            <br><br>
                                            {{ $t->acct_details }}
                                        @endif
                                        </td>
                                        <td>{{ $t->created_at->format('d m y, h:ia') }}</td>
                                        <td>{{ $t->status }}</td>
                                        <td>
                                            <div class="btn-group">
                                                @if ($t->status == 'waiting')
                                                <button data-toggle="modal" data-target="#confirm-modal-{{ $t->id }}" class="btn btn-primary">Approve</button>
                                                <button class="btn btn-danger">Cancel</button>
                                                @endif
                                            </div>
                                        </td>
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

@if(!empty($transactions))
    {{-- Confirm trade approval modal --}}
    @foreach ($transactions as $t)
    <div class="modal fade " id="confirm-modal-{{ $t->id }}">
        <div class="modal-dialog  ">
            <form action="{{route('admin.naira-p2p.confirm', $t)}}" id="freeze-form" method="post"> @method('put')
                @csrf
                <div class="modal-content  c-rounded">
                    <!-- Modal Header -->
                    <div class="modal-header bg-custom-gradient c-rounded-top p-4 ">
                        <h4 class="modal-title">Confirm Trade <i class="fa fa-paper-plane"></i></h4>
                        <button type="button" class="close bg-light rounded-circle " data-dismiss="modal">&times;</button>
                    </div>
                    <!-- Modal body -->

                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Wallet pin </label>
                                    <input type="password" name="pin" required class="form-control">
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-block c-rounded bg-custom-gradient txn-btn">
                            Confirm
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endforeach
@endif

@if ($show_limit)
{{-- Set Limits --}}
<div class="modal fade " id="limits-modal">
    <div class="modal-dialog modal-dialog-centered ">
        <form action="{{ route('admin.naira-p2p.set-limits') }}" id="freeze-form" method="post"> @csrf
            <div class="modal-content  c-rounded">
                <!-- Modal body -->

                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Minimum</label>
                                <input type="number" name="min" value="{{ Auth::user()->agentLimits->min }}" required
                                    class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Maximum</label>
                                <input type="number" name="max" value="{{ Auth::user()->agentLimits->max }}" required
                                    class="form-control">
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-block c-rounded bg-custom-gradient txn-btn">
                        Save
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

    @if(!empty($account))
        <div class="modal fade " id="account-modal">
            <div class="modal-dialog modal-dialog-centered ">
                <div class="modal-content">
                    <div class="modal-body">
                        <form action="{{ route('agent.update-bank') }}" method="POST" class="mb-4">@csrf
                            <div class="form-row ">
                                <div class="col-md-12">
                                    <input type="hidden" value="{{ $account->id }}" name="id">
                                    <div class="position-relative form-group">
                                        <label>Bank Name</label>
                                        <select name="bank_id" class="form-control">
                                            <option value="{{ $account->bank_id }}">{{ $account->bank_name }}</option>
                                            @foreach ($banks as $b)
                                            <option value="{{$b->id}}">{{$b->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="position-relative form-group">
                                        <label>Account Number</label>
                                        <input type="text" required class="form-control" value="{{ $account->account_number }}" name="account_number">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="position-relative form-group">
                                        <label>Account Name</label>
                                        <input type="text" required class="form-control" value="{{ $account->account_name }}" name="account_name">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" id="sign-up-btn" class="mt-2 btn btn-outline-primary">
                                <i class="spinner-border spinner-border-sm" id="s-b" style="display: none;"></i>
                                Save
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif
@endsection
