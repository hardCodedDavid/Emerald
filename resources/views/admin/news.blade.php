@php
use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.admin")

@section('title') News Updates @endsection

@section('head')
<link rel="stylesheet" href="{{ asset('assets/bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
<style>
    .theme-white .btn-primary:hover {
        background-color: red !important;
        color: #fff;
    }
</style>
@endsection

@section('news') active @endsection

@section('content')
<div class="section-body">
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<h4>News</h4>
					<div class="float-right">
					    <a href="/admin/news/add" class="btn btn-primary">Add Update</a>
					</div>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-striped" id="table-1">
							<thead>
								<tr>
									<th class="text-center">
										#
									</th>
									<th>Farmlist</th>
									<th>Title</th>
									<th>Date Posted</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								@php
								$i = 1;
								@endphp
								@foreach($news as $new)
								@php
								$farmlist = Util::getFarmlist($new->farmlist);
								@endphp
								<tr>
									<td>
										{{ $i++ }}
									</td>
									<td><img src="{{ asset($farmlist->cover) }}" width="200"><br>{{ ucwords($farmlist->title) }}</td>
									<td>
										{{ ucwords($new->title) }}
									</td>
									<td>
										{{ date('F-d-Y', strtotime($new->created_at)) }}
									</td>
									<td>
                                        <div class="dropdown show">
                                            <a class="btn btn-success dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Actions
                                            </a>

                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                <a href="/admin/news/edit/{{ $new->slug }}" class="dropdown-item">Edit</a>
                                                <a href="/admin/news/delete/{{ $new->slug }}" onclick="return confirm('Are you sure you want to delete this update?');" class="dropdown-item">Delete</a>
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
<script src="{{ asset('assets/js/page/datatables.js') }}"></script>
@endsection
