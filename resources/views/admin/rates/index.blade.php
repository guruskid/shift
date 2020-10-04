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
                            <i class="pe-7s-graph1 icon-gradient bg-warm-flame">
                            </i>
                        </div>
                        <div>Rates</div>
                    </div>
                </div>
            </div>

            {{-- For Super Admins --}}
            @if (in_array(Auth::user()->role, [999, 666] ))
            <div class="row mb-4">
                <div class="col-md-5">
                    <div class="main-card card shadow">
                        <div class="card-header">
                            Cards
                        </div>
                        <div class="card-body">
                            <div class="media">
                                <i class="fa fa-box-open  align-self-center mr-3" style="font-size: 14px" ></i>
                                <div class="media-body">
                                  <h5>Walmart</h5>
                                </div>
                              </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="main-card shadow">
                        <div class="card-header">
                            Currencies
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="main-card shadow">
                        <div class="card-header">
                            Card Type
                        </div>
                    </div>
                </div>

            </div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <ul class="nav nav-tabs nav-justified">
                                <li class="nav-item"><a data-toggle="tab" href="#tab-eg11-0"
                                        class="active nav-link">Buy (from Dantown)</a></li>
                                <li class="nav-item"><a data-toggle="tab" href="#tab-eg11-1" class="nav-link">Sell (to Dantown)</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                {{-- Sell --}}
                                <div class="tab-pane active" id="tab-eg11-0" role="tabpanel">
                                    <div class="table-responsive">
                                        <table
                                            class="align-middle mb-0 table table-borderless table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th class=" text-center">Asset</th>
                                                    <th class=" text-center">USD</th>
                                                    <th class=" text-center">EUR</th>
                                                    <th class=" text-center">GBP</th>
                                                    <th class=" text-center">AUD</th>
                                                    <th class=" text-center">CAD</th>
                                                    <th class=" text-center">Min</th>
                                                    <th class=" text-center">Max</th>
                                                    @if (in_array(Auth::user()->role, [999, 666] ))
                                                    <th class=" text-center">Actions</th>
                                                    @endif

                                                </tr>
                                            </thead>
                                            <tbody>
                                              {{--   @foreach ($buy as $b)
                                                <tr>
                                                    <td class="text-center">{{ ucfirst($b->card) }}</td>
                                                    <td class="text-center">{{$b->usd == '' ? '-': $b->usd }}</td>
                                                    <td class="text-center">{{$b->eur == '' ? '-': $b->eur }}</td>
                                                    <td class="text-center">{{$b->gbp == '' ? '-': $b->gbp }}</td>
                                                    <td class="text-center">{{$b->aud == '' ? '-': $b->aud }}</td>
                                                    <td class="text-center">{{$b->cad == '' ? '-': $b->cad }}</td>
                                                    <td class="text-center">{{$b->min == '' ? '-': $b->min }}</td>
                                                    <td class="text-center">{{$b->max == '' ? '-': $b->max }}</td>
                                                    @if (in_array(Auth::user()->role, [999, 666] ))
                                                    <td class="text-center">
                                                        <a href="#" data-toggle="modal" data-target="#edit-rate-modal" onclick="editRate({{$b}})" >
                                                            <span class="badge badge-info">Edit</span>
                                                        </a>
                                                        <a href="#" onclick="deleteRate({{$b->id}})" >
                                                            <span class="badge badge-danger">Delete</span>
                                                        </a>
                                                    </td>
                                                    @endif
                                                </tr>
                                                @endforeach --}}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                {{-- Buy --}}
                                <div class="tab-pane" id="tab-eg11-1" role="tabpanel">
                                    <div class="table-responsive">
                                        <table
                                            class="align-middle mb-0 table table-borderless table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th class=" text-center">Asset</th>
                                                    <th class=" text-center">USD</th>
                                                    <th class=" text-center">EUR</th>
                                                    <th class=" text-center">GBP</th>
                                                    <th class=" text-center">AUD</th>
                                                    <th class=" text-center">CAD</th>
                                                    <th class=" text-center">Min</th>
                                                    <th class=" text-center">Max</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- @foreach ($sell as $s)
                                                <tr>
                                                    <td class="text-center">{{ ucfirst($s->card) }}</td>
                                                    <td class="text-center">{{$s->usd == '' ? '-': $s->usd }}</td>
                                                    <td class="text-center">{{$s->eur == '' ? '-': $s->eur }}</td>
                                                    <td class="text-center">{{$s->gbp == '' ? '-': $s->gbp }}</td>
                                                    <td class="text-center">{{$s->aud == '' ? '-': $s->aud }}</td>
                                                    <td class="text-center">{{$s->cad == '' ? '-': $s->cad }}</td>
                                                    <td class="text-center">{{$s->min == '' ? '-': $s->min }}</td>
                                                    <td class="text-center">{{$s->max == '' ? '-': $s->max }}</td>
                                                    <td class="text-center">
                                                        <a href="#" data-toggle="modal" data-target="#edit-rate-modal" onclick="editRate({{$s}})" >
                                                            <span class="badge badge-info">Edit</span>
                                                        </a>
                                                        <a href="#" onclick="deleteRate({{$s->id}})" >
                                                            <span class="badge badge-danger">Delete</span>
                                                        </a>
                                                    </td>
                                                </tr>
                                                @endforeach --}}
                                            </tbody>
                                        </table>
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
                <form action="{{route('admin.edit_rate')}} " method="POST" class="mb-3">
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
@endsection
