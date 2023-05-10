@php
use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.admin")

@section('title') Package | {{ (isset($edit))?'Edit package':'Add new' }} @endsection

@section('packages') active @endsection

@section('content')
<div class="section-body">
	<div class="row">
		<div class="col-12 col-md-6 col-lg-6">
			<div class="card">
				<div class="card-header">
					<h4>{{ (isset($edit))?'Edit':'New' }} Package</h4>
				</div>
				<div class="card-body">
					@if(isset($edit))
					<form action="{{ route('package.edit') }}" method="post">
						@csrf
						<div class="form-group">
							<label>Package Name</label>
							<input type="text" class="form-control" name="name" value="{{ $package->name }}" required>
							<input type="hidden" value="{{ $package->id }}" name="id">
						</div>
						<div class="form-group">
							<label>Interest rate </label>
							<input type="number" class="form-control" name="interest" step="any" required value="{{ $package->interest }}">
						</div>
						<div class="form-group">
							<label>Maturity Date (Days)</label>
							<input type="text" class="form-control" name="maturity_date" required value="{{ $package->maturity_date }}">
						</div>
						<div class="form-group">
							<label>Available Units</label>
							<input type="text" class="form-control" name="available_units" required value="{{ $package->available_units }}">
						</div>
						<div class="form-group">
							<label>Description</label>
							<textarea class="form-control" name="description" required>{{ $package->description }}</textarea>
						</div>
						<div class="form-group">
							<button type="submit" class="btn btn-success btn-lg btn-block">Submit</button>
					</form>
					@else
					<form action="{{ route('package.add') }}" method="post">
						@csrf
						<div class="form-group">
							<label>Package Name</label>
							<input type="text" class="form-control" name="name" required>
						</div>
						<div class="form-group">
							<label>Interest rate </label>
							<input type="number" class="form-control" name="interest" step="any" required>
						</div>
						<div class="form-group">
							<label>Maturity Date (Days)</label>
							<input type="text" class="form-control" name="maturity_date" required>
						</div>
						<div class="form-group">
							<label>Available Units</label>
							<input type="text" class="form-control" name="available_units" required>
						</div>
						<div class="form-group">
							<label>Description</label>
							<textarea class="form-control" name="description" required></textarea>
						</div>
						<div class="form-group">
							<button type="submit" class="btn btn-success btn-lg  btn-block">Submit</button>
					</form>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>

@endsection