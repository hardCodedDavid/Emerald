@php
use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.user")

@section('title') Profile @endsection

@section('settings') active @endsection

@section('content')
    @if(Util::completeProfile(auth()->user()) || Util::completeProfileKin(auth()->user()))
        <div class="row">
        <div class="col-12">
            <div class="alert alert-info show fade alert-has-icon">
                <div class="alert-icon">
                    <i class="far fa-lightbulb"></i>
                </div>
                <div class="alert-body">
                    <p>
                        Kindly provide the following information to complete your registration:
                    </p>
                    <ul>
                        @if(!$user->address)
                            <li>Address</li>
                        @endif
                        @if(!$user->state)
                            <li>State</li>
                        @endif
                        @if(!$user->country)
                            <li>Country</li>
                        @endif
                        @if(!$user->city)
                            <li>City</li>
                        @endif
                        @if(!$user->dob)
                            <li>Date of Birth</li>
                        @endif
                        @if(count($banks) < 1)
                            <li>Bank Details</li>
                        @endif
                        @if(! ($user->nk_Name && $user->nk_Email && $user->nk_Address && $user->nk_Phone))
                            <li>Next of Kin Details</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row mt-sm-4">
	<div class="col-12 col-md-12 col-lg-4">
		<div class="card author-box">
			<div class="card-body">
				<div class="author-box-center">
					<img alt="image" src="{{ Util::getPassport($user) }}" class="rounded-circle author-box-picture">
					<div class="clearfix"></div>
					<div class="author-box-name">
						<a href="#">{{ ucwords($user->name) }}</a>
					</div>
					<div class="author-box-job">{{ strtolower($user->email) }}</div>
				</div>
				<div class="text-center">
					<div class="author-box-description">
						<p>
							{{ ucfirst($user->address) }}
						</p>
					</div>
				</div>
			</div>
		</div>
		<div class="card">
			<div class="card-header">
				<h4>Personal Details</h4>
			</div>
			<div class="card-body">
				<div class="py-4">
					<p class="clearfix">
						<span class="float-left">
						Address
						</span>
						<span class="float-right text-muted">
						{{ $user->address }}
						</span>
					</p>
					<p class="clearfix">
						<span class="float-left">
						State
						</span>
						<span class="float-right text-muted">
						{{ $user->state }}
						</span>
					</p>
					<p class="clearfix">
						<span class="float-left">
						City
						</span>
						<span class="float-right text-muted">
						{{ $user->city }}
						</span>
					</p>
					<p class="clearfix">
						<span class="float-left">
						Country
						</span>
						<span class="float-right text-muted">
						<a href="#">{{ $user->country }}</a>
						</span>
					</p>
					<p class="clearfix">
						<span class="float-left">
						Zip Code
						</span>
						<span class="float-right text-muted">
						<a href="#"> {{ $user->zip }}</a>
						</span>
					</p>
      <!--              <p class="clearfix">-->
						<!--<span class="float-left">-->
						<!--Referral Code-->
						<!--</span>-->
      <!--                  <span class="float-right text-muted">-->
						<!--<a href="#">{{ auth()->user()->code }}</a>-->
						<!--</span>-->
      <!--              </p>-->
				</div>
			</div>
		</div>
	</div>
	<div class="col-12 col-md-12 col-lg-8">
		<div class="card">
			<div class="padding-20">
				<ul class="nav nav-tabs" id="myTab2" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" id="profile-tab2" data-toggle="tab" href="#settings" role="tab"
							aria-selected="false">Profile</a>
					</li>

                    <li class="nav-item">
                        <a class="nav-link" id="profile-tab2" data-toggle="tab" href="#kin" role="tab"
                           aria-selected="false">Next Of Kin</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" id="bank-details" data-toggle="tab" href="#bank" role="tab"
                           aria-selected="false">Bank Details</a>
                    </li>

				</ul>
				<div class="tab-content tab-bordered" id="myTab3Content">
					<div class="tab-pane fade show active" id="settings" role="tabpanel" aria-labelledby="profile-tab1">
						<form method="post" action="{{ route('profile.edit') }}" enctype="multipart/form-data">
							@csrf
							<div class="card-header">
								<h4>Edit Profile</h4>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="form-group col-md-12 col-12">
										<label>Name <span class="text-danger">*</span></label>
										<input type="text" class="form-control" value="{{ $user->name }}" name="name">
									</div>
								</div>
								<div class="row">
									<div class="form-group col-md-7 col-12">
										<label>Email <span class="text-danger">*</span></label>
										<input type="email" class="form-control" value="{{ $user->email }}" name="email" readonly="">
									</div>
									<div class="form-group col-md-5 col-12">
										<label>Phone <span class="text-danger">*</span></label>
										<input type="tel" class="form-control" value="{{ $user->phone }}" name="phone" >
									</div>
								</div>

								<div class="row">
									<div class="form-group col-12 col-md-4">
										<label>Address <span class="text-danger">*</span></label>
										<input type="text" name="address" class="form-control" value='{{ $user->address }}'>
									</div>
									<div class="form-group col-12 col-md-4">
										<label>City <span class="text-danger">*</span></label>
										<input type="text" name="city" class="form-control" value='{{ $user->city }}'>
									</div>
                                    <div class="form-group col-12 col-md-4">
                                        <label>DOB <span class="text-danger">*</span></label>
                                        <input type="date" name="dob" class="form-control {{$user->dob == null ?  'border-danger' : ''}}" value='{{ $user->dob }}'>
                                    </div>
								</div>

								<div class="row">
									<div class="form-group col-12 col-md-4">
										<label>State <span class="text-danger">*</span></label>
										<input type="text" name="state" class="form-control" value='{{ $user->state }}'>
									</div>
									<div class="form-group col-12 col-md-4">
										<label>Country <span class="text-danger">*</span></label>
										<input type="text" name="country" class="form-control" value='{{ $user->country }}'>
									</div>
									<div class="form-group col-12 col-md-4">
										<label>ZIP Code</label>
										<input type="text" name="zip" class="form-control" value='{{ $user->zip }}'>
									</div>
								</div>
								<div class="row">
									<div class="form-group col-12 col-md-12">
										<label>Passport</label>
										<input type="file" name="passport" class="form-control">
									</div>
								</div>
							</div>
							<div class="card-footer text-right">
								<button type="submit" class="btn btn-success">Save Changes</button>
							</div>
						</form>
					</div>

					<div class="tab-pane fade show" id="kin" role="tabpanel" aria-labelledby="profile-tab2">
						<form method="post" action="{{ route('profile.edit.kin') }}" enctype="multipart/form-data">
							@csrf
							<div class="card-header">
								<h4>Edit Next of Kin</h4>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="form-group col-md-12 col-12">
										<label>Name <span class="text-danger">*</span></label>
										<input type="text" class="form-control" value="{{ $user->nk_Name }}" name="name">
									</div>
								</div>
								<div class="row">
									<div class="form-group col-md-7 col-12">
										<label>Email <span class="text-danger">*</span></label>
										<input type="email" class="form-control" value="{{ $user->nk_Email }}" name="email" >
									</div>
									<div class="form-group col-md-5 col-12">
										<label>Phone <span class="text-danger">*</span></label>
										<input type="tel" class="form-control" value="{{ $user->nk_Phone }}" name="phone" >
									</div>
								</div>

								<div class="row">
									<div class="form-group col-12">
										<label>Address <span class="text-danger">*</span></label>
										<input type="text" name="address" class="form-control" value='{{ $user->nk_Address }}'>
									</div>

								</div>

							</div>
							<div class="card-footer text-right">
								<button type="submit" class="btn btn-success">Save Changes</button>
							</div>
						</form>
					</div>

                    <div class="tab-pane fade show" id="bank" role="tabpanel" aria-labelledby="bank-details">
                        <form method="post" action="{{ route('profile.edit.kin') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-header">
                                <h4>Bank Details</h4>
                            </div>

                            <div class="card-body">
{{--                                <a class="btn btn-success float-right" href="/banks/add"> Add bank details</a><br><br>--}}
                                <button type="button" id="fetchBankButton" class="btn mb-3 float-right mb-2 btn-primary" data-toggle="modal" data-target="#bankModal">Add bank details</button>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th class="text-center">
                                                #
                                            </th>
                                            <th>Bank Name</th>
                                            <th>Account Name</th>
                                            <th>Account Number</th>
                                            <th>Account Information</th>
                                            <th>Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                            $i = 1;
                                        @endphp
                                        @foreach($banks as $bank)
                                            <tr>
                                                <td>
                                                    {{ $i++ }}
                                                </td>
                                                <td> {{ ucwords($bank->bank_name) }}</td>
                                                <td>{{ ucwords($bank->account_name) }}</td>
                                                <td>{{ $bank->account_number }}</td>
                                                <td>{{ $bank->account_information }}</td>
                                                <td><a href="/banks/delete/{{ $bank->id }}" onclick="return confirm('Are you sure you want to delete this bank?');" class="btn btn-danger">Delete</a><a href="/bank/edit/{{ $bank->id }}" class="btn btn-warning">Edit</a></td>
                                            </tr>
                                        @endforeach

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection

