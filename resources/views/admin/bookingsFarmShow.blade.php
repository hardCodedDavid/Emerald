@php
use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.admin")

@section('title') Bookings @endsection

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
					<h4>Bookings for {{$farmlist->title}}</h4>
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
									<th>Status</th>
									<th>Units</th>
									<th>Category</th>
									<th>Rollover</th>
									<th>Date</th>
									<th>Action</th>
 								</tr>
							</thead>

                            <tbody>
                                @foreach($farmlist->bookings as $key => $booking)
                                    <tr>
                                        <td>{{$key + 1}}</td>
                                        <td><a href="/admin/users/view/{{ $booking->user->id }}">{{$booking->user->name}}</a></td>
                                        <td>NGN {{$booking->amount}}</td>
                                        <td>
                                            @if($booking->status == 'pending')
                                                <div class="badge badge-warning">{{ucwords($booking->status)}}</div>
                                            @elseif($booking->status == 'approved' || $booking->status == 'sponsored')
                                                <div class="badge badge-success">{{ucwords($booking->status)}}</div>
                                            @elseif($booking->status == 'declined')
                                                <div class="badge badge-danger">{{ucwords($booking->status)}}</div>
                                            @endif
                                        </td>
                                        <td>{{$booking->units}}</td>
                                        <td>{{$booking->category->name}}</td>
                                        <td>{{$booking->rollover == 1 ? 'Yes' : 'No'}}</td>
                                        <td>{{$booking->created_at->format('M d, Y')}}</td>
                                        <td>
                                            @if($booking->status == 'pending')

                                            <div class="dropdown show">
                                                <a class="btn btn-success dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Action
                                                </a>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                        <a href="/admin/bookings/{{$booking->id}}/approve" class="dropdown-item" onclick="return confirm('Are you sure you want to approve this booking?');">Approve</a>
                                                        <a href="/admin/bookings/{{$booking->id}}/decline" class="dropdown-item" onclick="return confirm('Are you sure you want to decline this booking?');">Decline</a>
                                                </div>
                                            </div>
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
@endsection
