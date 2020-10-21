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
            {{-- <div class="app-page-title">
                <div class="page-title-wrapper">
                    <div class="page-title-heading">
                        <div class="page-title-icon">
                            <i class="pe-7s-graph1 icon-gradient bg-warm-flame">
                            </i>
                        </div>
                        <div>Rates</div>
                    </div>
                </div>
            </div> --}}

            {{-- For Super Admins --}}
            @if (in_array(Auth::user()->role, [999, 666] ))
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="main-card card shadow">
                        <div class="card-header d-flex justify-content-between">
                            Cards
                            <button data-toggle="modal" data-target="#add-card-modal"
                                class="btn btn-sm btn-primary shadow"><i class="fa fa-plus"></i> Add card</button>
                        </div>
                        <div class="card-body" style="height: 200px; overflow: auto;">
                            @foreach ($cards as $card)
                            <div class="media mb-2">
                                <img src="{{ asset('storage/assets/'.$card->image) }}" style="height: 30px;"
                                    alt="{{ $card->name }}" class="align-self-center mr-3">
                                <div class="media-body">
                                    <h5>{{ $card->name }}</h5>
                                </div>
                                {{-- <a href="#" title="Add rate" class="mr-2" data-toggle="modal" data-target="#add-card-modal"><i class="fa fa-plus"></i></a> --}}
                                <a href="#" title="Edit card" onclick="editCard({{ $card }})" class="text-warning"
                                    data-toggle="modal" data-target="#edit-card-modal"><i class="fa fa-pen"></i></a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="main-card card shadow">
                        <div class="card-header d-flex justify-content-between">
                            Currencies
                            <button data-toggle="modal" data-target="#add-currency-modal"
                                class="btn btn-sm btn-primary shadow"><i class="fa fa-plus"></i> Add Currency</button>
                        </div>
                        <div class="card-body" style="height: 200px; overflow: auto;">
                            @foreach ($currencies as $currency)
                            <div class="media mb-2">
                                {{-- <img src="{{ asset('storage/assets/'.$currency->image) }}" style="height: 30px;"
                                alt="{{ $currency->name }}" class="align-self-center mr-3"> --}}
                                <div class="media-body">
                                    <h5>{{ $currency->name }}</h5>
                                </div>
                                {{-- <a href="#" title="Edit card" onclick="editCurrency({{ $currency }})"
                                class="text-warning" data-toggle="modal" data-target="edit-card-modal"><i
                                    class="fa fa-pen"></i></a> --}}
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="main-card card shadow">
                        <div class="card-header d-flex justify-content-between">
                            Card Types
                            <button data-toggle="modal" data-target="#add-card-type-modal"
                                class="btn btn-sm btn-primary shadow"><i class="fa fa-plus"></i> Add Card Type</button>
                        </div>
                        <div class="card-body" style="height: 200px; overflow: auto;">
                            @foreach ($card_types as $cardtype)
                            <div class="media mb-2">
                                {{-- <img src="{{ asset('storage/assets/'.$cardtype->image) }}" style="height: 30px;"
                                alt="{{ $cardtype->name }}" class="align-self-center mr-3"> --}}
                                <div class="media-body">
                                    <h5>{{ $cardtype->name }}</h5>
                                </div>
                                {{-- <a href="#" title="Edit card" onclick="editCurrency({{ $cardtype }})"
                                class="text-warning" data-toggle="modal" data-target="edit-card-modal"><i
                                    class="fa fa-pen"></i></a> --}}
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card card card-body mb-3">
                        @foreach ($errors->all() as $err)
                            <p class="text-warning">{{ $err }}</p>
                        @endforeach
                       <h5>Add Card Combination</h5>
                    <form action="{{ route('admin.rate.add') }}" method="POST"> @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <select name="card_id" id="" class="form-control  ">
                                    <option value="card_id">Select Card</option>
                                    @foreach ($cards as $card)
                                    <option value="{{ $card->id }}">{{ $card->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <select name="currency_id" id="" class="form-control  ">
                                    <option value="currency_id">Select Currency</option>
                                    @foreach ($currencies as $currency)
                                    <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <select name="payment_medium_id" id="" class="form-control  ">
                                    <option value="">Select Card Type</option>
                                    @foreach ($card_types as $card_type)
                                    <option value="{{ $card_type->id }}">{{ $card_type->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select name="buy_sell" id="" class="form-control  ">
                                    <option value="">Trade Type</option>
                                    <option value="1">Buy</option>
                                    <option value="2">Sel</option>
                                </select>
                            </div>

                            <button class="col-md-1 btn btn-primary">Add</button>
                        </div>
                    </form>
                   </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <ul class="nav nav-tabs nav-justified">
                                <li class="nav-item"><a data-toggle="tab" href="#tab-eg11-0" class="active nav-link">Buy
                                        (from Dantown)</a></li>
                                <li class="nav-item"><a data-toggle="tab" href="#tab-eg11-1" class="nav-link">Sell (to
                                        Dantown)</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                {{-- Sell --}}
                                <div class="tab-pane active" id="tab-eg11-0" role="tabpanel">
                                    <div class="row">
                                        @foreach ($rates as $rate)
                                        <div class="col-md-4">
                                            <div class="main-card card shadow-lg border mb-3">
                                                <div class="card-header d-block">
                                                    {{ $rate->card_name }} > {{ $rate->currency_name }} > {{ $rate->paymentMedium->name }}
                                                    <button data-toggle="modal" data-target="#add-card-type-modal"
                                                        class="btn btn-sm btn-primary shadow"><i class="fa fa-plus"></i>
                                                        Add Rate</button>
                                                </div>
                                                <div class="card-body" style="height: 200px; overflow: auto;">
                                                    @foreach ($rate->rates as $item)
                                                    <div class="media mb-2">
                                                        <img src="{{ asset('storage/assets/steam.png') }}"
                                                            style="height: 30px;" alt="" class="align-self-top mr-3">
                                                        <div class="media-body d-flex justify-content-between">
                                                                <h5>{{ $item->value }}</h5>
                                                                <h5>₦{{ $item->rate }}</h5>
                                                            <div>
                                                                <a href="#" title="Edit card" class="text-warning mr-2"
                                                                    data-toggle="modal"
                                                                    data-target="#edit-card-modal"><i
                                                                        class="fa fa-pen"></i></a>
                                                                <a href="#" title="Edit card" class="text-danger"
                                                                    data-toggle="modal"
                                                                    data-target="#edit-card-modal"><i
                                                                        class="fa fa-trash"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                {{-- Buy --}}
                                <div class="tab-pane" id="tab-eg11-1" role="tabpanel">
                                    <div class="row">

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


{{-- Edit Rate Modal --}}
<div class="modal fade  item-badge-rightm" id="edit-rate-modal" role="dialog">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form action="{{-- {{route('admin.edit_rate')}} --}} " method="POST" class="mb-3">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-3">
                            <div class="form-group">
                                <label for="">Card</label>
                                <select name="card" class="form-control">
                                    <option id="card"></option>
                                    {{--  @foreach ($cards as $card)
                                    <option value=" {{$card->name}} "> {{ ucfirst($card->name) }} </option>
                                    @endforeach --}}
                                </select>
                            </div>
                        </div>

                        <div class="col">
                            <div class="form-group">
                                <label for="">USD</label>
                                <input type="number" id="usd" required class="form-control" name="usd">
                                <input type="hidden" id="rate-id" required class="form-control" name="id">
                            </div>
                        </div>

                        <div class="col">
                            <div class="form-group">
                                <label for="">EUR</label>
                                <input type="number" id="eur" class="form-control" name="eur">
                            </div>
                        </div>

                        <div class="col">
                            <div class="form-group">
                                <label for="">GBP</label>
                                <input type="number" id="gbp" class="form-control" name="gbp">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="">AUD</label>
                                <input type="number" id="aud" class="form-control" name="aud">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="">CAD</label>
                                <input type="number" id="cad" class="form-control" name="cad">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="">Type</label>
                                <select name="rate_type" class="form-control">
                                    <option id="r-type"></option>
                                    <option value="buy">Buy (from Dantown) </option>
                                    <option value="sell">Sell (to Dantown) </option>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="">Min</label>
                                <input type="number" id="min" required class="form-control" name="min">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="">Max</label>
                                <input type="number" id="max" required class="form-control" name="max">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-outline-primary btn ">Save</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Add card Modal --}}
<div class="modal fade  item-badge-rightm" id="add-card-modal" role="dialog">
    <div class="modal-dialog modal-md " role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form action="{{ route('admin.card.create') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="">Card Name</label>
                        <input type="text" name="name" class="form-control">
                    </div>
                    <div class="form-group">
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input type="checkbox" name="buyable" value="true" class="form-check-input">Buyable from
                                Dantown
                            </label>
                        </div>
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input type="checkbox" name="sellable" value="true" class="form-check-input">Sellable to
                                Dantown
                            </label>
                        </div>
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input type="checkbox" name="is_crypto" class="form-check-input">Is Crypto
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="">Wallet Id</label>
                            <input type="text" name="wallet_id" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Save </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Edit Card Modal --}}
<div class="modal fade  item-badge-rightm" id="edit-card-modal" role="dialog">
    <div class="modal-dialog modal-md " role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form action="{{ route('admin.card.edit') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <img src="" id="e-card-image" class="d-block mx-auto" style="height: 100px" alt="">
                    <div class="form-group">
                        <label for="">Card Name</label>
                        <input type="text" id="e-card-name" name="name" class="form-control">
                        <input type="hidden" id="e-card-id" name="card_id">
                    </div>
                    <div class="form-group">
                        <div class="form-group">
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="checkbox" id="e-card-buyable" name="buyable" value="true"
                                        class="form-check-input">Buyable from
                                    Dantown
                                </label>
                            </div>
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="checkbox" id="e-card-sellable" name="sellable" value="true"
                                        class="form-check-input">Sellable to
                                    Dantown
                                </label>
                            </div>
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="checkbox" id="e-card-crypto" name="is_crypto"
                                        class="form-check-input">Is Crypto
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Wallet Id</label>
                            <input type="text" id="e-card-wallet" name="wallet_id" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="">Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Save </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Add currency Modal --}}
<div class="modal fade  item-badge-rightm" id="add-currency-modal" role="dialog">
    <div class="modal-dialog modal-md " role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form action="{{ route('admin.currency.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="">Card Name</label>
                        <input type="text" name="name" class="form-control">
                    </div>
                    {{-- <div class="form-group">
                        <label for="">Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div> --}}
                    <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Save </button>
                </form>
            </div>
        </div>
    </div>
</div>


{{--Add card type Modal  --}}
<div class="modal fade  item-badge-rightm" id="add-card-type-modal" role="dialog">
    <div class="modal-dialog modal-md " role="document">
        <div class="modal-content">
            <div class="modal-body">
                <form action="{{ route('admin.card-type.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="">Card Type Name</label>
                        <input type="text" name="name" class="form-control">
                    </div>
                    {{-- <div class="form-group">
                        <label for="">Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div> --}}
                    <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Save </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
