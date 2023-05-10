@php
    use App\Http\Controllers\Globals as Util;
@endphp

@extends('layouts.user')

@section('title') Dashboard @endsection

@section('dashboard') active @endsection

@section('content')
    <div class="row ">

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="card">
                <div class="card-statistic-4">
                    <div class="align-items-center justify-content-between">
                        <a href="/transactions/investments/short">
                            <div class="row ">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                    <div class="card-content">
                                        <h5 class="font-15">Total investments</h5>
                                        <h2 class="mb-3 font-18">{{ number_format($investments) }}</h2>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                    <div class="banner-img">
                                        <img src="assets/img/banner/1.png" alt="">
                                    </div>
                                </div>
                            </div></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="card">
                <div class="card-statistic-4">
                    <div class="align-items-center justify-content-between">
                        <a href="/wallet">
                            <div class="row ">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                    <div class="card-content">
                                        <h5 class="font-15"> Wallet</h5>
                                        <h2 class="mb-3 font-18">₦{{ number_format($wallet->total_amount,2) }}</h2>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                    <div class="banner-img">
                                        <img src="assets/img/banner/2.png" alt="">
                                    </div>
                                </div>
                            </div></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="card">
                <div class="card-statistic-4">
                    <div class="align-items-center justify-content-between">
                        <a href="/wallet">
                            <div class="row ">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                    <div class="card-content">
                                        <h5 class="font-15">Emerald Bank</h5>
                                        <h2 class="mb-3 font-18">₦{{ number_format(auth()->user()->emeraldbank->total_amount,2) }}</h2>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                    <div class="banner-img">
                                        <img src="assets/img/banner/4.png" alt="">
                                    </div>
                                </div>
                            </div></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="card">
                <div class="card-statistic-4">
                    <div class="align-items-center justify-content-between">
                        <a href="/transactions/investments/short">
                            <div class="row ">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                    <div class="card-content">
                                        <h5 class="font-15">Active Investments</h5>
                                        <h2 class="mb-3 font-18">{{$activeInvestmentsCount}}</h2>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                    <div class="banner-img">
                                        <img src="assets/img/banner/3.png" alt="">
                                    </div>
                                </div>
                            </div></a>
                    </div>
                </div>
            </div>
        </div>

           @if(\App\Investment::where(['user'=>auth()->user()->email,'paid'=>0])->latest()->where('status', '!=' ,'pending')->count() > 0)
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Top Active Farm Investments</h4>

                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="table-1">
                            <thead>
                            <tr>
                                <th class="text-center">
                                    #
                                </th>
                                <th>Date created</th>
                                <th>Farmlist</th>
                                <th>Amount</th>
                                <th>Days Remaining</th>
                                <th>Status</th>
                                <th>Maturity Date</th>
                                <th>Maturity Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $i = 1;
                                $investments = \App\Investment::where(['user'=>auth()->user()->email,'paid'=>0])->latest()->where('status', '!=' ,'pending')->get()->take(5);
                            @endphp
                            @foreach($investments as $invest)
                                @php
                                    $cur = strtotime(date('Y-m-d H:i:s'));
                                    $mat = strtotime($invest->maturity_date);
                                    $diff = $mat - $cur;
                                    $farmlist = Util::getFarmlist($invest->farmlist);
                                    $interest = $invest->amount_invested*($farmlist->interest/100);
                                    $add = $invest->amount_invested+$interest;
                                @endphp
                                <tr>
                                    <td>
                                        {{ $i++ }}
                                    </td>
                                    <td>{{$invest->created_at->format('M d, Y')}}</td>
                                    <td>{{ ucwords(Util::getFarmlist($invest->farmlist)->title) }}</td>
                                    <td>
                                        ₦{{ number_format($invest->amount_invested,2) }}
                                    </td>
                                    <td>
                                        @if($invest->maturity_date == null)
                                            <div class="badge badge-warning">Pending</div>
                                        @elseif($invest->maturity_status == 'pending')
                                            {{ abs(round((($diff/24)/60)/60)) }}
                                        @elseif($invest->maturity_status == 'matured')
                                            <div class="badge badge-success">Completed</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($invest->paid == 1)
                                            <div class="badge badge-success">Paid</div>
                                        @else
                                            <div class="badge @if($invest->status == 'active') badge-primary @elseif($invest->status == 'pending') badge-warning @elseif($invest->status == 'closed') badge-danger @endif py-2 px-2">{{ucwords($invest->status) }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $invest->maturity_date ? $invest->maturity_date->format('M d, Y') : '' }}
                                    </td>
                                    <td>
                                        <div class="badge @if($invest->maturity_status == 'matured') badge-primary @elseif($invest->maturity_status == 'pending') badge-warning @endif py-2 px-2">{{ucwords($invest->maturity_status) }}</div>
                                    </td>
                                    <td>
                                        <a href="{{route('short-investment.show', $invest->id)}}" class="btn btn-success">View Investment</a>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(\App\MilestoneInvestment::where(['user_id'=>auth()->user()->id,'paid'=>0])->latest()->where('status', '!=' ,'pending')->count() > 0)
            <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Top Active Longterm Investments</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="table-1">
                            <thead>
                            <tr>
                                <th class="text-center">
                                    #
                                </th>
                                <th>Date created</th>
                                <th>Farmlist</th>
                                <th>Amount invested</th>
                                <th>Milestone</th>
                                <th>Maturity Status</th>
                                <th>Due Date</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $i = 1;
                                $investments = \App\MilestoneInvestment::where(['user_id'=>auth()->user()->id,'paid'=>0])->latest()->where('status', '!=' ,'pending')->get()->take(5);
                            @endphp
                            @foreach($investments as $invest)
                                @foreach($invest->milestoneDates() as $key => $date)
                                    @if ($loop->last)
                                        @php $lastDate = $date @endphp
                                    @endif
                                @endforeach
                                <tr>
                                    <td>
                                        {{ $i++ }}
                                    </td>
                                    <td>{{  $invest->created_at->format('d M, Y h:i A') }}</td>
                                    <td>{{ ucwords($invest->farm->title) }}</td>
                                    <td>
                                        ₦{{ number_format($invest->amount_invested,2) }}
                                    </td>
                                    <td>{{ count($invest->payments).'/'.count($invest->milestoneDates()) }}</td>
                                    <td>
                                        @if(strtotime($lastDate) < strtotime(now()))
                                            <span class="badge badge-success">completed</span>
                                        @else
                                            <span class="badge badge-warning">pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ count($invest->payments) < count($invest->milestoneDates()) ? $invest->milestoneDates()[count($invest->payments)]->format('d M, Y') : 'Fully paid' }}
                                    </td>
                                    <td>
                                        <a href="{{route('long-investment.show', $invest->id)}}" class="btn btn-success">View Investment</a>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

@endsection

