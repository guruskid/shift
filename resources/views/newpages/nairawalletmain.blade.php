@extends('layouts.user')
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

            <div id="content" class="main-content">
                <div class="layout-px-spacing">
                    <div class="row layout-top-spacing"></div>
                    <div class="row layout-top-spacing">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                            <div class="widget widget-chart-one">
                                <div class="widget-heading">
                                    <div>
                                        <span class="h3 giftcard-text" style="color: #000070;">Naira Wallet (₦) </span>
                                    </div>
                                    <div class="widget-n" style="justify-content: center; text-align: center;">
                                        <span class="d-block" style="h6 walletbalance-text">Wallet Balance</span>
                                        <span class="d-block price">₦20,000</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card card-body mb-4" style="height:550px;">
                                {{-- <div class="container px-4 d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div
                                            style="background: rgba(0, 0, 112, 0.25);width:24px;height:24px;border-radius:12px;">
                                            <span style="position: relative;left:33%;top:0;">
                                                <svg width="8" height="12" viewBox="0 0 8 12" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M7.41 1.41L6 0L0 6L6 12L7.41 10.59L2.83 6L7.41 1.41Z"
                                                        fill="#000070" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="ml-2" style="color: #000070;font-size: 20px;">Back</div>
                                    </div>
                                    <div class="d-flex">
                                        <div class="mr-3 mr-lg-4" style="color: #0D1F3C;font-size: 30px;">$ 8,452.98
                                        </div>
                                        <div>
                                            <span class="d-block">
                                                <svg width="90" height="30" viewBox="0 0 123 35" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M2.49294 1.25609C2.15141 0.914637 1.59768 0.914637 1.25615 1.25609C0.914617 1.59754 0.914617 2.15115 1.25615 2.4926C1.98719 3.22348 2.72393 3.95277 3.46157 4.68295C10.7683 11.9158 18.1624 19.2351 20.9613 29.0292C20.9915 29.117 21.11 29.3235 21.2085 29.4312C21.3678 29.5481 21.7335 29.6611 21.9209 29.6557C22.0485 29.6285 22.2432 29.5448 22.312 29.5003C22.3542 29.4681 22.4203 29.4087 22.4453 29.3827C22.5194 29.3026 22.5629 29.2233 22.5665 29.2166C22.5885 29.1778 22.605 29.142 22.6102 29.1304C22.6253 29.0975 22.6419 29.0579 22.657 29.0211C22.689 28.9434 22.7331 28.8323 22.7865 28.6956C22.8939 28.4204 23.0458 28.0241 23.2278 27.5439C23.5922 26.5828 24.0806 25.2781 24.5829 23.9187C25.5784 21.2246 26.6554 18.2442 26.9036 17.3537C27.0952 16.6666 27.2509 15.775 27.4078 14.8615L27.435 14.703C27.5868 13.8174 27.7455 12.892 27.9466 12.0074C28.1606 11.0657 28.4128 10.2168 28.7301 9.55027C29.0542 8.86948 29.3924 8.49359 29.7122 8.3337C29.7467 8.3165 29.7794 8.29734 29.8105 8.27641C30.0701 8.51875 30.3254 8.75508 30.5734 8.98466C30.9752 9.35657 31.358 9.71088 31.7083 10.0441C32.6057 10.8978 33.3716 11.687 34.0012 12.5318C35.2292 14.1795 35.9887 16.1037 35.9887 19.2185C35.9887 19.5309 36.1049 19.9361 36.2226 20.2911C36.3547 20.6897 36.5415 21.1734 36.7675 21.7043C37.22 22.7673 37.8457 24.0577 38.5471 25.298C39.2445 26.5311 40.0373 27.7513 40.8292 28.6506C41.2237 29.0986 41.6468 29.5001 42.0862 29.7803C42.516 30.0544 43.0645 30.2766 43.6614 30.1772C44.9308 29.9657 45.8062 29.1606 46.4318 28.199C47.0463 27.2547 47.4765 26.0751 47.844 24.9429C47.9577 24.5926 48.0652 24.2482 48.1703 23.911C48.4182 23.1161 48.6536 22.3614 48.9295 21.6643C49.3241 20.6675 49.7367 19.9722 50.2158 19.5891C50.801 19.121 51.0594 18.4581 51.2141 17.9292C51.2834 17.6924 51.3409 17.4474 51.3918 17.2308L51.4128 17.1415C51.4718 16.8919 51.524 16.682 51.5863 16.4959C51.7144 16.1135 51.8343 15.9909 51.9349 15.9382C52.0365 15.885 52.3122 15.8028 52.9671 15.9628C53.0265 16.0866 53.0974 16.2818 53.173 16.5553C53.3349 17.1413 53.4783 17.9368 53.5992 18.7872C53.8397 20.4799 53.9739 22.2677 54.0018 22.7425C54.03 23.2221 54.162 23.766 54.5956 24.1303C55.0551 24.5162 55.6057 24.4997 55.9976 24.4101C56.3917 24.32 56.7896 24.1238 57.1507 23.9103C57.5219 23.6908 57.9088 23.4197 58.2875 23.1368C58.6674 22.8529 59.0536 22.5462 59.4211 22.2528L59.5344 22.1623C59.8637 21.8992 60.1762 21.6497 60.4668 21.4284C60.7936 21.1797 61.0691 20.985 61.2894 20.8556C61.4291 20.7734 61.5065 20.7422 61.5343 20.731C61.538 20.7295 61.5428 20.7275 61.5428 20.7275C61.9474 20.7397 62.3955 20.924 62.8868 21.3213C63.3951 21.7325 63.8931 22.3279 64.3555 23.0323C65.2812 24.4423 65.9718 26.1459 66.285 27.2419C67.0255 29.8328 68.1839 32.0167 70.0755 33.163C72.0197 34.3411 74.4841 34.2797 77.4713 32.9524C77.7207 32.8415 77.9247 32.6473 78.0598 32.5066C78.2157 32.3445 78.3803 32.1447 78.5479 31.9256C78.8842 31.486 79.2783 30.9082 79.7039 30.2594C80.1294 29.6107 80.5972 28.8741 81.0832 28.109L81.0896 28.099C81.5792 27.3281 82.0884 26.5266 82.5994 25.7433C83.6285 24.1657 84.6368 22.7064 85.4756 21.7485C85.8753 21.292 86.1879 21.0068 86.4059 20.866C87.0982 21.5885 87.7711 22.4593 88.5053 23.4095C88.6156 23.5523 88.7272 23.6968 88.8406 23.8429C89.7546 25.0211 90.7607 26.2779 91.9369 27.3362C92.6406 27.9695 93.6134 28.2892 94.595 28.4574C95.5934 28.6284 96.7132 28.6607 97.8115 28.6344C98.9133 28.6081 100.03 28.5216 101.02 28.4437L101.067 28.44C102.055 28.3623 102.89 28.2966 103.511 28.2966C104.494 28.2966 105.438 28.7052 106.488 29.3496C106.923 29.6169 107.353 29.9096 107.806 30.2175L108.085 30.4076C108.633 30.7785 109.212 31.1624 109.817 31.4984C111.706 32.5478 113.777 32.7246 115.635 32.8693L115.758 32.8789C117.63 33.0244 119.275 33.1523 120.734 33.8816C121.166 34.0976 121.691 33.9225 121.907 33.4906C122.123 33.0587 121.948 32.5335 121.516 32.3176C119.744 31.4318 117.789 31.2813 116.019 31.1451L115.771 31.1259C113.874 30.9781 112.176 30.8085 110.666 29.9697C110.134 29.6743 109.61 29.3285 109.067 28.9601L108.8 28.7785C108.347 28.4703 107.876 28.1503 107.403 27.8596C106.27 27.1638 104.986 26.5479 103.511 26.5479C102.82 26.5479 101.926 26.6183 100.985 26.6923L100.883 26.7004C99.8872 26.7787 98.8168 26.8612 97.7697 26.8862C96.7191 26.9113 95.7277 26.8773 94.8905 26.7338C94.0366 26.5875 93.4494 26.3446 93.107 26.0364C92.0588 25.0933 91.1364 23.949 90.2227 22.7711C90.1127 22.6294 90.0026 22.4868 89.8922 22.3438C89.1046 21.3237 88.3039 20.2865 87.4617 19.4445C87.1809 19.1638 86.8184 19.0311 86.4407 19.0469C86.101 19.0611 85.8002 19.192 85.5637 19.3312C85.0938 19.6078 84.6152 20.0763 84.1595 20.5967C83.2304 21.6577 82.1626 23.2118 81.1343 24.788C80.6166 25.5816 80.1018 26.3921 79.6131 27.1615L79.6111 27.1646C79.1217 27.9351 78.6597 28.6626 78.2412 29.3005C77.8205 29.9419 77.4548 30.4761 77.1586 30.8632C77.01 31.0574 76.8902 31.1997 76.7986 31.295C76.7575 31.3379 76.7305 31.3621 76.7169 31.3739C74.0421 32.5524 72.2445 32.4325 70.9822 31.6676C69.6604 30.8666 68.6669 29.2113 67.9668 26.7615C67.6135 25.5252 66.856 23.6542 65.8177 22.0727C65.2981 21.2813 64.6852 20.5267 63.9869 19.9619C63.2889 19.3973 62.449 18.9782 61.4999 18.9782C61.077 18.9782 60.6714 19.1903 60.4028 19.3481C60.0907 19.5316 59.7488 19.7771 59.4072 20.0371C59.0988 20.2719 58.7702 20.5344 58.4434 20.7955L58.3297 20.8863C57.9614 21.1803 57.5953 21.4709 57.2405 21.736C56.8844 22.002 56.5542 22.2313 56.2603 22.4051C56.0471 22.5312 55.8775 22.6131 55.7493 22.6613L55.7479 22.6398C55.7181 22.1341 55.5801 20.2958 55.3309 18.5413C55.2068 17.6682 55.051 16.7848 54.8589 16.0896C54.764 15.746 54.6498 15.4095 54.5077 15.1331C54.4364 14.9945 54.3439 14.8427 54.2221 14.7072C54.1032 14.5747 53.9138 14.4107 53.6429 14.3333C52.6647 14.0539 51.8144 14.0269 51.1227 14.3894C50.4206 14.7575 50.1052 15.4106 49.9277 15.9408C49.8372 16.211 49.7691 16.4916 49.7106 16.7396L49.6886 16.8328C49.6365 17.0542 49.5904 17.25 49.5353 17.4383C49.4067 17.8781 49.2741 18.1028 49.1231 18.2236C48.2708 18.9053 47.7212 19.9648 47.3031 21.0208C47.0018 21.782 46.7353 22.6366 46.479 23.4582C46.3784 23.7806 46.2792 24.0986 46.1804 24.4031C45.8152 25.5282 45.4429 26.512 44.9657 27.2455C44.4996 27.9617 43.9941 28.3489 43.3739 28.4523C43.3739 28.4523 43.2719 28.4623 43.0266 28.3059C42.7805 28.149 42.4816 27.8806 42.142 27.495C41.4657 26.7271 40.7419 25.6259 40.0697 24.4373C39.4016 23.2559 38.8047 22.0244 38.377 21.0196C38.1629 20.5166 37.9952 20.08 37.8829 19.741C37.7756 19.4175 37.746 19.2606 37.7395 19.2257C37.7388 19.2221 37.7381 19.2188 37.7381 19.2188C37.7381 15.7562 36.8733 13.4588 35.4038 11.487C34.6846 10.522 33.8334 9.65185 32.914 8.77727C32.5208 8.40321 32.1273 8.03935 31.7243 7.66666C31.1544 7.13958 30.5651 6.59459 29.9305 5.9783C29.5841 5.64183 29.0304 5.64985 28.6938 5.99621C28.4259 6.27195 28.3764 6.67898 28.5421 7.00328C27.9207 7.44557 27.4785 8.11033 27.1508 8.79873C26.7518 9.63685 26.4657 10.6312 26.241 11.6199C26.0291 12.5521 25.863 13.5207 25.7129 14.3964L25.6839 14.5657C25.5227 15.5046 25.3815 16.3005 25.2188 16.8843C24.9952 17.6864 23.953 20.577 22.9422 23.3127C22.5602 24.3465 22.1865 25.348 21.8699 26.1891C18.5041 17.0944 11.4079 10.0784 4.72053 3.46663C3.97094 2.72551 3.22643 1.98941 2.49294 1.25609Z"
                                                        fill="#000070" />
                                                    <path
                                                        d="M55.5459 22.7156L55.5495 22.7155M1.25615 1.25609C1.59768 0.914637 2.15141 0.914637 2.49294 1.25609C3.22643 1.98941 3.97094 2.72551 4.72053 3.46663C11.4079 10.0784 18.5041 17.0944 21.8699 26.1891C22.1865 25.348 22.5602 24.3465 22.9422 23.3127C23.953 20.577 24.9952 17.6864 25.2188 16.8843C25.3815 16.3005 25.5227 15.5046 25.6839 14.5657L25.7129 14.3964C25.863 13.5207 26.0291 12.5521 26.241 11.6199C26.4657 10.6312 26.7518 9.63685 27.1508 8.79873C27.4785 8.11033 27.9207 7.44557 28.5421 7.00328C28.3764 6.67898 28.4259 6.27195 28.6938 5.99621C29.0304 5.64985 29.5841 5.64183 29.9305 5.9783C30.5651 6.59459 31.1544 7.13958 31.7243 7.66666C32.1273 8.03935 32.5208 8.40321 32.914 8.77727C33.8334 9.65185 34.6846 10.522 35.4038 11.487C36.8733 13.4588 37.7381 15.7562 37.7381 19.2188C37.7381 19.2188 37.7388 19.2221 37.7395 19.2257C37.746 19.2606 37.7756 19.4175 37.8829 19.741C37.9952 20.08 38.1629 20.5166 38.377 21.0196C38.8047 22.0244 39.4016 23.2559 40.0697 24.4373C40.7419 25.6259 41.4657 26.7271 42.142 27.495C42.4816 27.8806 42.7805 28.149 43.0266 28.3059C43.2719 28.4623 43.3739 28.4523 43.3739 28.4523C43.9941 28.3489 44.4996 27.9617 44.9657 27.2455C45.4429 26.512 45.8152 25.5282 46.1804 24.4031C46.2792 24.0986 46.3784 23.7806 46.479 23.4582C46.7353 22.6366 47.0018 21.782 47.3031 21.0208C47.7212 19.9648 48.2708 18.9053 49.1231 18.2236C49.2741 18.1028 49.4067 17.8781 49.5353 17.4383C49.5904 17.25 49.6365 17.0542 49.6886 16.8328L49.7106 16.7396C49.7692 16.4916 49.8372 16.211 49.9277 15.9408C50.1052 15.4106 50.4206 14.7575 51.1227 14.3894C51.8144 14.0269 52.6647 14.0539 53.6429 14.3333C53.9139 14.4107 54.1032 14.5747 54.2221 14.7072C54.3439 14.8427 54.4364 14.9945 54.5077 15.1331C54.6498 15.4095 54.764 15.746 54.8589 16.0896C55.051 16.7848 55.2068 17.6682 55.3309 18.5413C55.5801 20.2958 55.7181 22.1341 55.7479 22.6398L55.7493 22.6613C55.8775 22.6131 56.0471 22.5312 56.2603 22.4051C56.5542 22.2313 56.8844 22.002 57.2405 21.736C57.5953 21.4709 57.9614 21.1803 58.3297 20.8863L58.4434 20.7955C58.7702 20.5344 59.0988 20.2719 59.4072 20.0371C59.7488 19.7771 60.0907 19.5316 60.4028 19.3481C60.6714 19.1903 61.077 18.9782 61.4999 18.9782C62.449 18.9782 63.2889 19.3973 63.9869 19.9619C64.6852 20.5267 65.2982 21.2813 65.8177 22.0727C66.856 23.6542 67.6135 25.5252 67.9668 26.7615C68.6669 29.2113 69.6604 30.8666 70.9822 31.6676C72.2445 32.4325 74.0421 32.5524 76.7169 31.3739C76.7305 31.3621 76.7575 31.3379 76.7986 31.295C76.8902 31.1997 77.01 31.0574 77.1586 30.8632C77.4548 30.4761 77.8205 29.9419 78.2412 29.3005C78.6597 28.6626 79.1217 27.9351 79.6111 27.1646L79.6131 27.1615C80.1018 26.3921 80.6166 25.5816 81.1343 24.788C82.1626 23.2118 83.2304 21.6577 84.1595 20.5967C84.6152 20.0763 85.0938 19.6078 85.5637 19.3312C85.8002 19.192 86.101 19.0611 86.4407 19.0469C86.8184 19.0311 87.1809 19.1638 87.4617 19.4445C88.3039 20.2865 89.1046 21.3237 89.8922 22.3438C90.0026 22.4868 90.1127 22.6294 90.2227 22.7711C91.1364 23.949 92.0588 25.0933 93.107 26.0364C93.4494 26.3446 94.0366 26.5875 94.8905 26.7338C95.7277 26.8773 96.7191 26.9113 97.7697 26.8862C98.8168 26.8612 99.8872 26.7787 100.883 26.7004L100.985 26.6923C101.926 26.6183 102.82 26.5479 103.511 26.5479C104.986 26.5479 106.27 27.1638 107.403 27.8596C107.876 28.1503 108.347 28.4703 108.8 28.7785L109.067 28.9601C109.61 29.3285 110.134 29.6743 110.666 29.9697C112.176 30.8085 113.874 30.9781 115.771 31.1259L116.019 31.1451C117.789 31.2813 119.744 31.4318 121.516 32.3176C121.948 32.5335 122.123 33.0587 121.907 33.4906C121.691 33.9225 121.166 34.0976 120.734 33.8816C119.275 33.1523 117.63 33.0244 115.758 32.8789L115.635 32.8693C113.777 32.7246 111.706 32.5478 109.817 31.4984C109.212 31.1624 108.633 30.7785 108.085 30.4076L107.806 30.2175C107.353 29.9096 106.923 29.6169 106.488 29.3496C105.438 28.7052 104.494 28.2966 103.511 28.2966C102.89 28.2966 102.055 28.3623 101.067 28.44L101.02 28.4437C100.03 28.5216 98.9133 28.6081 97.8115 28.6344C96.7132 28.6607 95.5934 28.6284 94.595 28.4574C93.6134 28.2892 92.6406 27.9695 91.9369 27.3362C90.7607 26.2779 89.7546 25.0211 88.8406 23.8429C88.7272 23.6968 88.6156 23.5523 88.5053 23.4095C87.7711 22.4593 87.0982 21.5885 86.4059 20.866C86.1879 21.0068 85.8753 21.292 85.4756 21.7485C84.6368 22.7064 83.6285 24.1657 82.5994 25.7433C82.0884 26.5266 81.5792 27.3281 81.0896 28.099L81.0832 28.109C80.5972 28.8741 80.1294 29.6107 79.7039 30.2594C79.2783 30.9082 78.8842 31.486 78.5479 31.9256C78.3803 32.1447 78.2157 32.3445 78.0598 32.5066C77.9247 32.6473 77.7207 32.8415 77.4713 32.9524C74.4841 34.2797 72.0197 34.3411 70.0755 33.163C68.1839 32.0167 67.0255 29.8328 66.285 27.2419C65.9718 26.1459 65.2812 24.4423 64.3555 23.0323C63.8931 22.3279 63.3951 21.7325 62.8868 21.3213C62.3955 20.924 61.9474 20.7397 61.5428 20.7275C61.5428 20.7275 61.538 20.7295 61.5343 20.731C61.5065 20.7422 61.4291 20.7734 61.2894 20.8556C61.0691 20.985 60.7936 21.1797 60.4668 21.4284C60.1762 21.6497 59.8637 21.8992 59.5344 22.1623L59.4211 22.2528C59.0536 22.5462 58.6674 22.8529 58.2875 23.1368C57.9088 23.4197 57.5219 23.6908 57.1507 23.9103C56.7896 24.1238 56.3917 24.32 55.9976 24.4101C55.6057 24.4997 55.0551 24.5162 54.5956 24.1303C54.162 23.766 54.03 23.2221 54.0018 22.7425C53.9739 22.2677 53.8397 20.4799 53.5992 18.7872C53.4783 17.9368 53.3349 17.1413 53.173 16.5553C53.0974 16.2818 53.0265 16.0866 52.9671 15.9628C52.3122 15.8028 52.0365 15.885 51.9349 15.9382C51.8343 15.9909 51.7144 16.1135 51.5863 16.4959C51.524 16.682 51.4718 16.8919 51.4128 17.1415L51.3918 17.2308C51.3409 17.4474 51.2834 17.6924 51.2141 17.9292C51.0594 18.4581 50.801 19.121 50.2158 19.5891C49.7367 19.9722 49.3241 20.6675 48.9295 21.6643C48.6536 22.3614 48.4182 23.1161 48.1703 23.911C48.0652 24.2482 47.9577 24.5926 47.844 24.9429C47.4765 26.0751 47.0463 27.2547 46.4318 28.199C45.8062 29.1606 44.9308 29.9657 43.6614 30.1772C43.0645 30.2766 42.516 30.0544 42.0862 29.7803C41.6468 29.5001 41.2237 29.0986 40.8292 28.6506C40.0373 27.7513 39.2445 26.5311 38.5471 25.298C37.8457 24.0577 37.22 22.7673 36.7675 21.7043C36.5415 21.1734 36.3547 20.6897 36.2226 20.2911C36.1049 19.9361 35.9887 19.5309 35.9887 19.2185C35.9887 16.1037 35.2292 14.1795 34.0012 12.5318C33.3716 11.687 32.6057 10.8978 31.7083 10.0441C31.358 9.71088 30.9752 9.35657 30.5734 8.98466C30.3254 8.75508 30.0701 8.51875 29.8105 8.27641C29.7794 8.29734 29.7467 8.3165 29.7122 8.3337C29.3924 8.49359 29.0542 8.86948 28.7301 9.55027C28.4128 10.2168 28.1606 11.0657 27.9466 12.0074C27.7455 12.892 27.5868 13.8174 27.435 14.703L27.4078 14.8615C27.2509 15.775 27.0952 16.6666 26.9036 17.3537C26.6554 18.2442 25.5784 21.2246 24.5829 23.9187C24.0806 25.2781 23.5922 26.5828 23.2278 27.5439C23.0458 28.0241 22.8939 28.4204 22.7865 28.6956C22.7331 28.8323 22.689 28.9434 22.657 29.0211C22.6419 29.0579 22.6253 29.0975 22.6102 29.1304C22.605 29.142 22.5885 29.1778 22.5665 29.2166C22.5629 29.2233 22.5194 29.3026 22.4453 29.3827C22.4203 29.4087 22.3542 29.4681 22.312 29.5003C22.2432 29.5448 22.0484 29.6285 21.9209 29.6557C21.7335 29.6611 21.3678 29.5481 21.2085 29.4312C21.11 29.3235 20.9915 29.117 20.9613 29.0292C18.1624 19.2351 10.7683 11.9158 3.46157 4.68295C2.72393 3.95277 1.98719 3.22348 1.25615 2.4926C0.914617 2.15115 0.914617 1.59754 1.25615 1.25609Z"
                                                        stroke="#000070" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            </span>
                                            <span class="d-block mt-1"
                                                style="margin-left:35%;color: #000070;font-size: 16px;">+5.24%</span>
                                        </div>
                                    </div>
                                </div> --}}
                                {{-- border line --}}
                                {{-- <div class="mt-4" style="width: 100%;border: 1px solid #C9CED6;"></div> --}}

                                {{-- Bitcoin  menu  --}}
                                <div class="walletpage__menu-container mx-auto mt-4">
                                    <div class="walletpage_menu d-flex flex-column flex-lg-row justify-content-center justify-content-lg-between align-items-center flex-wrap flex-lg-nowrap">
                                        <div>
                                            <span class="d-block" style="color: #565656;font-size: 16px;">Bitcoin wallet
                                                Balance</span>
                                            <span class="d-block">
                                                <span style="color: #000070;font-size: 30px;">0.8934</span>
                                                <span style="color: #000070;font-size: 30px;">BTC</span>
                                            </span>
                                            <span class="d-block"
                                                style="color: #565656;font-size: 16px;opacity: 0.5;">₦20,000</span>
                                        </div>
                                        <div class="d-flex mt-3 mt-md-0">
                                            <a href="#" class="btn walletpage_menu-active">
                                                <span class="d-block">
                                                    <svg width="40" height="40" viewBox="0 0 44 44" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M18.333 25.6667L38.4997 5.5" stroke="#2C3E50"
                                                            stroke-width="1.83333" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M38.5004 5.5L26.5837 38.5C26.5033 38.6755 26.3741 38.8243 26.2116 38.9285C26.0491 39.0328 25.8601 39.0883 25.667 39.0883C25.474 39.0883 25.2849 39.0328 25.1224 38.9285C24.96 38.8243 24.8308 38.6755 24.7504 38.5L18.3337 25.6667L5.50037 19.25C5.32485 19.1696 5.1761 19.0404 5.07182 18.8779C4.96754 18.7154 4.91211 18.5264 4.91211 18.3333C4.91211 18.1403 4.96754 17.9512 5.07182 17.7887C5.1761 17.6262 5.32485 17.4971 5.50037 17.4167L38.5004 5.5Z"
                                                            stroke="#2C3E50" stroke-width="1.83333"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <span class="d-block"
                                                    style="color: #000000;font-size: 14px;">Send</span>
                                            </a>
                                            <a href="#" class="btn ">
                                                <span class="d-block">
                                                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M28.0144 28.6527C27.7539 28.3922 27.3316 28.3922 27.0711 28.6527L20.6671 35.0573V16.6667C20.6671 16.2985 20.3686 16 20.0004 16C19.6322 16 19.3337 16.2985 19.3337 16.6667V35.0573L12.9297 28.6527C12.6692 28.3922 12.2469 28.3922 11.9864 28.6527C11.7259 28.9132 11.7259 29.3355 11.9864 29.596L19.5284 37.1373C19.7884 37.398 20.2106 37.3986 20.4712 37.1385C20.4716 37.1381 20.4721 37.1377 20.4724 37.1373L28.0144 29.596C28.2749 29.3355 28.2749 28.9132 28.0144 28.6527Z" fill="#2C3E50"/>
                                                        <path d="M38 2.66699H2C0.895417 2.66699 0 3.56241 0 4.66699V28.667C0 29.7716 0.895417 30.667 2 30.667H8.66667C9.03483 30.667 9.33333 30.3685 9.33333 30.0003C9.33333 29.6322 9.03483 29.3337 8.66667 29.3337H2C1.63183 29.3337 1.33333 29.0352 1.33333 28.667V10.667H38.6667V28.667C38.6667 29.0352 38.3682 29.3337 38 29.3337H31.3333C30.9652 29.3337 30.6667 29.6322 30.6667 30.0003C30.6667 30.3685 30.9652 30.667 31.3333 30.667H38C39.1046 30.667 40 29.7716 40 28.667V4.66699C40 3.56241 39.1046 2.66699 38 2.66699ZM38.6667 8.00033H1.33333V4.66699C1.33333 4.29883 1.63183 4.00033 2 4.00033H38C38.3682 4.00033 38.6667 4.29883 38.6667 4.66699V8.00033Z" fill="#2C3E50"/>
                                                        <path d="M19.3333 12H4.66667C4.2985 12 4 12.2985 4 12.6667C4 13.0348 4.2985 13.3333 4.66667 13.3333H19.3333C19.7015 13.3333 20 13.0348 20 12.6667C20 12.2985 19.7015 12 19.3333 12Z" fill="#2C3E50"/>
                                                        </svg>                                                        
                                                </span>
                                                <span class="d-block"
                                                    style="color: #000000;font-size: 14px;">Withdraw</span>
                                            </a>
                                            <a href="#" class="btn">
                                                <span class="d-block">
                                                    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M11.9876 24.6803C12.2481 24.9408 12.6704 24.9408 12.9309 24.6803L19.3349 18.2757V36.6663C19.3349 37.0345 19.6334 37.333 20.0016 37.333C20.3697 37.333 20.6682 37.0345 20.6682 36.6663L20.6682 18.2757L27.0722 24.6803C27.3327 24.9408 27.7551 24.9408 28.0156 24.6803C28.2761 24.4198 28.2761 23.9975 28.0156 23.737L20.4736 16.1957C20.2136 15.935 19.7914 15.9344 19.5307 16.1945C19.5303 16.1949 19.5299 16.1953 19.5296 16.1957L11.9876 23.737C11.7271 23.9975 11.7271 24.4198 11.9876 24.6803Z" fill="#2C3E50"/>
                                                        <path d="M38 2.66699H2C0.895417 2.66699 0 3.56241 0 4.66699L0 28.667C0 29.7716 0.895417 30.667 2 30.667H8.66667C9.03483 30.667 9.33333 30.3685 9.33333 30.0003C9.33333 29.6322 9.03483 29.3337 8.66667 29.3337H2C1.63183 29.3337 1.33333 29.0352 1.33333 28.667V10.667H38.6667V28.667C38.6667 29.0352 38.3682 29.3337 38 29.3337H31.3333C30.9652 29.3337 30.6667 29.6322 30.6667 30.0003C30.6667 30.3685 30.9652 30.667 31.3333 30.667H38C39.1046 30.667 40 29.7716 40 28.667V4.66699C40 3.56241 39.1046 2.66699 38 2.66699ZM38.6667 8.00033H1.33333V4.66699C1.33333 4.29883 1.63183 4.00033 2 4.00033H38C38.3682 4.00033 38.6667 4.29883 38.6667 4.66699V8.00033Z" fill="#2C3E50"/>
                                                        <path d="M19.3333 12H4.66667C4.2985 12 4 12.2985 4 12.6667C4 13.0348 4.2985 13.3333 4.66667 13.3333H19.3333C19.7015 13.3333 20 13.0348 20 12.6667C20 12.2985 19.7015 12 19.3333 12Z" fill="#2C3E50"/>
                                                        </svg>                                                        
                                                </span>
                                                <span class="d-block" style="color: #000000;font-size: 14px;">Deposit</span>
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex flex-column justify-content-center align-items-center mt-5 mb-2 wallettransfertype">
                                        <a href="#" class="d-block nairawallet_trx_type-card mx-auto py-1 py-lg-3 pl-2 px-lg-4 mt-4">
                                            <span class="d-block nairawallet_trx_type_title">Dantown to Dantown</span>
                                            <span class="d-block nairawallet_trx_type_desc">Send to a dantown account</span>
                                        </a>
                                        <a href="#" class="d-block nairawallet_trx_type-card mx-auto py-1 py-lg-3 pl-2 px-lg-4 mt-4">
                                            <span class="d-block nairawallet_trx_type_title">Dantown to other account</span>
                                            <span class="d-block nairawallet_trx_type_desc">Send from a dantown account to another account</span>
                                        </a>
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