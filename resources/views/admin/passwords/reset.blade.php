@extends("layouts.admin_auth")

@section('title') Reset Password @endsection

@section('content')
<div class="row">
	<div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
        <div class="text-center mb-4">
            <img alt="image" src="https://emeraldfarm.test/assets/img/logo-icon.png" class="header-logo" style="max-width:70px;">
        </div>

        <div class="card card-primary">

            <div class="card-header">
                <h4>Reset Password</h4>
            </div>

			<div class="card-body">
				<form method="POST" action="{{ url('/reset/admin') }}" class="needs-validation" novalidate="">
					@csrf
					<input type="hidden" name="token" value="{{ $token }}">
                    <div class="form-group">
                        <label>{{ __('E-Mail Address') }}</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus placeholder="Enter email">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>{{ __('Password') }}</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" autofocus>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>{{ __('Confrim Password') }}</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password_confirmation" required autocomplete="new-password">
                    </div>

					<div class="form-group">
						<button type="submit" class="btn btn-success btn-lg btn-block" tabindex="4">
						{{ __('Reset Password') }}
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection
