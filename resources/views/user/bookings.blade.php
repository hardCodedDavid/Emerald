@php
use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.user")

@section('title') Bookings @endsection

@section('head')
<link rel="stylesheet" href="{{ asset('assets/bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection

@section('bookings') active @endsection

@section('content')
<div class="section-body">
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<h4>Bookings</h4>
                    <a href="#" class="badge badge-success" data-toggle="modal" data-target="#booking">Create</a>
                </div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-striped" id="table-1">
							<thead>
								<tr>
									<th class="text-center">
										#
									</th>
									<th>Category</th>
									<th>Description</th>
									<th>Status</th>
									<th>Action</th>
 								</tr>
							</thead>
							<tbody>

								@foreach(auth()->user()->bookings as $key => $booking)
								<tr>
									<td>
										{{ $key + 1}}
									</td>

									<td>{{$booking->category->name }}</td>
									<td>{{$booking->category->description }}</td>

									<td>
										@if($booking->status == "pending")
										    <div class="badge badge-warning">Pending</div>
										@elseif($booking->status == "sponsored")
										    <div class="badge badge-success">Sponsored</div>
										@endif
									</td>
                                    <td>
                                        @if($booking->status != "sponsored")
                                            <a href="{{route('bookings.delete', $booking->id)}}" class="btn btn-danger">Delete</a>
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
