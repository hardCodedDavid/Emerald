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
					<h4>Payout | Farmlists</h4>
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
                                <th>Status</th>
                                <th>Investment Count</th>
                                <th>Action</th>
                            </tr>
							</thead>


                            <tbody>
                                @foreach($farmlists as $key => $farmlist)
                                    <tr>
                                        <td>{{$key + 1}}</td>
                                        <td>{{$farmlist->title}}</td>
                                        <td>
                                            <a href="{{asset($farmlist->cover)}}" data-featherlight="image" data-title="' .$post->title. '">
                                                <img src="{{asset('assets/uploads/courses/farmlist.png')}}" alt="{{$farmlist->title}}" width="70">
                                            </a>
                                        </td>
                                        <td>
                                            @if($farmlist->isOpen())
                                                <div class="badge badge-success">Open</div>
                                            @elseif($farmlist->isClosed())
                                                <div class="badge badge-danger">Closed</div>
                                            @elseif($farmlist->available_units < 1)
                                                <div class="badge badge-danger">Sold Out</div>
                                            @else
                                                <div class="badge badge-warning">Coming Soon</div>
                                            @endif
                                        </td>
                                        <td>
                                            {{$farmlist->investments->count()}}
                                        </td>
                                        <td>
                                            <div class="dropdown show">
                                                <a class="btn btn-success dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Action
                                                </a>

                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                    <a href="/admin/payouts/{{$farmlist->category->id}}/farms/{{$farmlist->id}}/payouts" class="dropdown-item">View Investments</a>
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
