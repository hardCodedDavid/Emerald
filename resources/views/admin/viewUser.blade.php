@php
use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.admin")

@section('title') View User @endsection

@section('content')
    <div class="row mt-sm-4">
	<div class="col-12 col-md-12 col-lg-4">
		<div class="card author-box">
			<div class="card-body">
				<div class="author-box-center">
					<img alt="image" src="{{ Util::getPassport($user) }}" class="rounded-circle author-box-picture">
					<div class="clearfix"></div>
					<div class="author-box-name">
						<a href="#">{{ ucwords($user->name) }}</a>
					</div>
					<div class="author-box-job">{{ strtolower($user->email) }}</div>
				</div>
				<div class="text-center">
					<div class="author-box-description">
						<p>
							{{ ucfirst($user->address) }}
						</p>
					</div>
				</div>
			</div>
		</div>
		<div class="card">
			<div class="card-header">
				<h4>Personal Details</h4>
			</div>
			<div class="card-body">
				<div class="py-4">
					<p class="clearfix">
						<span class="float-left">
						Address
						</span>
						<span class="float-right text-muted">
						{{ $user->address }}
						</span>
					</p>
					<p class="clearfix">
						<span class="float-left">
						State
						</span>
						<span class="float-right text-muted">
						{{ $user->state }}
						</span>
					</p>
					<p class="clearfix">
						<span class="float-left">
						City
						</span>
						<span class="float-right text-muted">
						{{ $user->city }}
						</span>
					</p>
					<p class="clearfix">
						<span class="float-left">
						Country
						</span>
						<span class="float-right text-muted">
						<a href="#">{{ $user->country }}</a>
						</span>
					</p>
					<p class="clearfix">
						<span class="float-left">
						Zip Code
						</span>
						<span class="float-right text-muted">
						<a href="#"> {{ $user->zip }}</a>
						</span>
					</p>

                    <p class="clearfix">
						<span class="float-left">
						Referral Code
						</span>
                        <span class="float-right text-muted">
						<a href="#"> {{ $user->code }}</a>
						</span>
                    </p>
				</div>
			</div>
		</div>
	</div>
	<div class="col-12 col-md-12 col-lg-8">
		<div class="card">
			<div class="padding-20">
				<ul class="nav nav-tabs" id="myTab2" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" id="profile-tab2" data-toggle="tab" href="#settings" role="tab"
							aria-selected="false">Profile</a>
					</li>
					<li class="nav-item">
                        <a class="nav-link" id="profile-tab3" data-toggle="tab" href="#account" role="tab"
                          aria-selected="false">Account Overview</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="profile-tab4" data-toggle="tab" href="#account2" role="tab"
                          aria-selected="false">Account Info</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" id="profile-tab4" data-toggle="tab" href="#account3" role="tab"
                           aria-selected="false">Actions</a>
                    </li>
				</ul>
				<div class="tab-content tab-bordered" id="myTab3Content">
					<div class="tab-pane fade show active" id="settings" role="tabpanel" aria-labelledby="profile-tab2">
						<form method="post" action="{{ route('profile.edit') }}" enctype="multipart/form-data">
							@csrf
							<div class="card-header">
								<h4>Edit Profile</h4>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="form-group col-md-12 col-12">
										<label>Name</label>
										<input type="text" class="form-control" value="{{ $user->name }}" readonly="">
									</div>
								</div>
								<div class="row">
									<div class="form-group col-md-7 col-12">
										<label>Email</label>
										<input type="email" class="form-control" value="{{ $user->email }}" name="email" readonly="">
									</div>
									<div class="form-group col-md-5 col-12">
										<label>Phone</label>
										<input type="tel" class="form-control" value="{{ $user->phone }}" name="phone" readonly>
									</div>
								</div>
							    <div class="row">
									<div class="form-group col-12 col-md-4">
										<label>Address</label>
										<input type="text" name="address" class="form-control" value='{{ $user->address }}' readonly>
									</div>
									<div class="form-group col-12 col-md-4">
										<label>City</label>
										<input type="text" name="city" class="form-control" value='{{ $user->city }}' readonly>
									</div>
                                    <div class="form-group col-12 col-md-4">
                                        <label>DOB</label>
                                        <input type="date" name="dob" class="form-control" value='{{ $user->dob }}' readonly>
                                    </div>
								</div>
								<div class="row">
									<div class="form-group col-12 col-md-4">
										<label>State</label>
										<input type="text" name="state" class="form-control" value='{{ $user->state }}' readonly>
									</div>
									<div class="form-group col-12 col-md-4">
										<label>Country</label>
										<input type="text" name="country" class="form-control" value='{{ $user->country }}' readonly>
									</div>
									<div class="form-group col-12 col-md-4">
										<label>ZIP Code</label>
										<input type="text" name="zip" class="form-control" value='{{ $user->zip }}' readonly>
									</div>
								</div>

							</div>

						</form>
                        <br><br>
                        <form method="post" action="{{ route('profile.edit.kin') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-header">
                                <h4>Edit Next of Kin</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-12 col-12">
                                        <label>Name</label>
                                        <input type="text" class="form-control" value="{{ $user->nk_Name }}" name="name">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-7 col-12">
                                        <label>Email</label>
                                        <input type="email" class="form-control" value="{{ $user->nk_Email }}" name="email" >
                                    </div>
                                    <div class="form-group col-md-5 col-12">
                                        <label>Phone</label>
                                        <input type="tel" class="form-control" value="{{ $user->nk_Phone }}" name="phone" >
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-12">
                                        <label>Address</label>
                                        <input type="text" name="address" class="form-control" value='{{ $user->nk_Address }}'>
                                    </div>

                                </div>

                            </div>

                        </form>

                    </div>
					<div class="tab-pane fade" id="account" role="tabpanel" aria-labelledby="profile-tab3">
					    <div class="card-body">
                            <h5>Wallet Summary</h5>
                            <ul>
                                <li><p class="small">Wallet Balance: <h6>₦{{number_format($user->wallet->total_amount,2) }}</h6></p></li>
                                <li><p class="small">Emerald Bank: <h6>₦{{ number_format($user->emeraldbank->total_amount,2) }}</h6></p></li>
                                <li><p class="small">Active Investments: <h6>₦{{ number_format($user->totalInvestmentsPlusMilestoneAmount(),2) }}</h6></p></li>
                                <li><p class="small">Withdrawable Funds: <h6>₦{{ number_format($user->emeraldbank->total_amount - $user->bookings()->where('status','approved')->sum('amount')) }}</h6></p></li>
                            </ul>
            			</div>

                        <div class="card-body">
                            <h5>Investment Summary</h5>
                            <ul>
                                <li><p class="small">Total Investments: <h6>{{ count($investments) }}</h6></p></li>
                                <li><p class="small">Total Invested Amount: <h6>{{ number_format($user->totalInvestmentsPlusMilestoneAmountAndPaid()) }}</h6></p></li>
                                <li><p class="small">Total Units Bought: <h6>{{ number_format($user->totalUnitsPlusMilestoneAmount()) }}</h6></p></li>
                            </ul>
                        </div>

                        <div class="card-body">
                            <h5>Transactions Summary</h5>
                            <ul>
                                <li><p class="small">Total transactions: <h6>₦{{number_format(\App\Transaction::where('user_id', $user->id)->where('status', 'approved')->where('type', '!=', 'transfer')->sum('amount')) }}</h6></p></li>
                                <li><p class="small">Total withdrawal: <h6>₦{{number_format(\App\Transaction::where('user_id', $user->id)->where('type', 'payouts')->where('status', 'approved')->sum('amount')) }}</h6></p></li>
                                <li><p class="small">Total deposits: <h6>₦{{number_format(\App\Transaction::where('user_id', $user->id)->where('type', 'deposits')->where('status', 'approved')->sum('amount')) }}</h6></p></li>
                            </ul>
                        </div>

				    </div>
				    <div class="tab-pane fade" id="account2" role="tabpanel" aria-labelledby="profile-tab4">
					    <div class="row">
                    		<div class="col-12">
                    			<div class="card">
                    				<div class="card-header">
                    					<h4>Banks</h4>
                    				</div>
                    				<div class="card-body">
                    					<div class="table-responsive">
                    						<table class="table table-striped" id="table-1">
                    							<thead>
                    								<tr>
                    									<th class="text-center">
                    										#
                    									</th>
                    									<th>Bank</th>
                    									<th>Account Name</th>
                    									<th>Account Number</th>
                    									<th>Account Information</th>
                     								</tr>
                    							</thead>
                    							<tbody>
                    								@php
                    								$i = 1;
                    								@endphp
                    								@foreach($banks as $bank)
                    								<tr>
                    									<td>
                    										{{ $i++ }}
                    									</td>
                    									<td>
                    										{{ ucwords($bank->bank_name) }}
                    									</td>
                    									<td>{{ ucwords($bank->account_name) }}</td>
                    									<td>{{ $bank->account_number }}</td>
                    									<td>{{ $bank->account_information }}</td>
                    								</tr>
                    								@endforeach
                    							</tbody>
                    						</table>
                    					</div>
                    				</div>
                    			</div>
                    		</div>
                    	</div>
				    </div>

                    <div class="tab-pane fade" id="account3" role="tabpanel" aria-labelledby="profile-tab5">
                        <div class="row">
                            <div class="col-12">
                                <h4>Wallet Funding</h4>

                                <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal2">Fund Wallet</a>
                                <a href="#" class="btn btn-warning" data-toggle="modal" data-target="#exampleModal">Withdraw Funds</a>
                                <a href="#" class="btn btn-warning" data-toggle="modal" data-target="#exampleModal3">Transfer</a>
                            </div>
                            <br>
                            <div class="col-12 pt-5">
                                <h4>Farm Sponsorships</h4>

                                <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#farmlist">Sponsor Farm</a>
                                <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#longterm">Sponsor Long-Term</a>
                                <a href="#" class="btn btn-warning" data-toggle="modal" data-target="#booking">Book Farm</a>
                            </div>
                            <br>
                            <div class="col-12 pt-5">

                                <a target="_blank" href="{{route('admin.user.investments', $user->id)}}" class="btn btn-primary">View Investment</a>

                                <a target="_blank" href="{{route('admin.user.investments.long', $user->id)}}" class="btn btn-primary">View Long-term Investment</a>

                                <a target="_blank" href="{{route('admin.user.wallets', $user->id)}}" class="btn btn-info">View Wallet</a>
                            </div>
                        </div>
                    </div>

                </div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('modal')
    <div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="formModal"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModal">Fund Account</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form action="{{route('admin.deposit.user')}}" method="POST">
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
                                <input type="hidden" class="form-control" name="user_id" value="{{$user->id}}" required>

                            </div>
                        </div>

                        <div class="form-group">
                            <label>Bank:</label>
                            <select name="location" class="form-control" required>
                                <option value="wallet">Wallet</option>
                                <option value="bank">Emerald Bank</option>
                            </select>
                        </div>


                        <button type="submit" class="btn btn-primary btn-block btn-lg m-t-15 waves-effect">Deposit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="formModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModal">Request Payout</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form class="" action="{{route('admin.withdraw.user')}}" method="POST">
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
                                <input type="hidden" class="form-control" name="user_id" value="{{$user->id}}" required>
                            </div>

                            <div class="form-group">
                                <label>Bank:</label>
                                <select name="location" class="form-control" required>
                                    <option value="wallet">Wallet - NGN {{number_format($user->wallet->total_amount, 2)}}</option>
                                    <option value="bank">Emerald Bank - NGN {{number_format($user->emeraldbank->total_amount, 2)}}</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary m-t-15 waves-effect">REQUEST</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal3" tabindex="-1" role="dialog" aria-labelledby="formModal"
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

                    <form class="" action="{{ route('admin.transfer.user') }}" method="post">
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
                                <input type="hidden" class="form-control" name="user_id"  value="{{$user->id}}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>From:</label>
                            <select name="from" class="form-control" required>
                                <option value="wallet">Wallet  - NGN {{number_format($user->wallet->total_amount, 2)}} </option>
                                <option value="bank">Emerald Bank  - NGN {{number_format($user->emeraldbank->total_amount, 2)}}</option>
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

    <div class="modal fade" id="booking" tabindex="-1" role="dialog" aria-labelledby="formModal"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModal">Create Farm Booking </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form class="" action="{{ route('admin.bookings.store') }}" method="post">
                        @csrf

                        <div class="form-group">
                            <label>Category:</label>
                            <input type="hidden" name="user_id" value="{{$user->id}}">
                            <select name="category_id" class="form-control" required id="category">
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

    <div class="modal fade" id="farmlist" tabindex="-1" role="dialog" aria-labelledby="formModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModal">Sponsor Farm </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form action="{{ route('admin.farmlist.store') }}" method="post">
                        @csrf

                        <label>Farm:</label>
                        <select id="farmList" name="farm_id" class="form-control" required>
                            <option value="null">Select Farm</option>

                            @foreach(\App\FarmList::whereStatus('opened')->get() as $farmlist)
                                <option data-price="{{$farmlist->price}}" value="{{$farmlist->id}}">{{$farmlist->title}} - NGN {{$farmlist->price}}  - {{$farmlist->available_units}} Units </option>
                            @endforeach
                        </select>
                        <br>
                        <strong>Enter your units to invest</strong>
                        <div class="form-group">
                            <input type="number" oninput="computePayment(event)" class="form-control" name="unit" required>
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <input type="hidden" id="farmPrice">
                            <div class="small mt-1" id="amountToPay"><strong></strong></div>
                        </div>

                        <!--<div class="form-check">-->
                        <!--    <input type="checkbox" name="rollover" class="form-check-input" id="rollover">-->
                        <!--    <label class="form-check-label" for="rollover">Rollover Investment</label>-->
                        <!--</div>-->

                        <button id="investButton" type="submit" disabled onclick="return confirm('You won\'t be able to cancel this once started. Are you sure you want to proceed?');" class="btn btn-primary btn-block btn-lg m-t-15 waves-effect">Make Farm Sponsorship</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="longterm" tabindex="-1" role="dialog" aria-labelledby="formModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModal">Sponsor Long-Term </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form action="{{ route('admin.long-invest.store') }}" method="post">
                        @csrf

                        <label>Farm:</label>
                        <select id="farmListLong" name="farm_id" class="form-control" required>
                            <option value="">Select Farm</option>

                            @foreach(\App\MilestoneFarm::get() as $farmlist)
                                @if($farmlist->isOpen())
                                    <option data-price="{{$farmlist->price}}" value="{{$farmlist->id}}">{{$farmlist->title}} - NGN {{$farmlist->price}}  - {{$farmlist->available_units}} Units </option>
                                @endif
                            @endforeach
                        </select>
                        <br>
                        <strong>Enter your units to invest</strong>
                        <div class="form-group">
                            <input type="number" oninput="longComputePayment(event)" class="form-control" name="unit" required>
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <input type="hidden" id="longFarmPrice">
                            <div class="small mt-1" id="longAmountToPay"><strong></strong></div>
                        </div>

                        <button id="longInvestButton" disabled type="submit" onclick="return confirm('You won\'t be able to cancel this once started. Are you sure you want to proceed?');" class="btn btn-primary btn-block btn-lg m-t-15 waves-effect">Make Long-Term Sponsorship</button>
                    </form>
                </div>

            </div>
        </div>
    </div>


