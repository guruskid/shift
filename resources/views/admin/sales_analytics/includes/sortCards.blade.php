<div class="row">
    {{-- number of called users --}}
    <div class="col-md-3 col-xl-3 to_trans_page"
    onclick="window.location = '{{route('sales.sort.salesAnalytics',['type'=>'calledUsers'])}}'" >
    @if ($type == "calledUsers")
        <div class="card mb-3 widget-content  bg-amy-crisp">
        @else
        <div class="card mb-3 widget-content  bg-secondary">
    @endif
        
            <div class="widget-content-wrapper py-2 text-white">
                <div class="widget-content-actions mx-auto ">
                    <div class="widget-heading text-center">
                        <h5>Number of Called Users</h5>
                        <h6>{{ number_format($noCalledUsers) }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- No of good Leads --}}
    <div class="col-md-3 col-xl-3 to_trans_page"
    onclick="window.location = '{{route('sales.sort.salesAnalytics',['type'=>'GoodLead'])}}'" >
         @if ($type == "GoodLead")
        <div class="card mb-3 widget-content  bg-amy-crisp">
        @else
        <div class="card mb-3 widget-content  bg-secondary">
        @endif
            <div class="widget-content-wrapper py-2 text-white">
                <div class="widget-content-actions mx-auto ">
                    <div class="widget-heading text-center">
                        <h5>No of good Leads</h5>
                        <h6>{{ number_format($noGoodLeads) }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Good Lead Conversion --}}
    <div class="col-md-2 col-xl-2 to_trans_page"
    onclick="window.location = '{{route('sales.sort.salesAnalytics',['type'=>'GLConversion'])}}'" >
        @if ($type == "GLConversion")
        <div class="card mb-3 widget-content  bg-amy-crisp">
        @else
        <div class="card mb-3 widget-content  bg-secondary">
        @endif
            <div class="widget-content-wrapper py-2 text-white">
                <div class="widget-content-actions mx-auto ">
                    <div class="widget-heading text-center">
                        <h5>Good Lead Conversation</h5>
                        <h6>{{ $goodLeadConversionRate }}%</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- No of Bad Leads --}}
    <div class="col-md-2 col-xl-2 to_trans_page"
    onclick="window.location = '{{route('sales.sort.salesAnalytics',['type'=>'BadLead'])}}'" >
        @if ($type == "BadLead")
        <div class="card mb-3 widget-content  bg-amy-crisp">
        @else
        <div class="card mb-3 widget-content  bg-secondary">
        @endif
            <div class="widget-content-wrapper py-2 text-white">
                <div class="widget-content-actions mx-auto ">
                    <div class="widget-heading text-center">
                        <h5>No of Bad Leads</h5>
                        <h6>{{ number_format($noBadLeads) }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Average Time between Calls --}}
    <div class="col-md-2 col-xl-2 to_trans_page"
    onclick="window.location = '{{route('sales.sort.salesAnalytics',['type'=>'ATBC'])}}'" >
        @if ($type == "ATBC")
        <div class="card mb-3 widget-content  bg-amy-crisp">
        @else
        <div class="card mb-3 widget-content  bg-secondary">
        @endif
            <div class="widget-content-wrapper py-2 text-white">
                <div class="widget-content-actions mx-auto ">
                    <div class="widget-heading text-center">
                        <h5>Average Time Between Calls</h5>
                        <h6>{{ $averageTimeBetweenCalls }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bad Lead Conversion --}}
    <div class="col-md-3 col-xl-3 to_trans_page"
    onclick="window.location = '{{route('sales.sort.salesAnalytics',['type'=>'BLConversion'])}}'" >
        @if ($type == "BLConversion")
        <div class="card mb-3 widget-content  bg-amy-crisp">
        @else
        <div class="card mb-3 widget-content  bg-secondary">
        @endif
            <div class="widget-content-wrapper py-2 text-white">
                <div class="widget-content-actions mx-auto ">
                    <div class="widget-heading text-center">
                        <h5>Bad Lead Conversion</h5>
                        <h6>{{ $badLeadConversionRate }}%</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TotalConversion Ratr --}}
    <div class="col-md-3 col-xl-3 to_trans_page"
    onclick="window.location = '{{route('sales.sort.salesAnalytics',['type'=>'TCR'])}}'" >
        @if ($type == "TCR")
        <div class="card mb-3 widget-content  bg-amy-crisp">
        @else
        <div class="card mb-3 widget-content  bg-secondary">
        @endif
            <div class="widget-content-wrapper py-2 text-white">
                <div class="widget-content-actions mx-auto ">
                    <div class="widget-heading text-center">
                        <h5>Total Conversation Rate</h5>
                        <h6>{{ $totalConversionRate }}%</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Conversion VOlume --}}
    <div class="col-md-2 col-xl-2 to_trans_page"
    onclick="window.location = '{{route('sales.sort.salesAnalytics',['type'=>'TCV'])}}'" >
        @if ($type == "TCV")
        <div class="card mb-3 widget-content  bg-amy-crisp">
        @else
        <div class="card mb-3 widget-content  bg-secondary">
        @endif
            <div class="widget-content-wrapper py-2 text-white">
                <div class="widget-content-actions mx-auto ">
                    <div class="widget-heading text-center">
                        <h5>Total Conversion Volume</h5>
                        <h6>${{ $totalConversionVolume }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- total Call Duration --}}
    <div class="col-md-2 col-xl-2 to_trans_page"
    onclick="window.location = '{{route('sales.sort.salesAnalytics',['type'=>'TCD'])}}'" >
        @if ($type == "TCD")
        <div class="card mb-3 widget-content  bg-amy-crisp">
        @else
        <div class="card mb-3 widget-content  bg-secondary">
        @endif
            <div class="widget-content-wrapper py-2 text-white">
                <div class="widget-content-actions mx-auto ">
                    <div class="widget-heading text-center">
                        <h5>Total Call Duration</h5>
                        <h6>{{ $totalCallDuration }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- AverageCallDuration --}}
    <div class="col-md-2 col-xl-2 to_trans_page"
    onclick="window.location = '{{route('sales.sort.salesAnalytics',['type'=>'ACD'])}}'" >
        @if ($type == "ACD")
        <div class="card mb-3 widget-content  bg-amy-crisp">
        @else
        <div class="card mb-3 widget-content  bg-secondary">
        @endif
            <div class="widget-content-wrapper py-2 text-white">
                <div class="widget-content-actions mx-auto ">
                    <div class="widget-heading text-center">
                        <h5>Average Call Duration</h5>
                        <h6>{{ $averageCallDuration }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- bg-ripe-malin --}}