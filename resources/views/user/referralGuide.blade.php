@php
use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.user")

@section('title') Referral Guide @endsection

@section('transactions') active @endsection

@section('content')
    <section class="section">
        <div class="section-body">
            <h2 class="section-title">Steps to refer a user</h2>
            <div class="row">
                <div class="col-12">
                    <div class="activities">
                        <div class="activity">
                            <div class="activity-icon bg-primary text-white">
                                <i class="fas fa-comment-alt"></i>
                            </div>
                            <div class="activity-detail">
                                <p>Send the registration link to the person you intend to refer <a href="{{route('register')}}">{{route('register')}}</a>.</p>
                            </div>
                        </div>
                        <div class="activity">
                            <div class="activity-icon bg-primary text-white">
                                <i class="fas fa-unlock"></i>
                            </div>
                            <div class="activity-detail">

                                <p>Ask said person to register and verify their account to gain access to the Emerald Farms user dashboard.</p>
                            </div>
                        </div>
                        <div class="activity">
                            <div class="activity-icon bg-primary text-white">
                                <i class="fas fa-arrows-alt "></i>
                            </div>
                            <div class="activity-detail">

                                <p> Then copy this link <a href="{{route('farm.invest', ['slug' => request('slug'), 'ref' => auth()->user()->code])}}">{{route('farm.invest', ['slug' => request('slug'), 'ref' => auth()->user()->code])}}</a>  and give to user to input when making an investment</p>
                            </div>
                        </div>
                        <div class="activity">
                            <div class="activity-icon bg-primary text-white">
                                <i class="fas fa-sign-out-alt"></i>
                            </div>
                            <div class="activity-detail">
                                <p>When the user uses your referral link, you get a one time reward of 2% of their investment amount.</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

