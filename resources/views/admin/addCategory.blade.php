@php
use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.admin")

@section('title') Category | {{ (isset($edit))?'Edit category':'Add new' }} @endsection

@section('category') active @endsection

@section('content')
<div class="section-body">
	<div class="row">
		<div class="col-12 col-md-6 col-lg-6">
			<div class="card">
				<div class="card-header">
					<h4>{{ (isset($edit))?'Edit':'New' }} Category</h4>
				</div>
				<div class="card-body">
					@if(isset($edit))
					<form action="{{ route('category.edit') }}" method="post">
						@csrf
                        <div class="form-group">
                            <label>Category Name</label>
                            <input type="text" class="form-control" name="name" value="{{$category->name}}" required>
                            <input type="hidden" value="{{ $category->id }}" name="id">

                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" required>{{$category->description}}</textarea>
                        </div>
						<div class="form-group">
							<button type="submit" class="btn btn-success btn-lg btn-block">Submit</button>
                        </div>
					</form>
					@else
					<form action="{{ route('category.add') }}" method="POST">
						@csrf

						<div class="form-group">
							<label>Category Name</label>
							<input type="text" class="form-control" name="name" required>
						</div>
						<div class="form-group">
							<label>Description</label>
							<textarea class="form-control" name="description" required></textarea>
						</div>
						<div class="form-group">
							<button type="submit" class="btn btn-success btn-lg btn-block">Submit</button>
                        </div>
					</form>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
