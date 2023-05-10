@php
    use App\Http\Controllers\Globals as Util;
    use App\Http\Controllers\FarmInvoke as Defarm;
    Defarm::init();
    $me = Util::getUser();

    $wallet = Util::getWallet($me);
    $banks = Util::getBanks($me->email);
    $latest = Util::getLatest();
@endphp

    <!DOCTYPE html>
<html lang="en">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('assets/css/app.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/img/site.webmanifest') }}">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <style>
        .hidden{
            display:none;
        }
    </style>
    @yield('head')
</head>
<body>
<div class="loader"></div>

<div id="app">
    <div class="main-wrapper main-wrapper-1">
        <div class="navbar-bg"></div>
        <nav class="navbar navbar-expand-lg main-navbar sticky">
            <div class="form-inline mr-auto">
                <ul class="navbar-nav mr-3">
                    <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg
								collapse-btn"> <i data-feather="align-justify"></i></a></li>
                    <li><a href="#" class="nav-link nav-link-lg fullscreen-btn">
                            <i data-feather="maximize"></i>
                        </a>
                    </li>
                    <li class="my-auto">
                        <button type="button" class="badge badge-success" style="border: none; outline: none" data-toggle="modal" data-target="#basicModal">
                            Quick Overview
                        </button>
{{--                        <a href="#" class="nav-link d-none d-sm-block" style="color:#9a9a9b">--}}
{{--                            Active Investments: <span class="badge badge-success"><big>₦{{ number_format(auth()->user()->totalInvestmentsPlusMilestoneAmount(),2) }}</big></span> <span class="d-sm-none d-lg-inline-block"></span>--}}
{{--                        </a>--}}
                    </li>
                </ul>
            </div>
            <ul class="navbar-nav navbar-right">
                <li class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown" class="nav-link nav-link-lg message-toggle"><i data-feather="bell"></i>
                    @if(auth()->user()->unreadNotifications()->count() > 0)
                    <span class="badge headerBadge1">{{ auth()->user()->unreadNotifications()->count() }}</span>
                    @endif </a>
                    <div class="dropdown-menu dropdown-list dropdown-menu-right pullDown">
                        <div class="dropdown-header">
                            Notifications
                            <div class="float-right">
                                <a href="/notifications/myaction/viewall">Mark All As Read</a>
                            </div>
                        </div>
                        <div class="dropdown-list-content dropdown-list-icons" style="overflow-y: auto">
                            @forelse(auth()->user()->unreadNotifications as $notification)
                            <a href="/notifications/{{ $notification->id }}" class="dropdown-item">
                                {!! $notification->data['icon'] !!}
                                <span class="dropdown-item-desc">
                                    {{ $notification->data['title'] }}
                                    <span class="time">
                                        {{ \Carbon\Carbon::createFromTimeStamp(strtotime($notification->created_at))->diffForHumans() }}
                                    </span>
                                </span>
                            </a>
                            @endforeach
                        </div>
                        <div class="dropdown-footer text-center">
                            <a href="/notifications">View All <i class="fas fa-chevron-right"></i></a>
                        </div>
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" data-toggle="dropdown"
                       class="nav-link dropdown-toggle nav-link-lg nav-link-user"> <img alt="image" src="{{ Util::getPassport($me) }}"
                                                                                        class="user-img-radious-style" style="height: 30px"> <span class="d-sm-none d-lg-inline-block"></span></a>
                    <div class="dropdown-menu dropdown-menu-right pullDown">
                        <div class="dropdown-title">Hello {{ ucwords($me->name) }}</div>
                        <a href="/profile" class="dropdown-item has-icon"> <i class="far
									fa-user"></i> Profile
                        </a>
                        <a href="/banks" class="dropdown-item has-icon"> <i class="fas fa-piggy-bank"></i> Bank Details
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="/logout" class="dropdown-item has-icon text-danger"> <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </a>
                    </div>
                </li>
            </ul>
        </nav>
        <div class="main-sidebar sidebar-style-2">
            <aside id="sidebar-wrapper">
                <div class="sidebar-brand">
                    <a href="/"> <img alt="image" src="{{ asset('assets/img/logo-icon.png') }}" class="header-logo" /> <span
                            class="logo-name">Emerald Farms</span>
                    </a>
                </div>
                <ul class="sidebar-menu">
                    <li class="menu-header">Menu</li>
                    <li class="dropdown @yield('dashboard')">
                        <a href="/" class="nav-link"><i data-feather="monitor"></i><span>Dashboard</span></a>
                    </li>

                    <li class="dropdown @yield('farmlist')">
                        <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="command"></i><span>Farmlists</span></a>
                        <ul class="dropdown-menu">
                            <li><a class="nav-link @if(request()->is('farmlist')) text-success @endif" href="/farmlist">Farmlist</a></li>
                            <li><a class="nav-link @if(request()->is('farmlist/long')) text-success @endif" href="/farmlist/long">Long Term Package</a></li>
                        </ul>
                    </li>

                    <li class="menu-header">Emerald Bank</li>
                    <li class="dropdown @yield('transactions')">
                        <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="activity"></i><span>Transactions</span></a>
                        <ul class="dropdown-menu">
                            <li><a class="nav-link @if(request()->is('transactions')) text-success @endif" href="/transactions">All Transactions</a></li>
{{--                            <li><a class="nav-link @if(request()->is('transactions/investments')) text-success @endif" href="/transactions/investments">Investment</a></li>--}}
                            <li><a class="nav-link @if(request()->is('transactions/payouts')) text-success @endif" href="/transactions/payouts">Payouts</a></li>
                            <li><a class="nav-link @if(request()->is('transactions/deposits')) text-success @endif" href="/transactions/deposits">Deposits</a></li>
                            <!--<li><a class="nav-link" href="/transactions/referrals">Referrals</a></li>-->
                        </ul>
                    </li>
                    <li class="dropdown @yield('investments')">
                        <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="command"></i><span>Investments</span></a>
                        <ul class="dropdown-menu">
                            <li><a class="nav-link @if(request()->is('transactions/investments/short')) text-success @endif" href="/transactions/investments/short">Farm Investments</a></li>
                            <li><a class="nav-link @if(request()->is('transactions/investments/long')) text-success @endif" href="/transactions/investments/long">Long Term Investments</a></li>
                        </ul>
                    </li>
                    <li class="dropdown @yield('wallet')">
                        <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="credit-card"></i><span>Wallet</span></a>
                        <ul class="dropdown-menu">
                            <li><a class="nav-link @if(request()->is('wallet')) text-success @endif" href="/wallet">View Wallet</a></li>
                            <li><a class="nav-link " data-toggle="modal" data-target="#exampleModal">Request Payout</a></li>
                            <li><a class="nav-link" data-toggle="modal" data-target="#exampleModal4">Fund to Save</a></li>
                            <li><a class="nav-link" data-toggle="modal" data-target="#exampleModal5">Fund to Invest</a></li>
                            <li><a class="nav-link" data-toggle="modal" data-target="#exampleModal2">Interaccount Transfer</a></li>
                        </ul>
                    </li>
                    <li class="dropdown @yield('settings')">
                        <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="settings"></i><span>Settings</span></a>
                        <ul class="dropdown-menu">
                            <li><a class="nav-link" href="/profile">User Profile</a></li>
                            <li><a class="nav-link" href="/banks">Bank Details</a></li>
{{--                            <li><a class="nav-link"  href="/logout">Logout</a></li>--}}
                        </ul>
                    </li>
{{--                     <li class="dropdown @yield('news')">--}}
{{--                        <a href="/news" class="nav-link"><i data-feather="cast"></i><span>News Updates</span></a>--}}
{{--                    </li>--}}

                </ul>
            </aside>
        </div>
        <div class="main-content">
            <section class="section">
            @yield('breadcrumbs')
            @if(session()->has('message'))
                {!! session()->get('message') !!}
            @endif

            @yield('content')

            </section>
            @yield('modal')
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="formModal"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="formModal">Request Payout</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form class="" action="{{ route('payouts.add') }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label>Amount</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                ₦
                                            </div>
                                        </div>
                                        <input type="number" class="form-control" name="amount" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Bank </label>
                                        <select name="bank" class="form-control" required>

                                            @foreach($banks as $bank)
                                                <option value="{{ $bank->id }}">{{ ucwords($bank->bank_name." - ".$bank->account_number) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary m-t-15 waves-effect">REQUEST</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- basic modal -->
            <div class="modal fade" id="basicModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Quick Overview</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <table class="table">
                                <tr>
                                    <td><strong>Active Investment:</strong></td>
                                    <td><span class="badge badge-success"><big>₦{{ number_format(auth()->user()->totalInvestmentsPlusMilestoneAmount(),2) }}</big></span> <span class="d-sm-none d-lg-inline-block"></span></td>
                                </tr>
                                <tr>
                                    <td><strong>Wallet:</strong></td>
                                    <td><span class="badge badge-success"><big>₦{{number_format(auth()->user()->wallet->total_amount, 2)}}</big></span> <span class="d-sm-none d-lg-inline-block"></span></td>
                                </tr>
                                <tr>
                                    <td><strong>Emerald Bank:</strong></td>
                                    <td><span class="badge badge-success"><big>₦{{number_format(auth()->user()->emeraldbank->total_amount, 2)}}</big></span> <span class="d-sm-none d-lg-inline-block"></span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="modal-footer bg-whitesmoke br">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="exampleModal1" tabindex="-1" role="dialog" aria-labelledby="formModal"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="formModal">Deposit </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!--<small><bold>Deposit:</bold> Save money in your wallet or use your funds purchase a farm cycle manually.</small>-->
                            <!--<hr>-->
                            <a class="btn btn-warning btn-lg btn-block mb-2" href="#">Pay Using Mastercard/VISA CARD/ Credit Card/Verve Card</a>
                            <center><h4>OR</h4></center>
                            <div class="alert alert-success">
                                <center>Via Bank Transfer / Deposit</center><br>
                                Bank name: Zenith Bank<br>
                                Account Number: 1216057639<br>
                                Account name: Emerald farms & cons ltd(invest) <br>
                            </div>
                            <form class="" action="{{ route('deposits.add') }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label>Amount</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                ₦
                                            </div>
                                        </div>
                                        <input type="number" class="form-control" name="amount" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Payment Method</label>
                                    <select name="method" class="form-control" required>
                                        <option value="null">Select Method</option>
                                        <option value="bank">Bank Transfer / Deposit</option>
                                        <option value="online">Online Payment / Card</option>
                                    </select>
                                </div>


                                <div class="form-group">
                                    <label>Bank:</label>
                                    <select name="location" class="form-control" required id="switch">
                                        <option value="wallet">Wallet</option>
                                        <option value="bank">Emerald Bank</option>
                                    </select>
                                </div>

                                <div class="extras hidden">
                                    <div class="form-group">
                                        <label>Category:</label>
                                        <select name="category_id" class="form-control" required>
                                            @foreach(\App\Category::all() as $category)
                                                <option value="{{$category->id}}">{{$category->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Units</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="units">
                                        </div>
                                    </div>
                                </div>

                                <small><bold>Wallet Funds:</bold> This funds are used to purchase a farm cycle and also for savings purposes</small>
                                <small><bold>Emerald Bank:</bold> This funds are used to automatically sponsor a farm cycle for you</small>
                                <button type="submit" class="btn btn-primary btn-block btn-lg m-t-15 waves-effect">DEPOSITS</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="exampleModal4" tabindex="-1" role="dialog" aria-labelledby="formModal"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="formModal">Deposit </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <small class="text-danger">Funds will be added to your wallet balance and can be used to invest in a farm, withdraw to account or transfer to your emerald bank</small>
                            <hr>
                            <div style="background-color:;" class="alert alert-danger">
                                <p><span><h6>Bank Details </h6></span></p>
                                <strong>Bank Name</strong> Zenith Bank <br>
                                <strong>Account Number:</strong> 1216057639 <br>
                                <strong>Account Name:</strong> Emerald farms & cons ltd(invest) <br>

                            </div>
                            <hr>

                            <form class="" action="{{ route('deposits.add') }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label>Amount</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                ₦
                                            </div>
                                        </div>
                                        <input type="number" class="form-control" name="amount" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Payment Method</label>
                                    <select name="method" class="form-control" required id="deposit-method">
                                        <option value="null">Select Method</option>
                                        <option value="bank">Bank Transfer / Deposit</option>
                                        <option value="online">Online Payment / Card</option>
                                    </select>

                                    <p class="text-danger" id="deposit-button-helper">Choose valid payment method!</p>
                                </div>

                                <input type="hidden" class="form-control" name="location" value="wallet">

                                <button type="button" class="btn btn-primary btn-block btn-lg m-t-15 waves-effect" id="deposit-button">DEPOSIT</button>
                            </form>
                            <br>
                            <p>
                                <center>
                                    <img style="width: 50%;" src="{{ asset('assets/img/emerald-farms-online-payment.png') }}" alt="">
                                </center>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="exampleModal5" tabindex="-1" role="dialog" aria-labelledby="formModal"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="formModal">Investment Deposit </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <small class="text-danger">Funds will be added to your emerald bank balance, which can be used to automatically invest in a farm or retain your rollover funds.</small>
                            <hr>
                            <div style="background-color:;" class="alert alert-danger">
                                <p><span><label>Bank Details </label></span></p>
                                <label><strong>Bank Name</strong>Zenith Bank</label><br>
                                <label><strong>Account Number:</strong> 1216057639 </label> <br>
                                <label><strong>Account Name:</strong> Emerald farms & cons ltd(invest) </label> <br>
                            </div>
                            <hr>
                            <form class="" action="{{ route('bookings.store') }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label>Farm Category:</label>
                                    <select name="category_id" class="form-control" id="category" required>
                                        <option value="null">Select Category</option>

                                        @foreach(\App\Category::all() as $category)
                                            <option value="{{$category->id}}">{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div id="new-content">

                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="formModal"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="formModal">Inter Account Transfer </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <form class="" action="{{ route('interaccount.transfer') }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label>Amount</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                ₦
                                            </div>
                                        </div>
                                        <input type="number" class="form-control" name="amount" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>From:</label>
                                    <select name="from" class="form-control" required>
                                        <option value="wallet">Wallet - NGN {{number_format(auth()->user()->wallet->total_amount)}}</option>
                                        <option value="bank">Emerald Bank - {{number_format(auth()->user()->emeraldbank->total_amount)}}</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>To:</label>
                                    <select name="to" class="form-control" required>
                                        <option value="bank">Emerald Bank</option>
                                        <option value="wallet">Wallet</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block btn-lg m-t-15 waves-effect">Transfer</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <footer class="main-footer">
            <div class="footer-left">
                Copyright &copy; {{now()->year}}
                <div class="bullet"></div>
                <a href="#">Emerald Farms</a>
                &nbsp; &nbsp;| &nbsp; &nbsp;
                Powered by
                <div class="bullet"></div>
                <a target="_blank" href="https://www.softwebdigital.com">Soft-Web Digital</a>
            </div>

        </footer>
    </div>
</div>

<script src="{{ asset('assets/js/app.min.js') }}"></script>
<script src="{{ asset('assets/bundles/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/js/page/index.js') }}"></script>
<script src="{{ asset('assets/js/scripts.js') }}"></script>
<script src="{{ asset('assets/js/custom.js') }}"></script>
@yield('foot')
</body>
</html>
<script type="text/javascript">
    <!--Start of Tawk.to Script-->

    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/5de42d96d96992700fca2e24/default';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);
    })();
    <!--End of Tawk.to Script-->


    $(document).ready(function(){
        $('#switch').change(function(){
            if($(this).val() == 'bank'){
                $('.extras').removeClass('hidden');
            }else{
                $('.extras').addClass('hidden');
            }
        });

        $('#deposit-method').change(function(){
            if($(this).val() == 'null'){
                $('#deposit-button').attr('type','button');
                $('#deposit-button-helper').html('Select valid payment method');
            }else{
                $('#deposit-button').attr('type','submit');
                $('#deposit-button-helper').html('');
            }
        });

        $(document).on('change', '#save-method', function(){
            if($(this).val() == 'null'){
                $('#save-button').attr('type','button');
                $('#save-button-helper').html('Select valid payment method');
            }else{
                $('#save-button').attr('type','submit');
                $('#save-button-helper').html('');
            }
        });

        $('#category').change(function(){
            if($(this).val() == 'null'){
                $('#new-content').html('<p class="text-danger">Choose valid category!</p>');
            }else{
                $.ajax({
                    url: `/farmlist/${$(this).val()}/farms`,
                }).done(function(response) {
                    let farm = response.data;
                    if(farm == null){
                        $('#new-content').html('<p class="text-danger">No pending farm in chosen category!</p>');
                    }else{
                        var formatter = new Intl.NumberFormat('en-US', {
                            style: 'currency',
                            currency: 'NGN',
                        });

                        var content = '';
                        var outer = '';

                        outer += '<hr> <h5>Farm Investment</h5>';

                        outer += `<p> <strong>Farm Name:</strong> ${farm.title} <br>
                                    <strong>Price Per Unit:</strong> ${formatter.format(farm.price)} <br>
                                    <strong>Current Available Units:</strong> <span style="">${farm.available_units} Units</span>
                                </p>`;

                        outer += `<input type="hidden" name="farm_id" value="${farm.id}">`;

                        outer += '<hr>';

                        outer += ` <div class="form-group">
                                        <label>Amount</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    ₦
                                                </div>
                                            </div>
                                            <input type="number" class="form-control" name="amount" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Payment Method</label>
                                        <select name="payment_type" class="form-control" required id="save-method">
                                            <option value="null">Select Method</option>
                                            <option value="bank">Bank Transfer / Deposit</option>
                                            <option value="online">Online Payment / Card</option>
                                            <option value="wallet">Wallet</option>
                                        </select>

                                    <p class="text-danger" id="save-button-helper">Choose valid payment method!</p>

                                    </div>`;

                        // outer += ` <div class="form-check">
                        //                 <input type="checkbox" name="rollover" class="form-check-input" id="rollover">
                        //                 <label class="form-check-label" for="rollover">Rollover Investment</label>
                        //             </div>

                        //         <button type="button" class="btn btn-primary btn-block btn-lg m-t-15 waves-effect" id="save-button">DEPOSITS</button>`;

                        outer += `<button type="button" class="btn btn-primary btn-block btn-lg m-t-15 waves-effect" id="save-button">DEPOSITS</button>`;

                        $('#new-content').html(outer);
                    }
                });
            }
        });
    });
</script>
