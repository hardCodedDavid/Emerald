@php
    use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.admin")

@section('title') My wallet @endsection

@section('wallet') active @endsection

@section('content')
    <div class="row">

        <div class="col-xl-12 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="card card-danger">
                <div class="card-header">
                    <h4>{{$user->name}}'s Wallet</h4>


                </div>
                <div class="card-body">
                    <p>You can use <strong>Fund to Invest</strong> to buy and reserve units automatically or use the<strong> Fund to Save</strong> to keep money in your wallet to purchase units manually or later.</p>

                    <p><big>Balance: </big><h2 style="color: #34ba55;">₦{{ number_format($user->wallet->total_amount,2) }}</h2></p>
                </div>
            </div>
        </div>


        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="card card-danger">
                <div class="card-header">
                    <h4>My Emerald Bank</h4>
                    <div class="card-header-action">
                        <div class="dropdown">
                            <a href="#" class="btn btn-primary" >Withdrawable: {{number_format($user->emeraldbank->total_amount - $user->bookings()->where('status','approved')->sum('amount'))}} </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p>This is where your rollover funds or automatic funds are stored. Our system will automatically withdraw your funds from your Emerald Bank and invest once the farm is open.</p>

                    <p><big>Balance: </big><h2 style="color: #34ba55;">₦{{ number_format($user->emeraldbank->total_amount,2) }}</h2></p>
                </div>
            </div>
        </div>

    </div>

@endsection
