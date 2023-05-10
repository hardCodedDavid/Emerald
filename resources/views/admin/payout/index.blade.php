@php
use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.admin")

@section('title') Farm Payouts @endsection

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
                    <h4>Farm Payouts</h4>
                </div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-striped" id="table-1">
							<thead>
								<tr>
									<th class="text-center">
										#
									</th>
									<th>Name</th>
									<th>Action</th>
								</tr>
							</thead>

                            <tbody>
                                @foreach($categories as $key => $category)
                                    <tr>
                                        <td>{{$key + 1}}</td>
                                        <td>{{$category->name}}</td>
                                        <td>
                                            <div class="dropdown show">
                                                <a class="btn btn-success dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Action
                                                </a>

                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                    <a href="/admin/payouts/{{$category->id}}/farms" class="dropdown-item">View Farms</a>
                                                </div>
                                            </div>
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