@section('modal')
    <!-- Modal with form -->
    <div class="modal fade" id="bankModal" tabindex="31" role="dialog" aria-labelledby="bankModal"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bankModal">Add Banks</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('bank.add') }}" id="localBankForm" method="post">
                        @csrf
                        <div class="small d-flex align-items-center text-info mb-3">
                            <div onclick="toggleBankVisibiltyForm()" style="cursor: pointer">Use international bank instead</div>
                            <input class="ml-3" onchange="toggleBankVisibiltyForm()" type="checkbox">
                        </div>
                        <div class="form-group">
                            <label for="bankList">Bank Name</label>
                            <select name="bank" id="bankList" class="form-control" required>
                                <option value="">Fetching Bank...</option>
                            </select>
                        </div>
                        <input type="hidden" id="bankCode">
                        <div class="form-group">
                            <label>Account Number</label>
                            <input type="text" id="accountNumber" name="account_number" class="form-control" required>
                            <div class="text-info mt-2 d-none" id="verifyingDisplay"></div>
                        </div>
                        <div class="form-group">
                            <label>Account Name</label>
                            <input type="text" id="accountName" name="account_name"  class="form-control" required readonly>
                        </div>
                        <div class="small d-flex align-items-center text-info mb-3">
                            <label class="mt-2 font-13" for="saveAsDefault1">Save as my preferred bank</label>
                            <input class="ml-2 my-auto" @if(\App\Bank::where('user', auth()->user()['email'])->count() == 0) checked @endif name="saveAsDefault" id="saveAsDefault1" value="Yes" type="checkbox">
                        </div>
                        <div class="form-group">
                            <button type="submit" disabled id="localFormSubmitButton" onclick="submitLocalBankForm(event)" class="btn btn-primary btn-block btn-lg">Submit</button>
                        </div>
                    </form>
                    <form action="{{ route('bank.add') }}" id="internationalBankForm" method="post">
                        @csrf
                        <div class="small d-flex align-items-center text-info mb-3">
                            <div onclick="toggleBankVisibiltyForm()" style="cursor: pointer">Use local bank instead</div>
                            <input class="ml-3" onchange="toggleBankVisibiltyForm()" type="checkbox">
                        </div>
                        <div class="form-group">
                            <label for="bankList">Bank Name</label>
                            <input type="text" name="bank" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Account Number</label>
                            <input type="text" name="account_number" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Account Name</label>
                            <input type="text" name="account_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Account Information (optional)</label>
                            <textarea  name="account_information" class="form-control"></textarea>
                        </div>
                        <div class="small d-flex align-items-center text-info mb-3">
                            <label class="mt-2 font-13" for="saveAsDefault1">Save as my preferred bank</label>
                            <input class="ml-2 my-auto" @if(\App\Bank::where('user', auth()->user()['email'])->count() == 0) checked @endif name="saveAsDefault" id="saveAsDefault1" value="Yes" type="checkbox">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block btn-lg">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('foot')
<script src="{{ asset('assets/bundles/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/bundles/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/js/page/datatables.js') }}"></script>
<script>
    function submitLocalBankForm(e) {
        e.preventDefault();
        if (document.getElementById('accountName').value){
            document.getElementById("localBankForm").submit();
        }
    }
    let isInternational = false;
    let bankList = document.querySelector('#bankList');
    let accountNumber = document.querySelector('#accountNumber');
    let bankCode = document.querySelector('#bankCode');
    function setBankFormVisibility(){
        document.querySelector('#localBankForm').style.display = isInternational ? 'none' : 'block';
        document.querySelector('#internationalBankForm').style.display = isInternational ? 'block' : 'none';
    }
    setBankFormVisibility();
    function toggleBankVisibiltyForm() {
        isInternational = !isInternational;
        setBankFormVisibility();
    }
    $(document).ready(function(){
        $('#fetchBankButton').click(function (){
            $.ajax({
                url: "https://api.paystack.co/bank",
                data: { account_number: accountNumber.value, bank_code: bankCode.value },
                type: "GET",
                beforeSend: function(xhr){
                    xhr.setRequestHeader('Content-Type', 'application/json');
                    xhr.setRequestHeader('Accept', 'application/json');
                },
                success: function(data) {
                    let html = '<option value="">Select Bank</option>';
                    for (let i = 0; i < data.data.length; i++){
                        html += '<option value="' + data.data[i].name + '" data-code="' + data.data[i].code + '">' + data.data[i].name + '</option>';
                    }
                    $('#bankList').html(html);
                },
                error: function (){
                    $('#bankList').html('<option>Error fetching banks</option>');
                }
            });
        });
        document.getElementById('bankList').addEventListener('change', function (){
            var code;
            $("#bankList option").each(function(){
                var bank = $(this).val();
                var id = $(this).attr('data-code');
                if(bank === $('#bankList').val()){
                    code = id;
                }
            })
            $('#bankCode').val(code)
            verifyAccountNumber();
        });
        document.getElementById('accountNumber').addEventListener('input', verifyAccountNumber);
        function verifyAccountNumber(){
            if (bankList.value && accountNumber.value.length === 10 && bankCode.value){
                document.querySelector('#verifyingDisplay').textContent = 'Verifying account number...';
                document.querySelector('#verifyingDisplay').classList.remove('d-none');
                document.querySelector('#verifyingDisplay').classList.remove('text-danger');
                document.querySelector('#verifyingDisplay').classList.remove('text-success');
                document.querySelector('#verifyingDisplay').classList.add('text-info');
                $.ajax({
                    url: "https://api.paystack.co/bank/resolve",
                    data: { account_number: accountNumber.value, bank_code: bankCode.value },
                    type: "GET",
                    beforeSend: function(xhr){
                        xhr.setRequestHeader('Authorization', 'Bearer sk_test_004613305b70376c5e50f7234ac87152e1df05ca');
                        xhr.setRequestHeader('Content-Type', 'application/json');
                        xhr.setRequestHeader('Accept', 'application/json');
                    },
                    success: function(res) {
                        document.querySelector('#verifyingDisplay').classList.remove('text-info');
                        document.querySelector('#verifyingDisplay').classList.add('text-success');
                        $('#verifyingDisplay').text('Account details verified');
                        $('#accountName').val(res.data.account_name);
                        document.querySelector('#localFormSubmitButton').disabled = false;
                    },
                    error: function (err){
                        let msg = 'Error processing verification';
                        document.querySelector('#verifyingDisplay').classList.remove('text-info');
                        document.querySelector('#verifyingDisplay').classList.add('text-danger');
                        if (parseInt(err.status) === 422){
                            msg = 'Account details doesn\'t match any record';
                        }
                        $('#verifyingDisplay').text(msg);
                    }
                });
            }else{
                $('#accountName').val("");
                document.querySelector('#verifyingDisplay').classList.add('d-none');
            }
        }
    });
</script>
@endsection
