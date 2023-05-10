@php
use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.admin")

@section('title') Long Farm lists @endsection

@section('head')
<link rel="stylesheet" href="{{ asset('assets/bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
<link href="//cdn.jsdelivr.net/npm/featherlight@1.7.14/release/featherlight.min.css" type="text/css" rel="stylesheet" />


@endsection

@section('farmlists') active @endsection

@section('content')
<div class="section-body">
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<h4>Long Term Package</h4>
                </div>
				<div class="card-body">
                    <a href="/admin/farmlist/long/add" class="btn btn-success mb-3">Create Long Term Package</a>
					<div class="table-responsive">
						<table class="table table-striped" id="table-1">
							<thead>
								<tr>
									<th class="text-center">
										#
									</th>
									<th>Title</th>
									<th>Cover</th>
                                    <th>Start Date</th>
                                    <th>Close Date</th>
                                    <th>Price / Unit</th>
                                    <th>Status</th>
                                    <th>Milestone</th>
                                    <th>Duration</th>
                                    <th>Available Units</th>
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
                "url": "{{ url('loadLongFarms') }}",
                "dataType": "json",
                "type": "POST",
                "data":{ _token: "{{csrf_token()}}"}
            },
            "columns": [
                { "data": "sn" },
                { "data": "title"},
                { "data": "cover" },
                { "data": "start_date" },
                { "data": "close_date" },
                { "data": "price" },
                { "data": "status" },
                { "data": "milestone" },
                { "data": "duration" },
                { "data": "units" },
                { "data": "actions" },
            ],
            "lengthMenu": [[100, 200, 300, 400], [100, 200, 300, 400]]
        });
    });
</script>

@endsection
