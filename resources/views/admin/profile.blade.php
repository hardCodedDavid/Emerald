@php
use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.admin")

@section('title') Profile @endsection

@section('content')
<div class="row mt-sm-4">
	<div class="col-12 col-md-12 col-lg-4">
		<div class="card author-box">
			<div class="card-body">
				<div class="author-box-center">
					<img alt="image" src="{{ Util::getPassport($user) }}" class="rounded-circle author-box-picture">
					<div class="clearfix"></div>
					<div class="author-box-name">
						<a href="#">{{ ucwords($user->name) }}</a>
					</div>
					<div class="author-box-job">{{ strtolower($user->email) }}</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-12 col-md-12 col-lg-8">
		<div class="card">
			<div class="padding-20">
				<ul class="nav nav-tabs" id="myTab2" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" id="profile-tab2" data-toggle="tab" href="#settings" role="tab"
							aria-selected="false">Profile</a>
					</li>
				</ul>
				<div class="tab-content tab-bordered" id="myTab3Content">
					<div class="tab-pane fade show active" id="settings" role="tabpanel" aria-labelledby="profile-tab2">
						<form method="post" action="{{ route('admin.profile.edit') }}" enctype="multipart/form-data">
							@csrf
							<div class="card-header">
								<h4>Edit Profile</h4>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="form-group col-md-12 col-12">
										<label>Name</label>
										<input type="text" class="form-control" value="{{ $user->name }}" name="name">
										<input type="hidden" name="id" value="{{ $user->id }}">
									</div>
								</div>
								<div class="row">
									<div class="form-group col-md-12 col-12">
										<label>Email</label>
										<input type="email" class="form-control" value="{{ $user->email }}" name="email" readonly="">
									</div>
								</div>
								<div class="row">
									<div class="form-group col-12 col-md-12">
										<label>Passport</label>
										<input type="file" name="passport" class="form-control">
									</div>
								</div>
							</div>
							<div class="card-footer text-right">
								<button type="submit" class="btn btn-success">Save Changes</button>
							</div>
						</form>
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