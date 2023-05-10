@extends('layouts.user')

@section('title', __('My Notifications'))

@section('head')
    <link rel="stylesheet" href="{{ asset('assets/bundles/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
    <link href="//cdn.jsdelivr.net/npm/featherlight@1.7.14/release/featherlight.min.css" type="text/css" rel="stylesheet" />
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		<div class="card m-b-30">
			<div class="card-body">
                <div class="d-sm-flex align-items-center justify-content-between">
                    <h4 class="mt-0 header-title">All Notifications</h4>
                    <a class="btn btn-primary" href="/notifications/myaction/viewall">Mark All As Read</a>
                </div>
				<p class="sub-title">
				</p>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th><a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown">Action </a></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($notifications as $entity)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{!! $entity->data['title'] !!}</td>
                                <td>
                                    @if($entity->read_at)
                                        <span class="badge badge-success">Read</span>
                                    @else
                                        <span class="badge badge-warning">Unread</span>
                                    @endif
                                </td>
                                <td style="white-space: nowrap">{{ $entity->created_at->format('d F, Y h:i A') }}</td>
                                <td><a class="btn btn-primary" href="/notifications/{{ $entity->id }}"><span style="white-space: nowrap"> View notification</span></a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div style="width: 100%; overflow-x: auto;">
                    {{ $notifications->links() }}
                </div>
			</div>
		</div>
	</div>
	<!-- end col -->
</div>
<!-- end row -->
@endsection

@section('foot')
    <script src="{{ asset('assets/bundles/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/bundles/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="//cdn.jsdelivr.net/npm/featherlight@1.7.14/release/featherlight.min.js" type="text/javascript" charset="utf-8"></script>

    <script>
        $(document).ready(function () {
            $('#table-1').DataTable();
        });
    </script>

@endsection