@endsection

@section('foot')
<script src="{{ asset('assets/bundles/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/bundles/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/js/page/datatables.js') }}"></script>

<script>
    let longAmountToPay = document.getElementById('longAmountToPay');
    let amountToPay = document.getElementById('amountToPay');
    let longInvestButton = document.getElementById('longInvestButton');
    let investButton = document.getElementById('investButton');
    let walletBalance = {{ \App\Wallet::where('user', $user->email)->latest()->first()['total_amount'] }};
    let toPay = 0;
    document.getElementById('farmList').addEventListener('change', function (){
        let price = 0;
        $("#farmList option").each(function(){
            let selected = $(this).val();
            let actualPrice = $(this).attr('data-price');
            if(selected === $('#farmList').val()){
                price = actualPrice;
            }
        })
        $('#farmPrice').val(price)
    });
    document.getElementById('farmListLong').addEventListener('change', function (){
        let priceLong = 0;
        $("#farmListLong option").each(function(){
            let selectedLong = $(this).val();
            let actualLongPrice = $(this).attr('data-price');
            if(selectedLong === $('#farmListLong').val()){
                priceLong = actualLongPrice;
            }
        })
        $('#longFarmPrice').val(priceLong)
    });
    amountToPay.style.display = 'none';
    let computePayment = function (e) {
        if (e.target.value){
            toPay = $('#farmPrice').val() * e.target.value;
            amountToPay.style.display = 'block';
            amountToPay.innerHTML = '<strong> Amount: ₦' + numberFormat(toPay) + '</strong>';
            if (walletBalance < toPay){
                e.target.style.border = '1px solid red';
                investButton.disabled = true;
            }else{
                investButton.disabled = false;
                e.target.style.border = '1px solid #20c997'
            }
        }else{
            amountToPay.style.display = 'none';
        }
    }
    longAmountToPay.style.display = 'none';
    let longComputePayment = function (e) {
        if (e.target.value){
            toPay = $('#longFarmPrice').val() * e.target.value;
            longAmountToPay.style.display = 'block';
            longAmountToPay.innerHTML = '<strong> Amount: ₦' + numberFormat(toPay) + '</strong>';
            if (walletBalance < toPay){
                e.target.style.border = '1px solid red';
                longInvestButton.disabled = true;
            }else{
                longInvestButton.disabled = false;
                e.target.style.border = '1px solid #20c997'
            }
        }else{
            longAmountToPay.style.display = 'none';
        }
    }
    function numberFormat(amount, decimal = ".", thousands = ",") {
        try {
            amount = Number.parseFloat(amount);
            let decimalCount = Number.isInteger(amount) ? 0 : amount.toString().split('.')[1].length;
            const negativeSign = amount < 0 ? "-" : "";
            let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
            let j = (i.length > 3) ? i.length % 3 : 0;
            return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
        } catch (e) {
            console.log(e)
        }
    }
    $(document).ready(function(){
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
                    url: `${location.origin}/admin/farmlist/${$(this).val()}/farms`,
                }).done(function(response) {
                    if(response.data == null){
                        $('#new-content').html('<p class="text-danger">No pending farm in chosen category!</p>');
                    }else{
                        let farm = response.data;
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
                                            <option value="wallet">Wallet</option>
                                        </select>

                                    <p class="text-danger" id="save-button-helper">Choose valid payment method!</p>

                                    </div>`;
                        outer += ` <div class="form-check">
                                        <input type="checkbox" name="rollover" class="form-check-input" id="rollover">
                                        <label class="form-check-label" for="rollover">Rollover Investment</label>
                                    </div>
                        <button type="button" class="btn btn-primary btn-block btn-lg m-t-15 waves-effect" id="save-button">Make Booking</button>\`;
`;

                        $('#new-content').html(outer);
                    }
                });
            }
        });
    });
</script>
@endsection
