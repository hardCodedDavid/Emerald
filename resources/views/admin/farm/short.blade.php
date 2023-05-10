@php
use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.admin")

@section('title') Farm lists | {{ (isset($edit))?'Edit Farm list':'Add new' }} @endsection

@section('farmlists') active @endsection

@section('content')
<div class="section-body">
	<div class="row">
		<div class="col-12 col-md-6 col-lg-6">
			<div class="card">
				<div class="card-header">
					<h4>{{ (isset($edit))?'Edit':'New' }} Farm List</h4>
				</div>
				<div class="card-body">
					@if(isset($edit))
					<form action="{{ route('farmlist.edit') }}" method="post" enctype="multipart/form-data">
						@csrf
						<div class="form-group">
							<label>Title</label>
							<input type="text" class="form-control" name="title" required value="{{ $farmlist->title }}">
							<input type="hidden" value="{{ $farmlist->id }}" name="id">
						</div>
						<div class="form-group">
							<label>Cover</label>
							<img src="{{ asset($farmlist->cover) }}" width="200">
							<input type="file" class="form-control" name="cover">
						</div>
						<div class="form-group">
							<label>Start Date</label>
							<input type="datetime-local" class="form-control" name="start_date" required value="{{ date('Y-m-d\TH:i', strtotime($farmlist->edit_start_date)) }}">
						</div>
						<div class="form-group">
							<label>Close Date</label>
							<input type="datetime-local" class="form-control" name="close_date" required value="{{ date('Y-m-d\TH:i', strtotime($farmlist->edit_close_date)) }}">
						</div>
						<div class="form-group">
							<label>Price per unit</label>
							<input type="number" class="form-control" step="any" name="price" required value="{{ $farmlist->price }}" readonly>
						</div>
                        <div class="form-group">
                            <label>Interest rate </label>
                            <input type="number" class="form-control" name="interest" step="any" required value="{{ $farmlist->interest }}" readonly>
                        </div>
                        <div class="form-group">
                            <label>Maturity Date (Days)</label>
                            <input type="text" class="form-control" name="maturity_date" required value="{{ $farmlist->maturity_date }}" readonly>
                        </div>
                        <div class="form-group">
                            <label>Available Units</label>
                            <input type="text" class="form-control" name="available_units" required value="{{ $farmlist->available_units }}">
                        </div>
						<div class="form-group">
							<label>Description</label>
							<textarea class="form-control" name="description" required>{!! $farmlist->description !!}</textarea>
						</div>
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category_id" class="form-control" required>
                            @foreach(\App\Category::all() as $category)
                                    <option value="{{ $category->id }}" {{ ($category->id == $farmlist->category_id)?'selected':'' }}>{{ ucwords($category->name) }}</option>
                                @endforeach
                            </select>
                        </div>

						<div class="form-group">
							<button type="submit" class="btn btn-success btn-lg btn-block">Submit</button>
                        </div>
					</form>
					@else
					<form action="{{ route('farmlist.add') }}" method="post" enctype="multipart/form-data">
						@csrf
						<div class="form-group">
							<label>Title</label>
							<input type="text" class="form-control" name="title" required>
						</div>
						<div class="form-group">
							<label>Cover</label>
							<input type="file" class="form-control" name="cover" required>
						</div>
						<div class="form-group">
							<label>Start Date</label>
							<input type="datetime-local" class="form-control time" name="start_date" required>
						</div>
						<div class="form-group">
							<label>Close Date</label>
							<input type="datetime-local" class="form-control time" name="close_date" required>
						</div>
						<div class="form-group">
							<label>Price per unit</label>
							<input type="number" step="any" class="form-control" name="price" required>
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
                            <label>Category</label>
                            <select name="category_id" class="form-control" required>
                                <option value="null">Select Category</option>

                            @foreach(\App\Category::all() as $category)
                                    <option value="{{ $category->id }}">{{ ucwords($category->name) }}</option>
                                @endforeach
                            </select>
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

@section('foot')
<script>

     $(document).ready(function () {
         if(/^(iPhone|iPad|iPod)/.test(navigator.platform)){
             // get the iso time string formatted for usage in an input['type="datetime-local"']
            var tzoffset = (new Date()).getTimezoneOffset() * 60000; //offset in milliseconds
            var localISOTime = (new Date(Date.now() - tzoffset)).toISOString().slice(0,-1);
            var localISOTimeWithoutSeconds = localISOTime.slice(0,16);

            $('.time').val(localISOTimeWithoutSeconds);
         }
     });
</script>
@endsection
