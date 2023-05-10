@php
use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.user")

@section('title') Farm lists @endsection

@section('head')
<link rel="stylesheet" href="{{ asset('assets/bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
<link href="//cdn.jsdelivr.net/npm/featherlight@1.7.14/release/featherlight.min.css" type="text/css" rel="stylesheet" />
<style>
    img{
        box-shadow: 0 5px 15px 0 rgba(105,103,103,0.5);
        border: 2px solid #ffffff;
        border-radius: 10px;
        margin: 10px 0;
    }
</style>
@endsection

@section('farmlist') active @endsection

@section('content')
<div class="section-body">
	<div class="row">
		<div class="col-12">
			<div class="card mb-0">
				<div class="card-body">
					<ul class="nav nav-pills">
						<li class="nav-item">
							<a class="nav-link {{ (isset($all))?'active':'' }}" href="/farmlist">All <span class="badge badge-{{ (isset($all))?'white':'primary' }}">{{ count($farmlists) }}</span></a>
						</li>
						<li class="nav-item">
							<a class="nav-link {{ (isset($pend))?'active':'' }}" href="/farmlist/pending">Coming soon <span class="badge badge-{{ (isset($pend))?'white':'primary' }}">{{ count($pending) }}</span></a>
						</li>
						<li class="nav-item">
							<a class="nav-link {{ (isset($close))?'active':'' }}" href="/farmlist/closed">Closed <span class="badge badge-{{ (isset($close))?'white':'primary' }}">{{ count($closed) }}</span></a>
						</li>
						<li class="nav-item">
							<a class="nav-link {{ (isset($open))?'active':'' }}" href="/farmlist/opened">Opened <span class="badge badge-{{ (isset($open))?'white':'primary' }}">{{ count($opened) }}</span></a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div><br><br><br>

	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<h4>Farm lists</h4>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-striped" id="table-1">
							<thead>
								<tr>
									<th class="text-center">
										#
									</th>
									<th>Title</th>
									<th>Cover</th>
									<th>Price / unit</th>
									<th>Status</th>
                                    <th>Maturity Date</th>
                                    <th>Available Units</th>
                                    <th>Farm Opening Date</th>
                                    <th>Investment Start Date</th>
									<th>Action</th>
								</tr>
							</thead>
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

<script>
    $(document).ready(function () {
        $('#table-1').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": true,
            "ajax":{
                "url": "{{ url('loadFarms/'. $type) }}",
                "dataType": "json",
                "type": "POST",
                "data":{ _token: "{{csrf_token()}}"}
            },
            "columns": [
                { "data": "sn" },
                { "data": "title"},
                { "data": "cover" },
                { "data": "price" },
                { "data": "status" },
                { "data": "maturity_date" },
                { "data": "units" },
                { "data": "farm_opening_date" },
                { "data": "investment_start_date" },
                { "data": "actions" },
            ]

        });
    });
</script>

@endsection
