@php
use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.admin")

@section('title') Admin lists @endsection

@section('admins') active @endsection

@section('content')
<div class="section-body">
	<div class="row">
		<div class="col-12 col-md-6 col-lg-6">
			<div class="card">
				<div class="card-header">
					<h4>New Admin</h4>
				</div>

				<div class="card-body">
					<form action="{{ route('admin.admin.store') }}" method="post">
						@csrf
						<div class="form-group">
							<label>Name</label>
							<input type="text" class="form-control @error('name') is-invalid @enderror" name="name"  value="{{old('name')}}" required>
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
						</div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{old('email')}}" required>

                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required >

                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Password Confirmation</label>
                            <input type="password" class="form-control" name="password_confirmation" required>
                        </div>

                        <div class="form-group">
                            <label>Admin Role</label>
                            <select name="role" class="form-control @error('role') is-invalid @enderror" required>
                                <option value="">Select Role </option>

                            @foreach(\App\Admin::$roles as $key => $role)
                                    <option value="{{ $key }}" @if(old('role') == $key) selected @endif>{{ ucwords($role) }}</option>
                                @endforeach
                            </select>

                            @error('role')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

						<div class="form-group">
							<button type="submit" class="btn btn-success btn-lg btn-block">Create Admin</button>
                        </div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection

