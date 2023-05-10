@php
use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.user")

@section('title') Transactions @endsection

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
					<h4>Transactions</h4>
                    <a href="{{route('download.user.transactions','all')}}" class="badge badge-success">Download Statement</a>
                </div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-striped" id="table-1">
							<thead>
								<tr>
									<th class="text-center">
										#
									</th>
									<th>Amount</th>
									<th>Type</th>
									<th> Date </th>
									<th>Status</th>
 								</tr>
							</thead>
							<tbody>
								@php
								$i = 1;
								@endphp
								@foreach($transactions as $trans)
								<tr>
									<td>
										{{ $i++ }}
									</td>
									<td>
										₦{{ number_format($trans->amount,2) }}
									</td>
									<td>
										@if($trans->type == 'booking')
										Booking/Deposit
										@else
										{{ ucwords($trans->type) }}
										@endif
									</td>

									<td>{{$trans->created_at->format('M d, Y - h:i A')}}</td>
									<td>
										@if($trans->status == "pending")
										<div class="badge badge-warning">Pending</div>
										@elseif($trans->status == "approved")
										<div class="badge badge-success">Approved</div>
										@elseif($trans->status == "paid")
										<div class="badge badge-success">Paid</div>
										@elseif($trans->status == "declined")
										<div class="badge badge-danger">Declined</div>
										@endif
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
