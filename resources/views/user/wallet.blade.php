@php
    use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.user")

@section('title') My wallet @endsection

@section('wallet') active @endsection

@section('content')
    <div class="row">

        <div class="col-xl-12 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="card card-danger">
                <div class="card-header">
                    <h4>My Wallet</h4>

                    <div class="card-header-action">
                        <div class="dropdown">
                            {{--						<a href="#" data-toggle="dropdown" class="btn btn-warning dropdown-toggle">Options</a>--}}
                            <div class="dropdown-menu">
                                {{--							<a href="" class="dropdown-item has-icon" data-toggle="modal" data-target="#exampleModal"><i class="fas fa-eye"></i> Request Payout</a>--}}
                                {{--							&nbsp &nbsp<a href="/wallet/deposit" data-toggle="modal" data-target="#exampleModal1" class="dropdown-item has-icon"><i class="far fa-edit"></i> Fund Wallet</a>--}}
                            </div>
                        </div>
                    </div>

                    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal4">Fund to Save</a>
                    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal5">Fund To Invest</a>
                    <a href="#" class="btn btn-warning" data-toggle="modal" data-target="#exampleModal">Request Payout</a>

                </div>
                <div class="card-body">
                    <p>You can use <strong>Fund to Invest</strong> to buy and reserve units automatically or use the<strong> Fund to Save</strong> to keep money in your wallet to purchase units manually or later.</p>

                    <p><big>Balance: </big><h2 style="color: #34ba55;">₦{{ number_format(auth()->user()->wallet->total_amount,2) }}</h2></p>
                </div>
            </div>
        </div>


        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="card card-danger">
                <div class="card-header">
                    <h4>My Emerald Bank</h4>
                    <div class="card-header-action">
                        <div class="dropdown">
                            <a href="#" class="btn btn-primary" >Withdrawable: {{number_format(auth()->user()->emeraldbank->total_amount - auth()->user()->bookings()->where('status','approved')->sum('amount'))}} </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p>This is where your rollover funds or automatic funds are stored. Our system will automatically withdraw your funds from your Emerald Bank and invest once the farm is open.</p>

                    <p><big>Balance: </big><h2 style="color: #34ba55;">₦{{ number_format(auth()->user()->emeraldbank->total_amount,2) }}</h2></p>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="card card-danger">
                <div class="card-header">
                    <h4>Inter account transfer</h4>
                    <div class="card-header-action">
                        <div class="dropdown">
                            <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal2">Make Transfer</a>
                            <!--<a href="/transactions" class="btn btn-primary">View Transactions</a>-->
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p>If you're no longer interested in automatic farm purchases, you can make a transfer from your Emerald Bank into your wallet so you can withdraw your funds.</p>

                <!--<p><big>Balance: </big><h2 style="color: #34ba55;">₦{{ number_format(auth()->user()->emeraldbank->total_amount,2) }}</h2></p>-->
                </div>
            </div>
        </div>


    </div>

@endsection
