@php
use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.admin")

@section('title')
	@if(isset($verified))
	Verified Users
	@elseif(isset($unverified))
	Unverified Users
	@else
	All Users
	@endif
@endsection

@section('head')
<link rel="stylesheet" href="{{ asset('assets/bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection

@section('users') active @endsection

@section('content')
<div class="section-body">
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<h4>Users</h4>

                    @php

                        $route = 'users';

                        if(request()->is('admin/users/verified')){
                            $route = 'verified';
                        }

                        if(request()->is('admin/users/unverified')){
                            $route = 'unverified';
                        }

                    @endphp

                    <a href="{{route('download.transactions',$route)}}" class="badge badge-success">Export Excel</a>

                </div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-striped" id="userTable">
							<thead>
								<tr>
									<th class="text-center">
										#
									</th>
									<th>Name</th>
									<th>Email</th>
									<th>Phone</th>
									<th>Status</th>
									<th>Date Joined</th>
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
@if(isset($verified))
<script>
    $(document).ready(function () {
        $('#userTable').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": true,
            "ajax":{
                     "url": "{{ url('loadVerifiedUser') }}",
                     "dataType": "json",
                     "type": "POST",
                     "data":{ _token: "{{csrf_token()}}"}
                   },
            "columns": [
                { "data": "sn" },
                { "data": "name" },
                { "data": "email" },
                { "data": "phone" },
                { "data": "status" },
                { "data": "date_joined" },
                { "data": "action" }
            ],
            "lengthMenu": [[100, 200, 300, 400], [100, 200, 300, 400]]

        });
    });
</script>
@elseif(isset($unverified))
<script>
    $(document).ready(function () {
        $('#userTable').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": true,
            "ajax":{
                     "url": "{{ url('loadUnverifiedUser') }}",
                     "dataType": "json",
                     "type": "POST",
                     "data":{ _token: "{{csrf_token()}}"}
                   },
            "columns": [
                { "data": "sn" },
                { "data": "name" },
                { "data": "email" },
                { "data": "phone" },
                { "data": "status" },
                { "data": "date_joined" },
                { "data": "action" }
            ],
            "lengthMenu": [[100, 200, 300, 400], [100, 200, 300, 400]]

        });
    });
</script>
@else
<script>
    $(document).ready(function () {
        $('#userTable').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": true,
            "ajax":{
                     "url": "{{ url('loadAllUser') }}",
                     "dataType": "json",
                     "type": "POST",
                     "data":{ _token: "{{csrf_token()}}"}
                   },
            "columns": [
                { "data": "sn" },
                { "data": "name" },
                { "data": "email" },
                { "data": "phone" },
                { "data": "status" },
                { "data": "date_joined" },
                { "data": "action" }
            ],
            "lengthMenu": [[100, 200, 300, 400], [100, 200, 300, 400]]

        });
    });
</script>
@endif

@endsection
