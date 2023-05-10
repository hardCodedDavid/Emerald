@php
use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.admin")

@section('title') Payout Request @endsection

@section('head')
<link rel="stylesheet" href="{{ asset('assets/bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection

@section('transactions') active @endsection

@section('content')
<div class="section-body">
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<h4>Payout Request</h4>
                    <a href="{{route('download.transactions','payoutRequests')}}" class="badge badge-success">Export Excel</a>

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
											<big>Bank: </big>{{ $bank->bank_name }}<br>
											<big>Account name: </big>{{ $bank->account_name }}<br>
											<big>Account Number: </big> {{ $bank->account_number }}
									</td>
									<td>{{ date('M d, Y', strtotime($trans->created_at)) }}</td>
									<td>
                                        <div class="dropdown">
                                            <button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                                                @if($trans->type == 'deposits' && $trans->status == 'pending')
                                                    <a href='/admin/transactions/deposits/approve/{{ $trans->id }}' class="dropdown-item" onclick="return confirm('Are you sure you want to approve this deposits?');">Approve</a>
                                                    <a href='/admin/transactions/deposits/decline/{{ $trans->id }}' class="dropdown-item" onclick="return confirm('Are you sure you want to decline this deposits?');">Decline</a>
                                                @elseif($trans->type == 'payouts' && $trans->status == 'pending')
                                                    <a href='/admin/transactions/payouts/approve/{{ $trans->id }}' class=" dropdown-item" onclick="return confirm('Are you sure you want to approve this payouts?');">Approve</a>
                                                    <a href='/admin/transactions/deposits/decline/{{ $trans->id }}' class="dropdown-item" onclick="return confirm('Are you sure you want to decline this payouts?');">Decline</a>
                                                @endif

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
</div>
@endsection

@section('foot')
<script src="{{ asset('assets/bundles/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/bundles/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/js/page/datatables.js') }}"></script>
@endsection
