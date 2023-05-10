@php
use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.admin")

@section('title') Farm lists | {{ (isset($edit))?'Edit Long Farm list':'Add New Long' }} @endsection

@section('farmlists') active @endsection

@section('content')
<div class="section-body">
	<div class="row">
		<div class="col-12 col-md-6 col-lg-6">
			<div class="card">
				<div class="card-header">
					<h4>{{ (isset($edit))?'Edit Long Term':'New Long Term' }} Farm List</h4>
				</div>
				<div class="card-body">
					@if(isset($edit))
					    <form action="{{ route('farmlist.long.edit') }}" method="post" enctype="multipart/form-data">
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
                        <div class="form-row" id="milestonesDataField">
							<div class="col-12 form-group mb-1">
								<label>Milestones </label>
							</div>
							<div class="col-12" id="milestones">
							@php
								$interests = json_decode($farmlist->interest);
							@endphp
							@if ($farmlist->milestone > 0)
								@for ($i = 0; $i < $farmlist->milestone; $i++)
									<div class="row">
										<div class="col-10 input-group mb-1">
											<input type="number" value="{{ $interests[$i] ?? '' }}" placeholder="Interest Rate" class="form-control" name="milestones[]" required>
											<div class="input-group-append">
												<div class="input-group-text">
													<i class="fas fa-percentage"></i>
												</div>
											</div>
										</div>
										<div class="col-2 pb-1 mb-1 d-flex justify-content-end align-items-end form-group">
											<button type="button" class="btn milestone-field-remove btn-danger"><i class="fa fa-trash"></i></button>
										</div>
									</div>
								@endfor
							@else
								<div class="row">
									<div class="col-10 input-group mb-1">
										<input type="number" placeholder="Interest Rate" class="form-control" name="milestones[]" required>
										<div class="input-group-append">
											<div class="input-group-text">
												<i class="fas fa-percentage"></i>
											</div>
										</div>
									</div>
									<div class="col-2 pb-1 mb-1 d-flex justify-content-end align-items-end form-group">
										<button type="button" class="btn milestone-field-remove btn-danger"><i class="fa fa-trash"></i></button>
									</div>
								</div>
							@endif
							</div>
							<div class="col-12 mt-1 mb-2 text-right">
								<button id="addMilestoneBtn" type="button" class="btn btn-success">Add Milestone <i class="ml-2 fa fa-plus"></i></button>
							</div>
                        </div>
                        <div class="form-group">
                            <label>Duration in months</label>
                            <input type="numeric" class="form-control" name="duration" required value="{{ $farmlist->duration}}" readonly>
                        </div>
                        <div class="form-group">
                            <label>Available Units</label>
                            <input type="number" class="form-control" name="available_units" required value="{{ $farmlist->available_units }}">
                        </div>
						<div class="form-group">
							<label>Description</label>
							<textarea class="form-control" name="description" required>{!! $farmlist->description !!}</textarea>
						</div>
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category_id" class="form-control" required>
                                @php
                                    $category = \App\Category::where('name', 'Longterm Investment')->first();
                                @endphp
                                @if($category)
                                    <option selected value="{{ $category->id }}">{{ ucwords($category->name) }}</option>
                                @else
                                    <option selected value="">Select Category</option>
                                @endif
                            </select>
                        </div>

						<div class="form-group">
							<button type="submit" class="btn btn-success btn-lg btn-block">Submit</button>
                        </div>
					</form>
					@else
					    <form action="{{ route('farmlist.add.long') }}" method="post" enctype="multipart/form-data">
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
						<div class="form-row" id="milestonesDataField">
							<div class="col-12 form-group mb-1">
								<label>Milestones </label>
							</div>
							<div class="col-12" id="milestones">
								<div class="row">
									<div class="col-10 input-group mb-1">
										<input type="number" placeholder="Interest Rate" class="form-control" name="milestones[]" required>
										<div class="input-group-append">
											<div class="input-group-text">
												<i class="fas fa-percentage"></i>
											</div>
										</div>
									  </div>
									<div class="col-2 pb-1 mb-1 d-flex justify-content-end align-items-end form-group">
										<button type="button" class="btn milestone-field-remove btn-danger"><i class="fa fa-trash"></i></button>
									</div>
								</div>
							</div>
							<div class="col-12 mt-1 mb-2 text-right">
								<button id="addMilestoneBtn" type="button" class="btn btn-success">Add Milestone <i class="ml-2 fa fa-plus"></i></button>
							</div>
                        </div>
                        <div class="form-group">
                            <label>Duration in Months </label>
                            <input type="number" class="form-control" name="duration" required>
                        </div>
                        <div class="form-group">
                            <label>Available Units</label>
                            <input type="number" class="form-control" name="available_units" required>
                        </div>
						<div class="form-group">
							<label>Description</label>
							<textarea class="form-control" name="description" required></textarea>
						</div>

                        <div class="form-group">
                            <label>Category</label>
                            <select required name="category_id" class="form-control">
                                @php
                                   $category = \App\Category::where('name', 'Longterm Investment')->first();
                                @endphp
                                @if($category)
                                    <option selected value="{{ $category->id }}">{{ ucwords($category->name) }}</option>
                                @else
                                    <option selected value="">Select Category</option>
                                @endif
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.repeater/1.2.1/jquery.repeater.js" integrity="sha512-bZAXvpVfp1+9AUHQzekEZaXclsgSlAeEnMJ6LfFAvjqYUVZfcuVXeQoN5LhD7Uw0Jy4NCY9q3kbdEXbwhZUmUQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
	$('#addMilestoneBtn').click(function(e) {
		$('#milestones').append(`<div class="row">
			<div class="col-10 input-group mb-1">
				<input type="number" placeholder="Interest Rate" class="form-control" name="milestones[]" required>
				<div class="input-group-append">
					<div class="input-group-text">
						<i class="fas fa-percentage"></i>
					</div>
				</div>
				</div>
			<div class="col-2 pb-1 mb-1 d-flex justify-content-end align-items-end form-group">
				<button type="button" data-key="1" class="btn milestone-field-remove btn-danger"><i class="fa fa-trash"></i></button>
			</div>
		</div>`);
	})
	$('#milestones').on('click', 'div div .milestone-field-remove', function() {
		if ($('#milestones').children().length > 1) {
			$(this).parent().parent().remove();
		}
	});
	function populateFields() {
		$('#milestones').children().each(function(index) {
			$(this).find('div input').prop('name', `milestone[${index+1}]`);
		})
	}
</script>
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
