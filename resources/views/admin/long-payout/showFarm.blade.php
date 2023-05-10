@php
use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.admin")

@section('title') Farm Lists @endsection

@section('head')
<link rel="stylesheet" href="{{ asset('assets/bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
<link href="//cdn.jsdelivr.net/npm/featherlight@1.7.14/release/featherlight.min.css" type="text/css" rel="stylesheet" />


@endsection

@section('payouts') active @endsection

@section('content')
<div class="section-body">
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<h4>Payout | Investments</h4>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-striped" id="table-1">
							<thead>
                            <tr>
                                <th>
                                    #
                                </th>
                                <th>User</th>
                                <th>Amount</th>
                                <th>Maturity Date</th>
                                <th>Milestone</th>
                                <th>Maturity Status</th>
                                <th>Units</th>
                                <th>Action</th>
                            </tr>
							</thead>


                            <tbody>
                                @php
                                    $i = 1;
                                @endphp
                                @foreach($investments as $invest)
                                <tr>
                                    <td>
                                        {{ $i++ }}
                                    </td>
                                    <td>{{  $invest->user->email }}</td>
                                    <td>
                                        ₦{{ number_format($invest->amount_invested,2) }}
                                    </td>
                                    <td>
                                        {{ count($invest->payments) < count($invest->milestoneDates()) ? $invest->milestoneDates()[count($invest->payments)]->format('d M, Y') : 'Fully paid' }}
                                    </td>
                                    <td>{{ count($invest->payments).'/'.count($invest->milestoneDates()) }}</td>
                                    <td>
                                        @if(strtotime($invest->approved_date.'+ '.$invest->getPaymentDurationInDays(). 'days') < strtotime(now()))
                                            <span class="badge badge-success">completed</span>
                                        @else
                                            <span class="badge badge-warning">pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $invest->units }}
                                    </td>
                                    <td>
                                        <a href="{{route('admin-long-investment.show', $invest->id)}}" class="btn btn-success">View Investment</a>
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
<script src="//cdn.jsdelivr.net/npm/featherlight@1.7.14/release/featherlight.min.js" type="text/javascript" charset="utf-8"></script>

{{--<script>--}}
{{--    $(document).ready(function () {--}}
{{--        $('#table-1').DataTable({--}}
{{--            "processing": true,--}}
{{--            "serverSide": true,--}}
{{--            "searching": true,--}}
{{--            "ajax":{--}}
{{--                "url": "{{ url('loadPayout') }}",--}}
{{--                "dataType": "json",--}}
{{--                "type": "POST",--}}
{{--                "data":{ _token: "{{csrf_token()}}"}--}}
{{--            },--}}
{{--            "columns": [--}}
{{--                { "data": "sn" },--}}
{{--                { "data": "title"},--}}
{{--                { "data": "cover" },--}}
{{--                { "data": "status" },--}}
{{--                { "data": "actions" },--}}
{{--            ],--}}
{{--            "lengthMenu": [[100, 200, 300, 400], [100, 200, 300, 400]]--}}
{{--        });--}}
{{--    });--}}
{{--</script>--}}

@endsection
