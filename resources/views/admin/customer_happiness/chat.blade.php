
@extends('layouts.customer_hapiness')

@section('content')
<div id="content" class="main-content">
    <div class="layout-px-spacing">

        <div class="dashboard-title d-flex">
            <ion-icon name="home-outline"></ion-icon>
            <div class="description">
                <h5>Dashboard Home</h5>
                <P>Hi good to see you again</P>
            </div>
        </div>
            <div class="row layout-top-spacing">

            

            


            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                <div class="row">
                    <div class="container py-5 px-4">
                        <div class="row rounded-lg overflow-hidden shadow">
                          <!-- Users box-->
                          <div class="col-5 px-0" id="userbox">
                            <div class="bg-white">
                      
                              <div class="bg-gray px-4 py-2 bg-light">
                                <p class="h5 mb-0 py-1">Recent</p>
                              </div>
                      
                              <div class="messages-box">
                                <div class="list-group rounded-0">
                                    @foreach ($userTicketsList as $list)
                                        
                                    
                                        <a href="{{ $list->status == "open" ? route('customerHappiness.chatdetails',['status'=>'New', 'ticketNo'=>$list->ticketNo]) : route('customerHappiness.chatdetails',['status'=>'Close', 'ticketNo'=>$list->ticketNo]) }}"
                                            class="list-group-item list-group-item-action 
                                                    {{ $list->ticketNo === $ticketNo ? 'active' : 'list-group-item-light'}}
                                                     text-white rounded-0">
                                            <div class="media"><img src="{{ asset($list->user->dp) }}" alt="user" height="50" width="50" class="rounded-circle">
                                            <div class="media-body ml-4">
                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                    <h4 class="mb-0">
                                                        @if (empty(trim($list->user->first_name)))
                                                            {{  $list->user->username }}
                                                        @else
                                                            {{ $list->user->first_name }}
                                                        @endif
                                                    </h4><small
                                                        class="small font-weight-bold text-muted">
                                                        {{ $list->created_at->format('d M Y') }}
                                                    </small>
                                                </div>
                                                <p class="font-italic
                                                {{ $list->ticketNo == $ticketNo ? '' : 'text-muted'}}
                                                mb-0 text-small">
                                                Ticket No = {{ $list->ticketNo }}</p>
                                            </div>
                                            </div>
                                        </a>
                                        @if ($list->status == "open")
                                            <div class="text-right bg-gray bg-light">
                                                
                                                
                                                <button class="btn btn-danger btn-sm" onclick="window.location.href='{{ route('customerHappiness.chatdetails',['status'=>'Closed', 'ticketNo'=>$list->ticketNo]) }}'">Close Ticket</button>
                                            </div>
                                        @else
                                            <div class="text-right bg-gray bg-light">
                                                <button class="btn btn-primary btn-sm" onclick="window.location.href='{{ route('customerHappiness.chatdetails',['status'=>'Opened', 'ticketNo'=>$list->ticketNo]) }}'">Open Ticket</button>
                                            </div>
                                        @endif
                            
                                        {{-- <a href="#" class="list-group-item list-group-item-action list-group-item-light rounded-0">
                                            <div class="media"><img src="https://bootstrapious.com/i/snippets/sn-chat/avatar.svg" alt="user" width="50" class="rounded-circle">
                                            <div class="media-body ml-4">
                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                <h6 class="mb-0">Jason Doe</h6><small class="small font-weight-bold">14 Dec</small>
                                                </div>
                                                <p class="font-italic text-muted mb-0 text-small">Lorem ipsum dolor sit amet, consectetur. incididunt ut labore.</p>
                                            </div>
                                            </div>
                                        </a> --}}
                                    @endforeach
                                </div>
                              </div>
                            </div>
                          </div>
                          <!-- Chat Box-->
                          <div class="col-7 px-0">
                            <div class="px-4 py-5 chat-box bg-white">

                                @if (isset($chatMessages))
                                    @foreach ($chatMessages as $chat)
                                        @if(!empty($ticketNo))
                                        
                                            @if($chat->is_agent == 0)
                                                <!-- Sender Message-->
                                                <div class="media w-50 mb-3"><img src="{{ asset($chat->user->dp) }}" alt="user" width="50" class="rounded-circle">
                                                    <div class="media-body ml-3">
                                                        <h6 class="text-primary">
                                                            @if (empty(trim($chat->user->first_name)))
                                                            {{  $chat->user->username }}
                                                        @else
                                                            {{ $chat->user->first_name }}
                                                        @endif
                                                        </h6>
                                                    <div class="bg-light rounded py-2 px-3 mb-2">
                                                        
                                                        <p class="text-small mb-0 text-muted">{{ $chat->message}}</p>
                                                    </div>
                                                    <p class="small text-muted">{{ $list->created_at->format('h:ia | d M') }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                        
                                            @if($chat->is_agent == 1)
                                                <!-- Reciever Message-->
                                                <div class="media w-50 ml-auto mb-3">
                                                    <div class="media-body">
                                                    <div class="bg-primary rounded py-2 px-3 mb-2">
                                                        <p class="text-small mb-0 text-white">{{ $chat->message}}</p>
                                                    </div>
                                                    <p class="small text-muted">{{ $list->created_at->format('h:ia | d M') }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    @endforeach
                                    <div id="last"></div>
                                @endif
                              
                            </div>
                      
                            <!-- Typing area -->
                            <form action="{{ route('customerHappiness.chat') }}" method="POST" class="bg-light">
                                @csrf
                                <div class="input-group">
                                    <input type="hidden" name="ticketNo" value="{{ $ticketNo }}">
                                    <input type="text" name="message" placeholder="Type a message"
                                        aria-describedby="button-addon2"
                                        class="form-control rounded-0 border-0 py-4 bg-light"
                                        {{ empty($ticketNo) ? 'disabled':'' }}>
                                    <div class="input-group-append">
                                        <button id="button-addon2" type="submit" class="btn btn-link" {{ empty($ticketNo) ? 'disabled':'' }}>
                                            <i class="{{ empty($ticketNo) ? 'bi bi-send-exclamation-fill text-danger':'bi bi-send-fill text-success' }} "></i></button>
                                    </div>
                                </div>
                            </form>
                      
                          </div>
                        </div>
                      </div>
                </div>
            </div>


            


        </div>

    </div>

</div>

@endsection

