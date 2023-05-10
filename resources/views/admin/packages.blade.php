@php
use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.admin")

@section('title') Packages @endsection

@section('head')
<link rel="stylesheet" href="{{ asset('assets/bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection

@section('packages') active @endsection

@section('content')
<div class="section-body">
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<h4>Packages</h4>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-striped" id="table-1">
							<thead>
								<tr>
									<th class="text-center">
										#
									</th>
									<th>Package name</th>
									<th>Interest</th>
									<th>Maturity Date</th>
									<th>Available Units</th>
									<th>Description</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								@php
								$i = 1;
								@endphp
								@foreach($packages as $package)
								<tr>
									<td>
										{{ $i++ }}
									</td>
									<td>{{ ucwords($package->name) }}</td>
									<td>
										{{ $package->interest }}% 
									</td>
									<td>
										{{ $package->maturity_date }} days
									</td>
									<td>
										{{ $package->available_units }} units
									</td>
									<td>
										{{ $package->description }}
									</td>
									<td><a href="/admin/packages/delete/{{ $package->slug }}" onclick="return confirm('Are you sure you want to delete this package?');" class="btn btn-danger">Delete</a><a href="/admin/packages/edit/{{ $package->slug }}" class="btn btn-warning ml-2">Edit</a></td>
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