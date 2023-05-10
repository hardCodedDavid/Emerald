@php
    use App\Http\Controllers\Globals as Util;
    use App\Http\Controllers\FarmInvoke as Defarm;
    use App\Investment;
    Defarm::init();
    $me = Util::getAdmin();
    $counts = Investment::where('status','active')->sum('amount_invested');
@endphp

<!DOCTYPE html>
<html lang="en">
	<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('assets/css/app.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <meta id="csrf" name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/img/site.webmanifest') }}">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

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
{{--								<a href="#" class="nav-link" style="color:#9a9a9b"> Active Investment: <span class="text text-success"><big>₦{{ number_format($counts,2) }}</big></span> <span class="d-sm-none d-lg-inline-block"></span></a>--}}
							</li>
						</ul>
					</div>
					<ul class="navbar-nav navbar-right">
						<li class="dropdown">
							<a href="#" data-toggle="dropdown"
								class="nav-link dropdown-toggle nav-link-lg nav-link-user"> <img alt="image" src="{{ Util::getPassport($me) }}" class="user-img-radious-style"  > <span class="d-sm-none d-lg-inline-block"></span></a>
							<div class="dropdown-menu dropdown-menu-right pullDown">
								<div class="dropdown-title">Hello {{ ucwords($me->name) }}</div>
								<a href="/admin/profile" class="dropdown-item has-icon"> <i class="far
									fa-user"></i> Profile
								</a>
								<div class="dropdown-divider"></div>
								<a href="/admin/logout" class="dropdown-item has-icon text-danger"> <i class="fas fa-sign-out-alt"></i>
								Logout
								</a>
							</div>
						</li>
					</ul>
				</nav>
				<div class="main-sidebar sidebar-style-2">
					<aside id="sidebar-wrapper">
						<div class="sidebar-brand">
							<a href="/admin"> <img alt="image" src="{{ asset('assets/img/logo-icon.png') }}" class="header-logo" /> <span
                            class="logo-name">Emerald Farms</span>
                          </a>
						</div>
						<ul class="sidebar-menu">
							<li class="menu-header">Menu</li>
							<li class="dropdown @yield('dashboard')">
								<a href="/admin" class="nav-link"><i data-feather="monitor"></i><span>Dashboard</span></a>
							</li>
{{--							<li class="dropdown @yield('packages')">--}}
{{--								<a href="#" class="menu-toggle nav-link has-dropdown"><i--}}
{{--									data-feather="briefcase"></i><span>Packages</span></a>--}}
{{--								<ul class="dropdown-menu">--}}
{{--									<li><a class="nav-link" href="/admin/packages/add">Add Packages</a></li>--}}
{{--									<li><a class="nav-link" href="/admin/packages">All Packages</a></li>--}}
{{--								</ul>--}}
{{--							</li>--}}

							<li class="dropdown @yield('farmlists')">
								<a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="command"></i><span>Farm Lists</span></a>
								<ul class="dropdown-menu">
                                    <li><a class="nav-link @if(request()->is('admin/farmlist/short')) text-success @endif" href="/admin/farmlist/short">Farm Lists</a></li>
									<li><a class="nav-link @if(request()->is('admin/farmlist/long')) text-success @endif" href="/admin/farmlist/long">Long Term Package</a></li>
								</ul>
							</li>

                            <li class="dropdown @yield('bookings')">
                                <a href="{{route('admin.bookings')}}" class="nav-link "><i data-feather="sliders"></i><span>Farm Bookings</span></a>
                            </li>

