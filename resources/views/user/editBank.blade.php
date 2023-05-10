@php
use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.user")

@section('title') Add Banks @endsection

@section('content')
<div class="section-body">
	<div class="row">
		<div class="col-12 col-md-6 col-lg-6">
			<div class="card">
				<div class="card-header">
					<h4>Update Banks</h4>
				</div>
				<div class="card-body">
					<form action="{{ route('bank.update', $bank->id) }}" method="post">
						@csrf
						<div class="form-group">
							<label>Bank Name</label>
							<input type="text" name="bank" class="form-control" value="{{$bank->bank_name}}">
						</div>
						<div class="form-group">
							<label>Account Name</label>
							<input type="text" name="account_name" class="form-control" value="{{$bank->account_name}}">
						</div>
						<div class="form-group">
							<label>Account Number</label>
							<input type="text" name="account_number" class="form-control"  value="{{$bank->account_number}}">
						</div>
                        <div class="form-group">
                            <label>Account Information (optional)</label>
                            <textarea  name="account_information" class="form-control" >{{$bank->account_information}}</textarea>
                        </div>
						<div class="form-group">
							<button type="submit" class="btn btn-primary btn-block btn-lg">Update</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
