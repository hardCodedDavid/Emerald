@extends('layouts.user')

@section('title', __('View notification'))

@section('bread')
<div class="page-title-box">
	<div class="row align-items-center">
		<div class="col-sm-6">
			<h4 class="page-title">Notifications</h4>
		</div>
		<div class="col-sm-6">
			<ol class="breadcrumb float-right">
				<li class="breadcrumb-item"><a href="/">City Fresh Farms</a></li>
				<li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
				<li class="breadcrumb-item"><a href="/notifications">Notifications</a></li>
				<li class="breadcrumb-item active">Read</li>
			</ol>
		</div>
	</div>
</div>
@endsection

@section('content')
<div class="row">
	<div class="col-lg-6">
		<div class="email mb-3">
			<div class="card">
				<div class="card-header">
					<h4>{!! json_decode($notification->data)->title !!}</h4>
				</div>
				<div class="card-body">
					<p>{!! json_decode($notification->data)->body !!}</p>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection