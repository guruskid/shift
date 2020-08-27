@extends('layouts.chat_layout')
@section('content')

<div id="app">
        <message-alert-component></message-alert-component>
    <div class="ca-main-conatiner">
        <div class="ca-main-wrapper">
            <div class="ca-sidebar-wrapper">
                <div class="ca-sidebar">
                    <div class="ca-sidebar__header">
                        <div class="ca-userprofile" data-toggle="modal" data-target="#viewProfileModal">
                            <a href="javascript:;" class="user-avatar user-avatar-rounded">
                                <img src="{{asset('storage/avatar/'.Auth::user()->dp)}} " alt="">
                            </a>
                        </div>
                        <div class="iconbox-group">

                            <div class="iconbox iconbox-search btn-hovered-light">
                                <i class="iconbox__icon mdi mdi-magnify"></i>
                            </div>

                            <a href=" {{route('admin.dashboard')}} ">
                                <div class="iconbox btn-hovered-light">
                                    <i class="iconbox__icon mdi mdi-home"></i>
                                </div>
                            </a>
                        </div>

                        <div class="iconbox-searchbar">
                            <form>
                                <input type="text" class="form-control" id="userSearch" placeholder="Search here..."
                                    autofocus>

                                <button class="search-submit" type="submit">
                                    <i class="mdi mdi-magnify"></i>
                                </button>
                                <a href="javascript:void(0)" class="search-close">
                                    <i class="mdi mdi-arrow-left"></i>
                                </a>
                            </form>
                        </div>

                    </div>

                    <div class="ca-sidebar__body">
                        <div class="ca-navigation-tabs">
                            <div class="nav-style-1">
                                <ul class="nav" id="caMainTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="caChatsTab" data-toggle="pill" href="#caChats"
                                            role="tab" aria-controls="caChats" aria-selected="true">
                                            <span class="mdi mdi-account-supervisor-outline"></span>
                                            <span>Chat</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " id="caChatsTab2" data-toggle="pill" href="#caChats2"
                                            role="tab" aria-controls="caChats" aria-selected="false">
                                            <span class="mdi mdi-account-supervisor-outline"></span>
                                            <span>Online Users</span>
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content" id="caMainTabContent">
                                    <users-component :inboxes=" {{$inboxes}} " ></users-component>

                                    <online-users-component></online-users-component>


                                    <div class="tab-pane fade position-relative" id="caContacts" role="tabpanel"
                                        aria-labelledby="caContactsTab">
                                        <div class="sidebar-contactlist">
                                            <ul class="list-unstyled userSearchList">
                                                <li>
                                                    <div class="contactlist active">
                                                        <div class="user-avatar user-avatar-rounded online">
                                                            <img src="{{asset('chat/assets/images/user/250/01.jpg')}} "
                                                                alt="">
                                                        </div>
                                                        <div class="contactlist__details">
                                                            <div class="contactlist__details--name">Jack P. Angulo</div>
                                                            <div class="calllist__details--info">
                                                                <span><i class="mdi mdi-tag-outline"></i></span>
                                                                <span>Co-Workers</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="ca-content-wrapper">
                <div class="ca-content">
                    <div class="ca-content__chatstab">
                        <div class="ca-content__chatstab--personal">
                            <div class="conversation-wrapper">
                                <div class="conversation-panel">
                                    <chat-header-component :rec-id=" {{$user->id}}"></chat-header-component>

                                    <chat-messages-component :rec-id=" {{$user->id}}"></chat-messages-component>

                                    <chat-form-component :rec-id=" {{$user->id}}"></chat-form-component>
                                </div>

                                <user-details-component :rec-id=" {{$user->id}}"></user-details-component>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="new-msgcall-data">
                <!-- NEW MESSAGE MODAL -->
                <div class="modal new-message-dialog" id="newMsgModal" tabindex="-1" role="dialog"
                    aria-labelledby="newMsgModal" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title" id="newMsgModalLabel">Create New Message</h6>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="searchbar">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search"
                                            aria-label="Search">
                                    </div>
                                </div>
                                <div class="modal-contact-list">
                                    <ul class="list-unstyled">
                                        <li>
                                            <div class="contactlist">
                                                <div class="user-avatar user-avatar-rounded">
                                                    <img src="{{asset('chat/assets/images/user/500/06.jpg')}} " alt="">
                                                </div>
                                                <div class="contactlist__details">
                                                    <div class="contactlist__details--name">Chuck McCann</div>
                                                    <div class="calllist__details--info">
                                                        <span><i class="mdi mdi-tag-outline"></i></span>
                                                        <span>Friends</span>
                                                    </div>
                                                </div>

                                                <div class="contactlist__actions">
                                                    <div class="iconbox btn-solid-primary">
                                                        <i class="iconbox__icon mdi mdi-message-text-outline"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Transactions modal -->
            <user-transactions-component :rec-id=" {{$user->id}}"></user-transactions-component>


            <div class="backdrop-overlay hidden"></div>
        </div>
    </div>
</div>
@endsection