{{--                            <li class="dropdown @yield('categories')">--}}
{{--                                <a href="#" class="menu-toggle nav-link has-dropdown"><i--}}
{{--                                        data-feather="briefcase"></i><span>Categories</span></a>--}}
{{--                                <ul class="dropdown-menu">--}}
{{--                                    <li><a class="nav-link" href="/admin/categories/add">Add Category</a></li>--}}
{{--                                    <li><a class="nav-link" href="/admin/categories">All Categories</a></li>--}}
{{--                                </ul>--}}
{{--                            </li>--}}

							<li class="dropdown @yield('users')">
								<a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="users"></i><span>Users</span></a>
								<ul class="dropdown-menu">
									<li><a class="nav-link @if(request()->is('admin/users')) text-success @endif" href="/admin/users">All Users</a></li>
									<li><a class="nav-link @if(request()->is('admin/users/verified')) text-success @endif" href="/admin/users/verified">Verified Users</a></li>
									<li><a class="nav-link @if(request()->is('admin/users/unverified')) text-success @endif" href="/admin/users/unverified">Unverified Users</a></li>
								</ul>
							</li>


                            <li class="dropdown @yield('investments')">
                                <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="database"></i><span>Investments</span></a>
                                <ul class="dropdown-menu">
                                    <li><a class="nav-link @if(request()->is('admin/transactions/investments/long')) text-success @endif" href="{{route('admin.investments.long')}}">Long Term Investments</a></li>
                                    <li><a class="nav-link  @if(request()->is('admin/transactions/investments/short')) text-success @endif" href="{{route('admin.investments.short')}}">Normal Investments</a></li>
                                    <li><a class="nav-link @if(request()->is('admin/transactions/investments')) text-success @endif" href="/admin/transactions/investments">Milestone Updates</a></li>
                                </ul>
                            </li>


                            <li class="dropdown @yield('admins')">
                                <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="users"></i><span>Admins</span></a>
                                <ul class="dropdown-menu">
                                    <li><a class="nav-link @if(request()->is(route('admin.admin.create'))) text-success @endif" href="{{route('admin.admin.create')}}">Create Admin</a></li>
                                    <li><a class="nav-link @if(request()->is(route('admin.admin.users'))) text-success @endif" href="{{route('admin.admin.users')}}">All Admins</a></li>
                                </ul>
                            </li>

							<li class="menu-header">Emerald Bank</li>
							<li class="dropdown @yield('transactions')">
								<a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="activity"></i><span>Transactions</span></a>
								<ul class="dropdown-menu">
									<li><a class="nav-link @if(request()->is('admin/transactions')) text-success @endif" href="/admin/transactions">All Transactions</a></li>
									<li><a class="nav-link @if(request()->is('admin/transactions/deposits')) text-success @endif" href="/admin/transactions/deposits">Deposits</a></li>
{{--									<li><a class="nav-link @if(request()->is('admin/transactions/referrals')) text-success @endif" href="/admin/transactions/referrals">Referrals</a></li>--}}
									<li><a class="nav-link @if(request()->is('admin/transactions/payouts')) text-success @endif" href="/admin/transactions/payouts">Payouts</a></li>
{{--									<li><a class="nav-link @if(request()->is('admin/transactions/payouts/request')) text-success @endif" href="/admin/transactions/payouts/request">Payout Request</a></li>--}}
									<li><a class="nav-link @if(request()->is('admin/transactions/paystack')) text-success @endif" href="/admin/transactions/paystack">Paystack</a></li>
								</ul>
							</li>
							<li class="dropdown @yield('wallets')">
								<a href="/admin/wallets" class="nav-link"><i data-feather="credit-card"></i><span>Wallets</span></a>
							</li>
{{--							<li class="dropdown @yield('news')">--}}
{{--								<a href="/admin/news" class="nav-link"><i data-feather="cast"></i><span>News Updates</span></a>--}}
{{--							</li>--}}

                            <li class="dropdown @yield('transactions')">
                                <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="cast"></i><span>Farm Payouts</span></a>
                                <ul class="dropdown-menu">
                                    <li><a class="nav-link @if(request()->is('/admin/payouts')) text-success @endif" href="/admin/payouts">Farm payouts </a></li>
                                    <li><a class="nav-link @if(request()->is('/admin/payouts/long')) text-success @endif" href="/admin/payouts/long">Long Term payouts </a></li>
                                </ul>
                            </li>

                            <li class="dropdown @yield('batch-payouts')">
                                <a href="/admin/batch-payouts" class="nav-link"><i data-feather="credit-card"></i><span>Batch Payouts</span></a>
                            </li>


{{--                            <li class="dropdown @yield('newsletter')">--}}
{{--                                <a href="{{route('news.letter')}}" class="nav-link "><i data-feather="sliders"></i><span>News Letter</span></a>--}}
{{--                            </li>--}}
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
                                            <td><strong>Active Farm Investments:</strong></td>
                                            <td><span class="badge badge-success"><big>₦{{ number_format(\App\Investment::where(['paid'=>0])->where('status', '!=' ,'pending')->sum('amount_invested'),2) }}</big></span> <span class="d-sm-none d-lg-inline-block"></span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Active Longterm Investments:</strong></td>
                                            <td><span class="badge badge-success"><big>₦{{ number_format(\App\MilestoneInvestment::where(['paid'=>0])->where('status', '!=' ,'pending')->sum('amount_invested'), 2) }}</big></span> <span class="d-sm-none d-lg-inline-block"></span></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="modal-footer bg-whitesmoke br">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
					</div>
					<div class="footer-right">
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
