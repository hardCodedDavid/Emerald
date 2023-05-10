@extends("layouts.admin_auth")

@section('title') Admin Login @endsection

@section('content')
<div class="row">
	<div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
        <div class="text-center mb-4">
            <img alt="image" src="{{asset('assets/img/logo-icon.png')}}" class="header-logo" style="max-width:70px;">
        </div>
		<div class="card card-primary">
            <div class="card-header">
                <h4>Login</h4>
            </div>

			<div class="card-body">
				<form method="POST" action="{{ route('admin.login') }}" class="needs-validation" novalidate="">
					@csrf
					<div class="form-group">
						<label for="email">{{ __('E-Mail Address') }}</label>
						<input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus tabindex="1" autofocus>
						<div class="invalid-feedback">
							Please fill in your email
						</div>
						@error('email')
                            <span class="invalid-feedback" role="alert">
                            	<strong>{{ $message }}</strong>
                            </span>
                        @enderror
					</div>
					<div class="form-group">
					    <label for="password" >{{ __('Password') }}</label>
					    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" tabindex="2">
						<div class="invalid-feedback">
							please fill in your password
						</div>
						@error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                           	</span>
                        @enderror
					</div>
					<div class="form-group row ml-2">
						<div class="custom-control custom-checkbox col-md-6 col-12">
							<input class="custom-control-input" tabindex="3" id="remember-me" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
							<label class="custom-control-label" for="remember-me">{{ __('Remember Me') }}</label>
						</div>
						<div class="float-right col-md-6 col-12">
							<a href="/admin/forget-password" class="text-small">
							Forgot Password?
							</a>
						</div>
					</div>
					<div class="form-group">
						<button type="submit" class="btn btn-success btn-lg btn-block" tabindex="4">
						{{ __('Login') }}
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
