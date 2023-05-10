@extends("layouts.admin_auth")

@section('title') Account registration @endsection

@section('content')
<div class="row">
    <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xl-8 offset-xl-2">
        <div class="text-center mb-4">
            <img alt="image" src="{{asset('assets/img/logo-icon.png')}}" class="header-logo" style="max-width:70px;">
        </div>
        <div class="card card-primary">
            <div class="card-header">
                <h4>Register</h4>
            </div>
            <div class="card-body">
                <form method="post" action="{{ route('register') }}">
                    @csrf
                    <div class="row">
                        <div class="form-group col-6">
                            <label for="name">Name</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-6">
                            <label for="email">Email</label>
                            <input name="email" id="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">

                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">

                        <div class="form-group col-6">
                            <label >Country</label>
                            <select class="form-control" name="country" required onchange="getPhoneCode(this)">
                                @foreach(\App\User::$countries as $key => $country)
                                    <option value="{{$country['phonecode']}}" data-code="+{{$country['phonecode']}}" @if($key == 159) selected @endif >{{$country['name']}}</option>
                                @endforeach
                            </select>

                            @error('country')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="form-group col-6">
                            <label for="phone">Phone</label>

                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="phone_code">+{{\App\User::$countries[159]['phonecode']}}</span>
                                </div>
                                <input type="text" class="form-control  @error('phone') is-invalid @enderror" name="phone" required value="{{ old('phone') }}">
                            </div>
                            @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-6">
                            <label for="password" class="d-block">Password</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group col-6">
                            <label for="password2" class="d-block">Password Confirmation</label>
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="agree" class="custom-control-input" id="agree" required>
                            <label class="custom-control-label" for="agree">I agree with the terms and conditions</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-success btn-lg btn-block">
                        Register
                        </button>
                    </div>
                </form>
            </div>
            <div class="mb-4 text-muted text-center">
                Already Registered? <a href="/login">Login</a>
            </div>
        </div>
        <div class="mt-5 text-muted text-center">
            By signing up you have agreed to our  <a href="https://emeraldfarms.ng/terms-and-condition/" target="_blank">Terms of Use & Privacy Policy</a>
        </div>
    </div>
</div>

@endsection


@section('foot')
    <script type="text/javascript">

        function getPhoneCode(obj){
            document.getElementById('phone_code').innerHTML = obj.options[obj.selectedIndex].getAttribute('data-code');
        }

    </script>

@endsection
