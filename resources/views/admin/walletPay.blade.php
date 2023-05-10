@php
use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.admin")

@section('title') Pay {{ ucwords($member->name) }} @endsection

@section('content')
<div class="section-body">
	<div class="row">
		<div class="col-12 col-md-6 col-lg-6">
			<div class="card">
				<div class="card-header">
					<h4>Pay {{ ucwords($member->name) }} </h4>
				</div>
				<div class="card-body">
				    <h6>Wallet Balance: N{{ number_format($wallet->total_amount,2) }}</h6>
					<form action="{{ route('wallet.pay') }}" method="post">
						@csrf
						<div class="form-group">
							<label>Amount</label>
							<input type="number" class="form-control" name="amount" required>
							<input type="hidden" class="form-control" name="user" value="{{ $wallet->user }}">
						</div>
						<div class="form-group">
							<button type="submit" class="btn btn-success btn-lg  btn-block">Submit</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

    @if(count($transactions))
        <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Payout Requests</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="table-1">
                            <thead>
                            <tr>
                                <th class="text-center">
                                    #
                                </th>
                                <th>User</th>
                                <th>Amount</th>
                                <th>Bank</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $i = 1;
                            @endphp
                            @foreach($transactions as $trans)
                                @php
                                    $member = Util::getUserByEmail($trans->user);
                                    $bank = Util::getBank($trans->bank);
                                @endphp
                                <tr>
                                    <td>
                                        {{ $i++ }}
                                    </td>
                                    <td>
                                        <a href="/admin/users/view/{{ $member->id }}" target="_blank">
                                            {{ ucwords($member->name) }}
                                        </a>
                                    </td>
                                    <td>₦{{ number_format($trans->amount,2) }}</td>
                                    <td>
                                        <big>Bank: </big>{{ $bank->bank_name  ?? ''}}<br>
                                        <big>Account name: </big>{{ $bank->account_name ?? ''}}<br>
                                        <big>Account Number: </big> {{ $bank->account_number ?? ''}}
                                    </td>
                                    <td>{{ date('M d, Y', strtotime($trans->created_at)) }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a href='/admin/transactions/payouts/approve/{{ $trans->id }}' class=" dropdown-item" onclick="return confirm('Are you sure you want to approve this payouts?');">Approve</a>
                                                <a href='/admin/transactions/deposits/decline/{{ $trans->id }}' class="dropdown-item" onclick="return confirm('Are you sure you want to decline this payouts?');">Decline</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
