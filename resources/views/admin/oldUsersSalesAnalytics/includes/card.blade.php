<div class="row">
    {{-- number of called users --}}
    <div class="col-md-3 col-xl-3 to_trans_page"
    onclick="window.location = '{{route('sales.oldUsers.salesAnalytics',['type'=>'calledUsers'])}}'" >
    @if ($type == "calledUsers")
        <div class="card mb-3 widget-content  bg-amy-crisp">
        @else
        <div class="card mb-3 widget-content  bg-secondary">
    @endif
        
            <div class="widget-content-wrapper py-2 text-white">
                <div class="widget-content-actions mx-auto ">
                    <div class="widget-heading text-center">
                        <h5>Number of Called Users</h5>
                        <h6>{{ number_format($noOfCalledUsers) }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-md-3 col-xl-3 to_trans_page"
    onclick="window.location = '{{route('sales.oldUsers.salesAnalytics',['type'=>'respondedUsers'])}}'" >
         @if ($type == "respondedUsers")
        <div class="card mb-3 widget-content  bg-amy-crisp">
        @else
        <div class="card mb-3 widget-content  bg-secondary">
        @endif
            <div class="widget-content-wrapper py-2 text-white">
                <div class="widget-content-actions mx-auto ">
                    <div class="widget-heading text-center">
                        <h5>No of Responded Users</h5>
                        <h6>{{ number_format($noOfRespondedUsers) }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-md-3 col-xl-3 to_trans_page"
    onclick="window.location = '{{route('sales.oldUsers.salesAnalytics',['type'=>'TradesRespondedUsers'])}}'" >
        @if ($type == "TradesRespondedUsers")
        <div class="card mb-3 widget-content  bg-amy-crisp">
        @else
        <div class="card mb-3 widget-content  bg-secondary">
        @endif
            <div class="widget-content-wrapper py-2 text-white">
                <div class="widget-content-actions mx-auto ">
                    <div class="widget-heading text-center">
                        <h5>Trades From Responded User</h5>
                        <h6>{{ number_format($respondedTranxNo) }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-md-3 col-xl-3 to_trans_page"
    onclick="window.location = '{{route('sales.oldUsers.salesAnalytics',['type'=>'CPE'])}}'" >
        @if ($type == "CPE")
        <div class="card mb-3 widget-content  bg-amy-crisp">
        @else
        <div class="card mb-3 widget-content  bg-secondary">
        @endif
            <div class="widget-content-wrapper py-2 text-white">
                <div class="widget-content-actions mx-auto ">
                    <div class="widget-heading text-center">
                        <h5>Call Percentage Effectiveness</h5>
                        <h6>{{ $callPercentageEffectiveness }}%</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-md-3 col-xl-3 to_trans_page"
    onclick="window.location = '{{route('sales.oldUsers.salesAnalytics',['type'=>'RUV'])}}'" >
        @if ($type == "RUV")
        <div class="card mb-3 widget-content  bg-amy-crisp">
        @else
        <div class="card mb-3 widget-content  bg-secondary">
        @endif
            <div class="widget-content-wrapper py-2 text-white">
                <div class="widget-content-actions mx-auto ">
                    <div class="widget-heading text-center">
                        <h5>Responded Users Volume</h5>
                        <h6>${{ number_format($respondedTranxVolume) }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-md-3 col-xl-3 to_trans_page"
    onclick="window.location = '{{route('sales.oldUsers.salesAnalytics',['type'=>'ACD'])}}'" >
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
    
    <div class="col-md-3 col-xl-3 to_trans_page"
    onclick="window.location = '{{route('sales.oldUsers.salesAnalytics',['type'=>'TCD'])}}'" >
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

   
    <div class="col-md-3 col-xl-3 to_trans_page"
    onclick="window.location = '{{route('sales.oldUsers.salesAnalytics',['type'=>'ATBC'])}}'" >
        @if ($type == "ATBC")
        <div class="card mb-3 widget-content  bg-amy-crisp">
        @else
        <div class="card mb-3 widget-content  bg-secondary">
        @endif
            <div class="widget-content-wrapper py-2 text-white">
                <div class="widget-content-actions mx-auto ">
                    <div class="widget-heading text-center">
                        <h5>Average Time between Calls</h5>
                        <h6>{{ $averageTimeBetweenCalls }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>


    
</div>

{{-- bg-ripe-malin --}}