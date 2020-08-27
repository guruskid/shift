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
        @include('layouts.partials.user')
    </div>

    {{-- Content Starts here --}}
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="app-page-title">
                <div class="page-title-wrapper">
                    <div class="page-title-heading">
                        <div class="page-title-icon">
                            <i class="pe-7s-graph1 icon-gradient bg-malibu-beach">
                            </i>
                        </div>
                        <div>All Rates
                            <div class="page-title-subheading">Check out our rates and exchange values
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <ul class="nav nav-tabs nav-justified">
                                <li class="nav-item"><a data-toggle="tab" href="#tab-eg11-1" class="active nav-link">Sell (to Dantown) </a>
                                </li>
                                <li class="nav-item"><a data-toggle="tab" href="#tab-eg11-0"
                                        class=" nav-link">Buy (from Dantown) </a></li>
                            </ul>
                            <div class="tab-content">
                                {{-- Sell --}}
                                <div class="tab-pane active" id="tab-eg11-1" role="tabpanel">
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
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($sell as $s)
                                                        <tr>
                                                            <td class="text-center">{{ ucfirst($s->card) }}</td>
                                                            <td class="text-center">{{$s->usd == '' ? '-': $s->usd }}</td>
                                                            <td class="text-center">{{$s->eur == '' ? '-': $s->eur }}</td>
                                                            <td class="text-center">{{$s->gbp == '' ? '-': $s->gbp }}</td>
                                                            <td class="text-center">{{$s->aud == '' ? '-': $s->aud }}</td>
                                                            <td class="text-center">{{$s->cad == '' ? '-': $s->cad }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                </div>

                                {{-- Buy --}}
                                <div class="tab-pane " id="tab-eg11-0" role="tabpanel">
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
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($buy as $b)
                                                <tr>
                                                    <td class="text-center">{{ ucfirst($b->card) }}</td>
                                                    <td class="text-center">{{$b->usd == '' ? '-': $b->usd }}</td>
                                                    <td class="text-center">{{$b->eur == '' ? '-': $b->eur }}</td>
                                                    <td class="text-center">{{$b->gbp == '' ? '-': $b->gbp }}</td>
                                                    <td class="text-center">{{$b->aud == '' ? '-': $b->aud }}</td>
                                                    <td class="text-center">{{$b->cad == '' ? '-': $b->cad }}</td>
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
        </div>
    </div>
</div>
@endsection

