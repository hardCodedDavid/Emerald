@php
use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.user")

@section('title') New Updates @endsection

@section('head')
<link rel="stylesheet" href="{{ asset('assets/bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection

@section('news') active @endsection

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
									<th>Farmlists</th>
									<th>News</th>
									<th>Last Updated</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								@php
								$i = 1;
								@endphp
								@foreach($farmlists as $farmlist)
								@if(Util::getNewsCounts($farmlist->slug) > 0)
								<tr>
									<td>
										{{ $i++ }}
									</td>
									<td>{{ ucwords($farmlist->title) }}</td>
									<td>
										{{ Util::getNewsCounts($farmlist->slug) }}
									</td>
									<td>
									    @if(Util::getNewsCounts($farmlist->slug) > 0)
										{{ date('F d, Y', strtotime(Util::getLastUpdated($farmlist->slug))) }}
										@endif
									</td>
									<td>
										<a href="/news/list/{{ $farmlist->slug }}" class="btn btn-primary">View</a>
									</td>
								</tr>
								@endif
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
