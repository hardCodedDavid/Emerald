@php
use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.admin")

@section('title') Dashboard @endsection

@section('dashboard') active @endsection

@section('content')
<div class="row ">
    <div class="col-12 mb-3">
        <button id="showBtn" onclick="toggleAmountVisibility()" class="btn float-right btn-success">Show Details</button>
        <button id="hideBtn" onclick="toggleAmountVisibility()" class="btn d-none float-right btn-success">Hide Details</button>
    </div>
	<div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<div class="card">
			<div class="card-statistic-4">
				<div class="align-items-center justify-content-between">
					<div class="row ">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
							<div class="card-content">
								<h5 class="font-15">All FarmLists</h5>
								<h2 class="mb-3 font-18">{{ number_format($farmLists) }}</h2>
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
							<div class="banner-img">
								<img src="assets/img/banner/1.png" alt="">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<div class="card">
			<div class="card-statistic-4">
				<div class="align-items-center justify-content-between">
					<div class="row ">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
							<div class="card-content">
								<h5 class="font-15"> Users</h5>
								<h2 class="mb-3 font-18">{{ number_format($users) }}</h2>
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
							<div class="banner-img">
								<img src="assets/img/banner/2.png" alt="">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<div class="card">
			<div class="card-statistic-4">
				<div class="align-items-center justify-content-between">
					<div class="row ">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
							<div class="card-content">
								<h5 class="font-15">Packages</h5>
								<h2 class="mb-3 font-18">{{ number_format($packages) }}</h2>
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
							<div class="banner-img">
								<img src="assets/img/banner/3.png" alt="">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<div class="card">
			<div class="card-statistic-4">
				<div class="align-items-center justify-content-between">
					<div class="row ">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
							<div class="card-content">
								<h5 class="font-15">Wallet</h5>
								<h2 class="mb-3 tagToHide font-18">₦{{ number_format($wallet,2) }}</h2>
								<h2 class="mb-3 tagToHideSub font-18">₦ -------</h2>
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
							<div class="banner-img">
								<img src="assets/img/banner/4.png" alt="">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<div class="card">
			<div class="card-statistic-4">
				<div class="align-items-center justify-content-between">
					<div class="row ">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
							<div class="card-content">
								<h5 class="font-15">Emerald Bank</h5>
								<h2 class="mb-3 tagToHide font-18">₦{{ number_format($emeraldbank,2) }}</h2>
                                <h2 class="mb-3 tagToHideSub font-18">₦ -------</h2>
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
							<div class="banner-img">
								<img src="assets/img/banner/4.png" alt="">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<div class="card">
			<div class="card-statistic-4">
				<div class="align-items-center justify-content-between">
					<div class="row ">
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
							<div class="card-content">
								<h5 class="font-15">Withdrawable Funds</h5>
								<h2 class="mb-3 tagToHide font-18">₦{{ number_format($withdrawable,2) }}</h2>
                                <h2 class="mb-3 tagToHideSub font-18">₦ -------</h2>
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
							<div class="banner-img">
								<img src="assets/img/banner/4.png" alt="">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h4>Recent Users</h4>
				<div class="card-header-form">
					<form>
						<div class="input-group">
							<input type="text" class="form-control" placeholder="Search">
							<div class="input-group-btn">
								<button class="btn btn-primary"><i class="fas fa-search"></i></button>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="card-body p-0">
				<div class="table-responsive">
					<table class="table table-striped">
						<tr>
							<th class="text-center">
								<div class="custom-checkbox custom-checkbox-table custom-control">
									<input type="checkbox" data-checkboxes="mygroup" data-checkbox-role="dad"
										class="custom-control-input" id="checkbox-all">
									<label for="checkbox-all" class="custom-control-label">&nbsp;</label>
								</div>
							</th>
							<th>User</th>
							<th>Name</th>
							<th>Email</th>
							<th>Date Joined</th>
							<th>Status</th>
						</tr>
						@foreach($recent as $rec)
						<tr>
							<td class="p-0 text-center">
								<div class="custom-checkbox custom-control">
									<input type="checkbox" data-checkboxes="mygroup" class="custom-control-input"
										id="checkbox-1">
									<label for="checkbox-1" class="custom-control-label">&nbsp;</label>
								</div>
							</td>
							<td class="text-truncate">
								<ul class="list-unstyled order-list m-b-0 m-b-0">
									<li class="team-member team-member-sm"><img class="rounded-circle"
										src="{{ Util::getPassport($rec) }}" alt="user" data-toggle="tooltip" title="" data-original-title="{{ $rec->name }}"></li>
								</ul>
							</td>
							<td class="align-middle">{{ ucwords($rec->name) }}</td>
							<td>{{ strtolower($rec->email) }}</td>
							<td>
								{{ date('M d, Y h:i A', strtotime($rec->created_at)) }}
							</td>
							<td>
								@if($rec->email_verified_at != '')
								<div class="badge badge-success">Verified</div>
								@else
								<div class="badge badge-warning">Not verified</div>
								@endif
							</td>
						</tr>
						@endforeach
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('foot')
    <script>
        document.querySelectorAll('.tagToHide').forEach(e => {
            e.classList.add('d-none');
        });
        document.querySelectorAll('.tagToHideSub').forEach(e => {
            e.classList.remove('d-none');
        });
        function toggleAmountVisibility(){
            document.getElementById('showBtn').classList.toggle('d-none');
            document.getElementById('hideBtn').classList.toggle('d-none');
            document.querySelectorAll('.tagToHide').forEach(e => {
                e.classList.toggle('d-none');
            });
            document.querySelectorAll('.tagToHideSub').forEach(e => {
                e.classList.toggle('d-none');
            });
        }
    </script>
@endsection
