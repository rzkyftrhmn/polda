@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="row  main-card">
                <div class="swiper mySwiper-counter position-relative overflow-hidden">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="card card-box bg-secondary">
                                <div class="card-header border-0 pb-0">
                                    <div  class="chart-num">
                                        <p>
                                            <i class="fa-solid fa-sort-down me-2"></i>
                                            4%(30 days)
                                        </p>
                                        <h2 class="font-w600 mb-0">$65,123</h2>
                                    </div>
                                    <div class="dlab-swiper-circle">
                                        <svg width="50" height="45" viewBox="0 0 137 137" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M68.5 0C30.6686 0 0 30.6686 0 68.5C0 106.331 30.6686 137 68.5 137C106.331 137 137 106.331 137 68.5C136.958 30.6865 106.313 0.0418093 68.5 0ZM40.213 63.6068H59.7843C62.4869 63.6068 64.6774 65.7973 64.6774 68.5C64.6774 71.2027 62.4869 73.3932 59.7843 73.3932H40.213C37.5104 73.3932 35.3199 71.2027 35.3199 68.5C35.3199 65.7973 37.5119 63.6068 40.213 63.6068ZM101.393 56.6456L95.5088 86.0883C94.1231 92.9226 88.122 97.8411 81.1488 97.8576H40.213C37.5104 97.8576 35.3199 95.6671 35.3199 92.9644C35.3199 90.2617 37.5119 88.0712 40.213 88.0712H81.1488C83.4617 88.0652 85.4522 86.4347 85.9121 84.168L91.7982 54.7253C92.3208 52.0973 90.6156 49.544 87.9891 49.0214C87.677 48.9601 87.3605 48.9288 87.0439 48.9288H49.9994C47.2967 48.9288 45.1062 46.7383 45.1062 44.0356C45.1062 41.3329 47.2967 39.1424 49.9994 39.1424H87.0439C95.128 39.1454 101.679 45.699 101.677 53.7831C101.677 54.7433 101.582 55.7019 101.393 56.6456Z" fill="#FFF"/>
                                        </svg>
                                    </div>
                                    
                                </div>
                                <div class="card-body p-0">
                                    <div id="widgetChart1" class="chart-primary"></div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="card card-box bg-dark">
                                <div class="card-header border-0 pb-0">
                                    <div class="chart-num">
                                        <p>
                                            <i class="fa-solid fa-sort-down me-2"></i>
                                            4%(30 days)
                                        </p>
                                        <h2 class="font-w600 mb-0">$66,123</h2>
                                    </div>
                                    <div class="dlab-swiper-circle">
                                        <svg width="50" height="45" viewBox="0 0 137 137" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M68.5 0C30.6686 0 0 30.6686 0 68.5C0 106.331 30.6686 137 68.5 137C106.331 137 137 106.331 137 68.5C136.958 30.6865 106.313 0.0418093 68.5 0ZM40.213 63.6068H59.7843C62.4869 63.6068 64.6774 65.7973 64.6774 68.5C64.6774 71.2027 62.4869 73.3932 59.7843 73.3932H40.213C37.5104 73.3932 35.3199 71.2027 35.3199 68.5C35.3199 65.7973 37.5119 63.6068 40.213 63.6068ZM101.393 56.6456L95.5088 86.0883C94.1231 92.9226 88.122 97.8411 81.1488 97.8576H40.213C37.5104 97.8576 35.3199 95.6671 35.3199 92.9644C35.3199 90.2617 37.5119 88.0712 40.213 88.0712H81.1488C83.4617 88.0652 85.4522 86.4347 85.9121 84.168L91.7982 54.7253C92.3208 52.0973 90.6156 49.544 87.9891 49.0214C87.677 48.9601 87.3605 48.9288 87.0439 48.9288H49.9994C47.2967 48.9288 45.1062 46.7383 45.1062 44.0356C45.1062 41.3329 47.2967 39.1424 49.9994 39.1424H87.0439C95.128 39.1454 101.679 45.699 101.677 53.7831C101.677 54.7433 101.582 55.7019 101.393 56.6456Z" fill="#FFF"/>
                                            </svg>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div id="widgetChart2" class="chart-primary"></div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="card card-box bg-warning">
                                <div class="card-header border-0 pb-0">
                                    <div class="chart-num">
                                        <p>
                                            <i class="fa-solid fa-sort-down me-2"></i>
                                            4%(29 days)
                                        </p>
                                        <h2 class="font-w600 mb-0">$67,123</h2>
                                    </div>
                                    <div class="dlab-swiper-circle">
                                        <svg width="50" height="45" viewBox="0 0 137 137" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M92.9644 53.8221C92.9599 48.4197 88.5804 44.0417 83.1795 44.0357H53.822V63.6069H83.1795C88.5804 63.6024 92.9599 59.2229 92.9644 53.8221Z" fill="#FFF"/>
                                        <path d="M53.822 92.9645H83.1795C88.5834 92.9645 92.9644 88.5835 92.9644 83.1796C92.9644 77.7743 88.5834 73.3933 83.1795 73.3933H53.822V92.9645Z" fill="#FFF"/>
                                        <path d="M68.5001 9.15527e-05C30.6687 9.15527e-05 0.00012207 30.6687 0.00012207 68.5001C0.00012207 106.332 30.6687 137 68.5001 137C106.332 137 137 106.332 137 68.5001C136.957 30.6866 106.314 0.0433939 68.5001 9.15527e-05V9.15527e-05ZM102.751 83.1781C102.737 93.9828 93.9829 102.737 83.1797 102.749V107.643C83.1797 110.345 80.9877 112.536 78.2865 112.536C75.5838 112.536 73.3933 110.345 73.3933 107.643V102.749H63.6084V107.643C63.6084 110.345 61.4164 112.536 58.7153 112.536C56.0126 112.536 53.8221 110.345 53.8221 107.643V102.749H39.144C36.4414 102.749 34.2509 100.559 34.2509 97.8577C34.2509 95.155 36.4414 92.9645 39.144 92.9645H44.0357V44.0357H39.144C36.4414 44.0357 34.2509 41.8452 34.2509 39.1425C34.2509 36.4399 36.4414 34.2493 39.144 34.2493H53.8221V29.3577C53.8221 26.655 56.0126 24.4645 58.7153 24.4645C61.4179 24.4645 63.6084 26.655 63.6084 29.3577V34.2493H73.3933V29.3577C73.3933 26.655 75.5838 24.4645 78.2865 24.4645C80.9891 24.4645 83.1797 26.655 83.1797 29.3577V34.2493C93.9426 34.2045 102.705 42.8919 102.751 53.6548C102.775 59.3543 100.304 64.7791 95.9867 68.5001C100.263 72.1793 102.731 77.5354 102.751 83.1781V83.1781Z" fill="#FFF"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div id="widgetChart3" class="chart-primary"></div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="card card-box bg-pink">
                                <div class="card-header border-0 pb-0">
                                    <div class="chart-num">
                                        <p>
                                            <i class="fa-solid fa-sort-down me-2"></i>
                                            4%(30 days)
                                        </p>
                                        <h2 class="font-w600 mb-0">$68,123</h2>
                                    </div>
                                    <div class="dlab-swiper-circle">
                                        <svg width="50" height="45" viewBox="0 0 137 137" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M70.3615 78.5206C69.1671 78.9977 67.8366 78.9977 66.6421 78.5206L53.8232 73.3927L68.5018 102.75L83.1804 73.3927L70.3615 78.5206Z" fill="#FFF"/>
                                        <path d="M68.4982 68.5L88.0696 61.6503L68.4982 34.25L48.9268 61.6503L68.4982 68.5Z" fill="#FFF"/>
                                        <path d="M68.5 0C30.6686 0 0 30.6686 0 68.5C0 106.331 30.6686 137 68.5 137C106.331 137 137 106.331 137 68.5C136.958 30.6865 106.313 0.0418093 68.5 0V0ZM97.3409 65.7958L72.8765 114.725C71.6685 117.142 68.7285 118.122 66.3125 116.914C65.3643 116.44 64.5968 115.673 64.1235 114.725L39.6591 65.7958C38.899 64.2698 38.9856 62.4586 39.8875 61.0117L64.3519 21.8692C65.978 19.5787 69.151 19.0381 71.4416 20.6642C71.9089 20.9957 72.3166 21.4019 72.6481 21.8692L97.111 61.0117C98.0144 62.4586 98.101 64.2698 97.3409 65.7958V65.7958Z" fill="#FFF"/>
                                        </svg>
                                        </div>
                                </div>
                                <div class="card-body p-0">
                                    <div id="widgetChart4" class="chart-primary"></div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="card card-box bg-secondary">
                                <div class="card-header border-0 pb-0">
                                    <div  class="chart-num">
                                        <p>
                                            <i class="fa-solid fa-sort-down me-2"></i>
                                            4%(31 days)
                                        </p>
                                        <h2 class="font-w600 mb-0">$69,123</h2>
                                    </div>
                                    <div class="dlab-swiper-circle">
                                        <svg width="50" height="45" viewBox="0 0 137 137" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M68.5 0C30.6686 0 0 30.6686 0 68.5C0 106.331 30.6686 137 68.5 137C106.331 137 137 106.331 137 68.5C136.958 30.6865 106.313 0.0418093 68.5 0ZM40.213 63.6068H59.7843C62.4869 63.6068 64.6774 65.7973 64.6774 68.5C64.6774 71.2027 62.4869 73.3932 59.7843 73.3932H40.213C37.5104 73.3932 35.3199 71.2027 35.3199 68.5C35.3199 65.7973 37.5119 63.6068 40.213 63.6068ZM101.393 56.6456L95.5088 86.0883C94.1231 92.9226 88.122 97.8411 81.1488 97.8576H40.213C37.5104 97.8576 35.3199 95.6671 35.3199 92.9644C35.3199 90.2617 37.5119 88.0712 40.213 88.0712H81.1488C83.4617 88.0652 85.4522 86.4347 85.9121 84.168L91.7982 54.7253C92.3208 52.0973 90.6156 49.544 87.9891 49.0214C87.677 48.9601 87.3605 48.9288 87.0439 48.9288H49.9994C47.2967 48.9288 45.1062 46.7383 45.1062 44.0356C45.1062 41.3329 47.2967 39.1424 49.9994 39.1424H87.0439C95.128 39.1454 101.679 45.699 101.677 53.7831C101.677 54.7433 101.582 55.7019 101.393 56.6456Z" fill="#FFF"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div id="widgetChart5" class="chart-primary"></div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="card card-box bg-pink">
                                <div class="card-header border-0 pb-0">
                                    <div class="chart-num">
                                        <p>
                                            <i class="fa-solid fa-sort-down me-2"></i>
                                            4%(32 days)
                                        </p>
                                        <h2 class="font-w600 mb-0">$65,123</h2>
                                    </div>
                                    <div class="dlab-swiper-circle">
                                        <svg width="50" height="45" viewBox="0 0 137 137" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M70.3615 78.5206C69.1671 78.9977 67.8366 78.9977 66.6421 78.5206L53.8232 73.3927L68.5018 102.75L83.1804 73.3927L70.3615 78.5206Z" fill="#FFF"/>
                                        <path d="M68.4982 68.5L88.0696 61.6503L68.4982 34.25L48.9268 61.6503L68.4982 68.5Z" fill="#FFF"/>
                                        <path d="M68.5 0C30.6686 0 0 30.6686 0 68.5C0 106.331 30.6686 137 68.5 137C106.331 137 137 106.331 137 68.5C136.958 30.6865 106.313 0.0418093 68.5 0V0ZM97.3409 65.7958L72.8765 114.725C71.6685 117.142 68.7285 118.122 66.3125 116.914C65.3643 116.44 64.5968 115.673 64.1235 114.725L39.6591 65.7958C38.899 64.2698 38.9856 62.4586 39.8875 61.0117L64.3519 21.8692C65.978 19.5787 69.151 19.0381 71.4416 20.6642C71.9089 20.9957 72.3166 21.4019 72.6481 21.8692L97.111 61.0117C98.0144 62.4586 98.101 64.2698 97.3409 65.7958V65.7958Z" fill="#FFF"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div id="widgetChart6" class="chart-primary"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-6">
                    <div class="card crypto-chart">
                        <div class="card-header pb-0 border-0 flex-wrap">
                            <div class="mb-0">
                                <h4 class="card-title">Crypto Statistics</h4>
                                <p>Lorem ipsum dolor sit amet, consectetur</p>
                            </div>
                            <div class="d-flex mb-2">
                                <div class="form-check form-switch toggle-switch me-3">
                                    <label class="form-check-label" for="flexSwitchCheckChecked1">Date</label>
                                    <input class="form-check-input custome" type="checkbox" id="flexSwitchCheckChecked1" checked="">
                                </div>
                                <div class="form-check form-switch toggle-switch">
                                    <label class="form-check-label" for="flexSwitchCheckChecked2">Value</label>
                                    <input class="form-check-input custome" type="checkbox" id="flexSwitchCheckChecked2" checked="">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-2">
                            <ul class="nav nav-pills">
                                <li class=" nav-item">
                                    <a href="#navpills-1" class="nav-link active" data-bs-toggle="tab" aria-expanded="false">Ripple</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#navpills-2" class="nav-link " data-bs-toggle="tab" aria-expanded="false">Bitcoin</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#navpills-3" class="nav-link" data-bs-toggle="tab" aria-expanded="true">Ethereum</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#navpills-5" class="nav-link" data-bs-toggle="tab" aria-expanded="true">Zcash</a>
                                    </li>
                                <li class="nav-item">
                                    <a href="#navpills-5" class="nav-link" data-bs-toggle="tab" aria-expanded="true">LiteCoin</a>
                                </li>
                            </ul>
                            <div id="marketChart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="card market-chart">
                        <div class="card-header border-0 pb-0 flex-wrap">
                            <div class="mb-0">
                                <h4 class="card-title">Market Overview</h4>
                                <p>Lorem ipsum dolor sit amet, consectetur</p>
                            </div>
                            <a href="javascript:void(0);" class="btn-link text-primary get-report mb-2">
                            <svg class="me-2" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M24 22.5C24 23.3284 23.3284 24 22.5 24H1.5C0.671578 24 0 23.3284 0 22.5C0 21.6716 0.671578 21 1.5 21H22.5C23.3284 21 24 21.6716 24 22.5ZM10.9394 17.7482C11.2323 18.0411 11.6161 18.1875 12 18.1875C12.3838 18.1875 12.7678 18.0411 13.0606 17.7482L18.3752 12.4336C18.961 11.8478 18.961 10.8981 18.3752 10.3123C17.7894 9.72652 16.8397 9.72652 16.2539 10.3123L13.5 13.0662V1.5C13.5 0.671578 12.8284 0 12 0C11.1716 0 10.5 0.671578 10.5 1.5V13.0662L7.74609 10.3123C7.1603 9.72652 6.21056 9.72652 5.62477 10.3123C5.03897 10.8981 5.03897 11.8478 5.62477 12.4336L10.9394 17.7482Z" fill="var(--primary)"></path>
                            </svg>
                            
                            Get Report</a>
                        </div>
                        <div class="card-body pt-2">
                            <div class="d-flex justify-content-between flex-wrap">
                                <div class="d-flex align-items-center mb-2">
                                    <h5 class="me-2 font-w600 m-0"><span class="text-success me-2">BUY</span> $5,673</h5>
                                    <h5 class="ms-2 font-w600 m-0"><span class="text-danger me-2">SELL</span> $5,982</h5>
                                </div>
                                <ul class="nav nav-pills justify-content-between mb-2" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" id="market-week-tab" href="#week" data-bs-toggle="tab" data-bs-target="#market-week">Week</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="market-month-tab" data-bs-toggle="tab" href="#month" data-bs-target="#market-month">month</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="market-year-tab" data-bs-toggle="tab" href="#year" data-bs-target="#market-year">year</a>
                                    </li>
                                </ul>
                            </div>	
                            <div id="marketChart2" class="market-line"></div> 
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card transaction-table">
                    <div class="card-header border-0 flex-wrap pb-0">
                        <div class="mb-2">
                            <h4 class="card-title">Recent Transactions</h4>
                            <p class="mb-sm-3 mb-0">Lorem ipsum dolor sit amet, consectetur</p>
                        </div>
                        <ul class="float-end nav nav-pills mb-2">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="Week-tab" data-bs-toggle="tab" data-bs-target="#Week" type="button" role="tab" aria-controls="month" aria-selected="true">Week</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="month-tab" data-bs-toggle="tab" data-bs-target="#month" type="button" role="tab" aria-controls="month" aria-selected="false">Month</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="year-tab" data-bs-toggle="tab" data-bs-target="#year" type="button" role="tab" aria-controls="year" aria-selected="false">Year</button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-0">
                        <div class="tab-content" id="myTabContent1">
                            <div class="tab-pane fade show active" id="Week" role="tabpanel" aria-labelledby="Week-tab">
                                <div class="table-responsive">
                                    <table class="table table-responsive-md">
                                        <thead>
                                            <tr>
                                                <th>
                                                    #
                                                </th>
                                                <th>Transaction ID</th>
                                                <th>Date</th>
                                                <th>From</th>
                                                <th>To</th>
                                                <th>Coin</th>
                                                <th>Amount</th>
                                                <th class="text-end">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <svg class="arrow svg-main-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <polygon points="0 0 24 0 24 24 0 24"/>
                                                            <rect fill="#fff" opacity="0.3" transform="translate(11.646447, 12.853553) rotate(-315.000000) translate(-11.646447, -12.853553) " x="10.6464466" y="5.85355339" width="2" height="14" rx="1"/>
                                                            <path d="M8.1109127,8.90380592 C7.55862795,8.90380592 7.1109127,8.45609067 7.1109127,7.90380592 C7.1109127,7.35152117 7.55862795,6.90380592 8.1109127,6.90380592 L16.5961941,6.90380592 C17.1315855,6.90380592 17.5719943,7.32548256 17.5952502,7.8603687 L17.9488036,15.9920967 C17.9727933,16.5438602 17.5449482,17.0106003 16.9931847,17.0345901 C16.4414212,17.0585798 15.974681,16.6307346 15.9506913,16.0789711 L15.6387276,8.90380592 L8.1109127,8.90380592 Z" fill="#fff" fill-rule="nonzero"/>
                                                        </g>
                                                    </svg>
                                                </td>
                                                <td>#12415346563475</td>
                                                <td>01 August 2020</td>
                                                <td>Thomas</td>
                                                <td><div class="d-flex align-items-center"><img src="images/avatar/1.jpg" class=" me-2" width="30" alt=""> <span class="w-space-no">Dr. Jackson</span></div></td>
                                                <td><div class="d-flex align-items-center"><img src="images/svg/btc.svg" alt="" class="me-2 img-btc">Bitcoin</div></td>
                                                <td class="text-success font-w600">+$5,553</td>
                                                <td  class="text-end"><div class="badge badge-sm badge-success">COMPLETED</div></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <svg class="arrow style-1 svg-main-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <polygon points="0 0 24 0 24 24 0 24"/>
                                                            <rect fill="#fff" opacity="0.3" transform="translate(11.646447, 12.853553) rotate(-315.000000) translate(-11.646447, -12.853553) " x="10.6464466" y="5.85355339" width="2" height="14" rx="1"/>
                                                            <path d="M8.1109127,8.90380592 C7.55862795,8.90380592 7.1109127,8.45609067 7.1109127,7.90380592 C7.1109127,7.35152117 7.55862795,6.90380592 8.1109127,6.90380592 L16.5961941,6.90380592 C17.1315855,6.90380592 17.5719943,7.32548256 17.5952502,7.8603687 L17.9488036,15.9920967 C17.9727933,16.5438602 17.5449482,17.0106003 16.9931847,17.0345901 C16.4414212,17.0585798 15.974681,16.6307346 15.9506913,16.0789711 L15.6387276,8.90380592 L8.1109127,8.90380592 Z" fill="#fff" fill-rule="nonzero"/>
                                                        </g>
                                                    </svg>
                                                </td>
                                                <td>#12415346563475</td>
                                                <td>01 August 2020</td>
                                                <td>Thomas</td>
                                                <td><div class="d-flex align-items-center"><img src="images/avatar/2.jpg" class=" me-2" width="30" alt=""> <span class="w-space-no">Dr. Jackson</span></div></td>
                                                <td><div class="d-flex align-items-center"><img src="images/svg/btc.svg" alt="" class="me-2 img-btc">Bitcoin</div></td>
                                                <td class="text-success font-w600">+$5,553</td>
                                                <td class="text-end"><div class="badge badge-sm badge-warning">PENDING</div></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <svg class="arrow style-2 svg-main-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <polygon points="0 0 24 0 24 24 0 24"/>
                                                            <rect fill="#fff" opacity="0.3" transform="translate(11.646447, 12.853553) rotate(-315.000000) translate(-11.646447, -12.853553) " x="10.6464466" y="5.85355339" width="2" height="14" rx="1"/>
                                                            <path d="M8.1109127,8.90380592 C7.55862795,8.90380592 7.1109127,8.45609067 7.1109127,7.90380592 C7.1109127,7.35152117 7.55862795,6.90380592 8.1109127,6.90380592 L16.5961941,6.90380592 C17.1315855,6.90380592 17.5719943,7.32548256 17.5952502,7.8603687 L17.9488036,15.9920967 C17.9727933,16.5438602 17.5449482,17.0106003 16.9931847,17.0345901 C16.4414212,17.0585798 15.974681,16.6307346 15.9506913,16.0789711 L15.6387276,8.90380592 L8.1109127,8.90380592 Z" fill="#fff" fill-rule="nonzero"/>
                                                        </g>
                                                    </svg>
                                                </td>
                                                <td>#12415346563475</td>
                                                <td>01 August 2020</td>
                                                <td>Thomas</td>
                                                <td><div class="d-flex align-items-center"><img src="images/avatar/3.jpg" class="me-2" width="30" alt=""> <span class="w-space-no">Dr. Jackson</span></div></td>
                                                <td><div class="d-flex align-items-center"><img src="images/svg/btc.svg" alt="" class="me-2 img-btc">Bitcoin</div></td>
                                                <td class="text-danger font-w600">+$5,553</td>
                                                <td class="text-end"><div class="badge badge-sm badge-danger">CANCEL</div></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade show" id="month" role="tabpanel" aria-labelledby="month-tab">
                                <div class="table-responsive">
                                    <table class="table table-responsive-md">
                                        <thead>
                                            <tr>
                                                <th>
                                                    #
                                                </th>
                                                <th>Transaction ID</th>
                                                <th>Date</th>
                                                <th>From</th>
                                                <th>To</th>
                                                <th>Coin</th>
                                                <th>Amount</th>
                                                <th class="text-end">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <svg class="arrow style-1 svg-main-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <polygon points="0 0 24 0 24 24 0 24"/>
                                                            <rect fill="#fff" opacity="0.3" transform="translate(11.646447, 12.853553) rotate(-315.000000) translate(-11.646447, -12.853553) " x="10.6464466" y="5.85355339" width="2" height="14" rx="1"/>
                                                            <path d="M8.1109127,8.90380592 C7.55862795,8.90380592 7.1109127,8.45609067 7.1109127,7.90380592 C7.1109127,7.35152117 7.55862795,6.90380592 8.1109127,6.90380592 L16.5961941,6.90380592 C17.1315855,6.90380592 17.5719943,7.32548256 17.5952502,7.8603687 L17.9488036,15.9920967 C17.9727933,16.5438602 17.5449482,17.0106003 16.9931847,17.0345901 C16.4414212,17.0585798 15.974681,16.6307346 15.9506913,16.0789711 L15.6387276,8.90380592 L8.1109127,8.90380592 Z" fill="#fff" fill-rule="nonzero"/>
                                                        </g>
                                                    </svg>
                                                </td>
                                                <td>#12415346563475</td>
                                                <td>01 August 2020</td>
                                                <td>Thomas</td>
                                                <td><div class="d-flex align-items-center"><img src="images/avatar/2.jpg" class=" me-2" width="24" alt=""> <span class="w-space-no">Dr. Jackson</span></div></td>
                                                <td><div class="d-flex align-items-center"><img src="images/svg/btc.svg" alt="" class="me-2 img-btc">Bitcoin</div></td>
                                                <td>+$5,553</td>
                                                <td class="text-end"><div class="badge badge-sm badge-warning">PENDING</div></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <svg class="arrow svg-main-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <polygon points="0 0 24 0 24 24 0 24"/>
                                                            <rect fill="#fff" opacity="0.3" transform="translate(11.646447, 12.853553) rotate(-315.000000) translate(-11.646447, -12.853553) " x="10.6464466" y="5.85355339" width="2" height="14" rx="1"/>
                                                            <path d="M8.1109127,8.90380592 C7.55862795,8.90380592 7.1109127,8.45609067 7.1109127,7.90380592 C7.1109127,7.35152117 7.55862795,6.90380592 8.1109127,6.90380592 L16.5961941,6.90380592 C17.1315855,6.90380592 17.5719943,7.32548256 17.5952502,7.8603687 L17.9488036,15.9920967 C17.9727933,16.5438602 17.5449482,17.0106003 16.9931847,17.0345901 C16.4414212,17.0585798 15.974681,16.6307346 15.9506913,16.0789711 L15.6387276,8.90380592 L8.1109127,8.90380592 Z" fill="#fff" fill-rule="nonzero"/>
                                                        </g>
                                                    </svg>
                                                </td>
                                                <td>#12415346563475</td>
                                                <td>01 August 2020</td>
                                                <td>Thomas</td>
                                                <td><div class="d-flex align-items-center"><img src="images/avatar/1.jpg" class=" me-2" width="24" alt=""> <span class="w-space-no">Dr. Jackson</span></div></td>
                                                <td><div class="d-flex align-items-center"><img src="images/svg/btc.svg" alt="" class="me-2 img-btc">Bitcoin</div></td>
                                                <td>+$5,553</td>
                                                <td  class="text-end"><div class="badge badge-sm badge-success">COMPLETED</div></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <svg class="arrow style-2 svg-main-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <polygon points="0 0 24 0 24 24 0 24"/>
                                                            <rect fill="#fff" opacity="0.3" transform="translate(11.646447, 12.853553) rotate(-315.000000) translate(-11.646447, -12.853553) " x="10.6464466" y="5.85355339" width="2" height="14" rx="1"/>
                                                            <path d="M8.1109127,8.90380592 C7.55862795,8.90380592 7.1109127,8.45609067 7.1109127,7.90380592 C7.1109127,7.35152117 7.55862795,6.90380592 8.1109127,6.90380592 L16.5961941,6.90380592 C17.1315855,6.90380592 17.5719943,7.32548256 17.5952502,7.8603687 L17.9488036,15.9920967 C17.9727933,16.5438602 17.5449482,17.0106003 16.9931847,17.0345901 C16.4414212,17.0585798 15.974681,16.6307346 15.9506913,16.0789711 L15.6387276,8.90380592 L8.1109127,8.90380592 Z" fill="#fff" fill-rule="nonzero"/>
                                                        </g>
                                                    </svg>
                                                </td>
                                                <td>#12415346563475</td>
                                                <td>01 August 2020</td>
                                                <td>Thomas</td>
                                                <td><div class="d-flex align-items-center"><img src="images/avatar/3.jpg" class=" me-2" width="24" alt=""> <span class="w-space-no">Dr. Jackson</span></div></td>
                                                <td><div class="d-flex align-items-center"><img src="images/svg/btc.svg" alt="" class="me-2 img-btc">Bitcoin</div></td>
                                                <td>+$5,553</td>
                                                <td class="text-end"><div class="badge badge-sm badge-danger">CANCEL</div></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade show" id="year" role="tabpanel" aria-labelledby="year-tab">
                                <div class="table-responsive">
                                    <table class="table table-responsive-md">
                                        <thead>
                                            <tr>
                                                <th>
                                                    #
                                                </th>
                                                <th>Transaction ID</th>
                                                <th>Date</th>
                                                <th>From</th>
                                                <th>To</th>
                                                <th>Coin</th>
                                                <th>Amount</th>
                                                <th class="text-end">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <svg class="arrow svg-main-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <polygon points="0 0 24 0 24 24 0 24"/>
                                                            <rect fill="#fff" opacity="0.3" transform="translate(11.646447, 12.853553) rotate(-315.000000) translate(-11.646447, -12.853553) " x="10.6464466" y="5.85355339" width="2" height="14" rx="1"/>
                                                            <path d="M8.1109127,8.90380592 C7.55862795,8.90380592 7.1109127,8.45609067 7.1109127,7.90380592 C7.1109127,7.35152117 7.55862795,6.90380592 8.1109127,6.90380592 L16.5961941,6.90380592 C17.1315855,6.90380592 17.5719943,7.32548256 17.5952502,7.8603687 L17.9488036,15.9920967 C17.9727933,16.5438602 17.5449482,17.0106003 16.9931847,17.0345901 C16.4414212,17.0585798 15.974681,16.6307346 15.9506913,16.0789711 L15.6387276,8.90380592 L8.1109127,8.90380592 Z" fill="#fff" fill-rule="nonzero"/>
                                                        </g>
                                                    </svg>
                                                </td>
                                                <td>#12415346563475</td>
                                                <td>01 August 2020</td>
                                                <td>Thomas</td>
                                                <td><div class="d-flex align-items-center"><img src="images/avatar/1.jpg" class=" me-2" width="24" alt=""> <span class="w-space-no">Dr. Jackson</span></div></td>
                                                <td><div class="d-flex align-items-center"><img src="images/svg/btc.svg" alt="" class="me-2 img-btc">Bitcoin</div></td>
                                                <td>+$5,553</td>
                                                <td  class="text-end"><div class="badge badge-sm badge-success">COMPLETED</div></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <svg class="arrow style-1 svg-main-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <polygon points="0 0 24 0 24 24 0 24"/>
                                                            <rect fill="#fff" opacity="0.3" transform="translate(11.646447, 12.853553) rotate(-315.000000) translate(-11.646447, -12.853553) " x="10.6464466" y="5.85355339" width="2" height="14" rx="1"/>
                                                            <path d="M8.1109127,8.90380592 C7.55862795,8.90380592 7.1109127,8.45609067 7.1109127,7.90380592 C7.1109127,7.35152117 7.55862795,6.90380592 8.1109127,6.90380592 L16.5961941,6.90380592 C17.1315855,6.90380592 17.5719943,7.32548256 17.5952502,7.8603687 L17.9488036,15.9920967 C17.9727933,16.5438602 17.5449482,17.0106003 16.9931847,17.0345901 C16.4414212,17.0585798 15.974681,16.6307346 15.9506913,16.0789711 L15.6387276,8.90380592 L8.1109127,8.90380592 Z" fill="#fff" fill-rule="nonzero"/>
                                                        </g>
                                                    </svg>
                                                </td>
                                                <td>#12415346563475</td>
                                                <td>01 August 2020</td>
                                                <td>Thomas</td>
                                                <td><div class="d-flex align-items-center"><img src="images/avatar/2.jpg" class=" me-2" width="24" alt=""> <span class="w-space-no">Dr. Jackson</span></div></td>
                                                <td><div class="d-flex align-items-center"><img src="images/svg/btc.svg" alt="" class="me-2 img-btc">Bitcoin</div></td>
                                                <td>+$5,553</td>
                                                <td class="text-end"><div class="badge badge-sm badge-warning">PENDING</div></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <svg class="arrow style-2 svg-main-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <polygon points="0 0 24 0 24 24 0 24"/>
                                                            <rect fill="#fff" opacity="0.3" transform="translate(11.646447, 12.853553) rotate(-315.000000) translate(-11.646447, -12.853553) " x="10.6464466" y="5.85355339" width="2" height="14" rx="1"/>
                                                            <path d="M8.1109127,8.90380592 C7.55862795,8.90380592 7.1109127,8.45609067 7.1109127,7.90380592 C7.1109127,7.35152117 7.55862795,6.90380592 8.1109127,6.90380592 L16.5961941,6.90380592 C17.1315855,6.90380592 17.5719943,7.32548256 17.5952502,7.8603687 L17.9488036,15.9920967 C17.9727933,16.5438602 17.5449482,17.0106003 16.9931847,17.0345901 C16.4414212,17.0585798 15.974681,16.6307346 15.9506913,16.0789711 L15.6387276,8.90380592 L8.1109127,8.90380592 Z" fill="#fff" fill-rule="nonzero"/>
                                                        </g>
                                                    </svg>
                                                </td>
                                                <td>#12415346563475</td>
                                                <td>01 August 2020</td>
                                                <td>Thomas</td>
                                                <td><div class="d-flex align-items-center"><img src="images/avatar/3.jpg" class=" me-2" width="24" alt=""> <span class="w-space-no">Dr. Jackson</span></div></td>
                                                <td><div class="d-flex align-items-center"><img src="images/svg/btc.svg"  alt="" class="me-2 img-btc">Bitcoin</div></td>
                                                <td>+$5,553</td>
                                                <td class="text-end"><div class="badge badge-sm badge-danger">CANCEL</div></td>
                                            </tr>
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
@endsection
