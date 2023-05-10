@php
use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.admin")

@section('title') Investments @endsection

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
					<h4>Investments</h4>
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
									<th>Farmlist</th>
									<th>Maturity Date</th>
									<th>Maturity Status</th>
									<th>Units</th>
									<th>Days Remaining</th>
									<th>Expected Returns</th>
									<th>Date</th>
									<th>Status</th>
									<th>Rollover</th>
 								</tr>
							</thead>
							<tbody>
								@php
								$i = 1;
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
                                        <td>
                                            ₦{{ number_format($invest->amount_invested,2) }}
                                        </td>
                                        <td>{{ ucwords(Util::getFarmlist($invest->farmlist)->title) }}</td>
                                        <td>
                                            @if($invest->maturity_date != null)
                                            {{ date('M d, Y h:i A', strtotime($invest->maturity_date)) }}
                                            @else
                                                <div class="badge badge-warning py-2 px-2">pending</div>
                                            @endif
                                            </td>
                                        <td>
                                            <div class="badge @if($invest->maturity_status == 'matured') badge-primary @elseif($invest->maturity_status == 'pending') badge-warning @endif py-2 px-2">{{ucwords($invest->maturity_status) }}</div>
                                        </td>
                                        <td>{{ $invest->units }}</td>
                                        <td>
                                            @if($invest->maturity_date == null)
                                                0
                                            @elseif($invest->maturity_status == 'pending')
                                                {{ round((($diff/24)/60)/60) }}
                                            @else
                                                0
                                            @endif
                                        </td>
                                        <td>
                                            @if($invest->maturity_status == 'pending')
                                                ₦{{ number_format( $add, 2)}}
                                            @else
                                                ₦{{ number_format($add, 2) }}
                                            @endif
                                        </td>
                                        <td>{{$invest->created_at->format('M d, Y')}}</td>
                                        <td><div class="badge @if($invest->status == 'active' || $invest->status == 'approve') badge-primary @elseif($invest->status == 'pending') badge-warning @elseif($invest->status == 'decline' || $invest->status == 'closed') badge-danger @endif py-2 px-2">{{ucwords($invest->status) }}</div></td>
                                        <td>
                                            @if($invest->rollover == 1)
                                                Yes
                                            @else
                                                No
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


