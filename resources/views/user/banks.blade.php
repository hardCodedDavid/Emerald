@php
use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.user")

@section('title') Banks @endsection

@section('settings') active @endsection


@section('head')
<link rel="stylesheet" href="{{ asset('assets/bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection

@section('content')
<div class="section-body">
	<div class="row">
		<div class="col-12">
            @if(session('info'))
                <div class="alert alert-info">
                    {{ session('info') }}
                </div>
            @endif
			<div class="card">
				<div class="card-header">
					<h4>Banks</h4>
				</div>
				<div class="card-body">
                        <button type="button" id="fetchBankButton" class="btn float-right mb-2 btn-primary" data-toggle="modal" data-target="#bankModal">Add bank details</button>
{{--					<a class="btn btn-success float-right" href="/banks/add"> Add bank details</a><br><br>--}}
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
									<td>
                                        <a href="/banks/delete/{{ $bank->id }}" onclick="return confirm('Are you sure you want to delete this bank?');" class="btn btn-danger">Delete</a>
{{--                                        <a href="/bank/edit/{{ $bank->id }}" class="btn btn-warning">Edit</a>--}}
                                        <button type="button" onclick="readyBankDetailsUpdate({{ $bank->id }} ,'{{ $bank->bank_name }}', '{{ ucwords($bank->account_name) }}', '{{ $bank->account_number }}', '{{ $bank->account_information }}')" class="btn btn-warning fetchBankButtonUpdate" data-toggle="modal" data-target="#updateBankModal">Edit</button>
                                    </td>
								</tr>
								@endforeach

							</tbody>
						</table>
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
                            <button type="submit" disabled id="localFormSubmitButton" onclick="submitLocalBankForm(event, 'new')" class="btn btn-primary btn-block btn-lg">Submit</button>
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

    <!-- Update Bank Modal with form -->
    <div class="modal fade" id="updateBankModal" tabindex="31" role="dialog" aria-labelledby="updateBankModal"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateBankModal">Update Bank</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('bank.update') }}" id="localBankFormUpdate" method="post">
                        @csrf
                        <input type="hidden" class="bankIDForUpdate" name="bank_id">
                        <div class="small d-flex align-items-center text-info mb-3">
                            <div onclick="toggleBankVisibiltyForm()" style="cursor: pointer">Use international bank instead</div>
                            <input class="ml-3" onchange="toggleBankVisibiltyForm()" type="checkbox">
                        </div>
                        <div class="form-group">
                            <label for="bankList">Bank Name</label>
                            <select name="bank" id="bankListUpdate" class="form-control" required>
                                <option value="">Fetching Bank...</option>
                            </select>
                        </div>
                        <input type="hidden" id="bankCodeUpdate">
                        <div class="form-group">
                            <label>Account Number</label>
                            <input type="text" id="accountNumberUpdate" name="account_number" class="form-control" required>
                            <div class="text-info mt-2 d-none" id="verifyingDisplayUpdated"></div>
                        </div>
                        <div class="form-group">
                            <label>Account Name</label>
                            <input type="text" id="accountNameUpdate" name="account_name"  class="form-control" required readonly>
                        </div>
                        <div class="small d-flex align-items-center text-info mb-3">
                            <label class="mt-2 font-13" for="saveAsDefault2">Save as my preferred bank</label>
                            <input class="ml-2 my-auto" name="saveAsDefault" id="saveAsDefault2" value="Yes" type="checkbox">
                        </div>
                        <div class="form-group">
                            <button type="submit" id="localFormSubmitButtonUpdate" onclick="submitLocalBankForm(event, 'update')" class="btn btn-primary btn-block btn-lg">Submit</button>
                        </div>
                    </form>
                    <form action="{{ route('bank.update') }}" id="internationalBankFormUpdate" method="post">
                        @csrf
                        <input type="hidden" class="bankIDForUpdate" name="bank_id">
                        <div class="small d-flex align-items-center text-info mb-3">
                            <div onclick="toggleBankVisibiltyForm()" style="cursor: pointer">Use local bank instead</div>
                            <input class="ml-3" onchange="toggleBankVisibiltyForm()" type="checkbox">
                        </div>
                        <div class="form-group">
                            <label for="bankList">Bank Name</label>
                            <input type="text" id="bankNameInternational" name="bank" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Account Number</label>
                            <input type="text" id="accountNumberInternational" name="account_number" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Account Name</label>
                            <input type="text" id="accountNameInternational" name="account_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Account Information (optional)</label>
                            <textarea id="accountInternational" name="account_information" class="form-control"></textarea>
                        </div>
                        <div class="small d-flex align-items-center text-info mb-3">
                            <label class="mt-2 font-13" for="saveAsDefault3">Save as my preferred bank</label>
                            <input class="ml-2 my-auto" name="saveAsDefault" id="saveAsDefault3" value="Yes" type="checkbox">
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

    function submitLocalBankForm(e, type) {
        e.preventDefault();
        if (type === 'new'){
            if (document.getElementById('accountName').value){
                document.getElementById("localBankForm").submit();
            }
        }else if(type === 'update'){
            if (document.getElementById('accountNameUpdate').value){
                document.getElementById("localBankFormUpdate").submit();
            }
        }
    }
    let isInternational = false;
    let bankList = document.querySelector('#bankList');
    let accountNumber = document.querySelector('#accountNumber');
    let bankCode = document.querySelector('#bankCode');

    let bankListUp = document.querySelector('#bankListUpdate');
    let accountNumberUp = document.querySelector('#accountNumberUpdate');
    let bankNameUp = document.querySelector('#bankNameInternational');
    let bankCodeUp = document.querySelector('#bankCodeUpdate');

    function readyBankDetailsUpdate(bankID, bankName, accountName, accountNumber, international) {
        let user = '{{ auth()->user()['name'] }}';
        document.querySelectorAll('.bankIDForUpdate').forEach(e => e.value = bankID);
        $('#bankNameInternational').val(bankName);
        $('#accountNameUpdate').val(accountName);
        $('#accountNumberUpdate').val(accountNumber);
        $('#accountNumberInternational').val(accountNumber);
        $('#accountNameInternational').val(accountName);
        $('#accountInternational').val(international);
        if (user === accountName){
            $('#saveAsDefault2').prop('checked', true);
            $('#saveAsDefault3').prop('checked', true);
        }else{
            $('#saveAsDefault2').prop('checked', false);
            $('#saveAsDefault3').prop('checked', false);
        }
        isInternational = international !== '';
        setBankFormVisibility();
    }
    function setBankFormVisibility(){
        document.querySelector('#localBankForm').style.display = isInternational ? 'none' : 'block';
        document.querySelector('#localBankFormUpdate').style.display = isInternational ? 'none' : 'block';
        document.querySelector('#internationalBankForm').style.display = isInternational ? 'block' : 'none';
        document.querySelector('#internationalBankFormUpdate').style.display = isInternational ? 'block' : 'none';
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
        document.querySelectorAll('.fetchBankButtonUpdate').forEach(e => {
            e.addEventListener('click', function (){
                $.ajax({
                    url: "https://api.paystack.co/bank",
                    data: { account_number: accountNumberUp.value, bank_code: bankCode.value },
                    type: "GET",
                    beforeSend: function(xhr){
                        xhr.setRequestHeader('Content-Type', 'application/json');
                        xhr.setRequestHeader('Accept', 'application/json');
                    },
                    success: function(data) {
                        let html = '<option value="">Select Bank</option>';
                        for (let i = 0; i < data.data.length; i++){
                            let oldval = $('#bankNameInternational').val();
                            if (oldval === data.data[i].name){
                                $('#bankCodeUpdate').val(data.data[i].code);
                            }
                            html += '<option ' + (oldval === data.data[i].name ? 'selected' : '') + ' value="' + data.data[i].name + '" data-code="' + data.data[i].code + '">' + data.data[i].name + '</option>';
                        }
                        $('#bankListUpdate').html(html);
                    },
                    error: function (){
                        $('#bankListUpdate').html('<option>Error fetching banks</option>');
                    }
                });
            })
        })
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
        document.getElementById('bankListUpdate').addEventListener('change', function (){
            var code;
            $("#bankListUpdate option").each(function(){
                var bank = $(this).val();
                var id = $(this).attr('data-code');
                if(bank === $('#bankListUpdate').val()){
                    code = id;
                }
            })
            $('#bankCodeUpdate').val(code)
            verifyAccountNumberUpdated();
        });
        document.getElementById('accountNumber').addEventListener('input', verifyAccountNumber);
        document.getElementById('accountNumberUpdate').addEventListener('input', verifyAccountNumberUpdated);
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
        function verifyAccountNumberUpdated(){
            if (bankListUp.value && accountNumberUp.value.length === 10 && bankCodeUp.value){
                document.querySelector('#verifyingDisplayUpdated').textContent = 'Verifying account number...';
                document.querySelector('#verifyingDisplayUpdated').classList.remove('d-none');
                document.querySelector('#verifyingDisplayUpdated').classList.remove('text-danger');
                document.querySelector('#verifyingDisplayUpdated').classList.remove('text-success');
                document.querySelector('#verifyingDisplayUpdated').classList.add('text-info');
                $.ajax({
                    url: "https://api.paystack.co/bank/resolve",
                    data: { account_number: accountNumberUp.value, bank_code: bankCodeUp.value },
                    type: "GET",
                    beforeSend: function(xhr){
                        xhr.setRequestHeader('Authorization', 'Bearer {{ env('PAYSTACK_API_KEY') }}');
                        xhr.setRequestHeader('Content-Type', 'application/json');
                        xhr.setRequestHeader('Accept', 'application/json');
                    },
                    success: function(res) {
                        document.querySelector('#verifyingDisplayUpdated').classList.remove('text-info');
                        document.querySelector('#verifyingDisplayUpdated').classList.add('text-success');
                        $('#verifyingDisplayUpdated').text('Account details verified');
                        $('#accountNameUpdate').val(res.data.account_name);
                        document.querySelector('#localFormSubmitButtonUpdate').disabled = false;
                    },
                    error: function (err){
                        let msg = 'Error processing verification';
                        document.querySelector('#verifyingDisplayUpdated').classList.remove('text-info');
                        document.querySelector('#verifyingDisplayUpdated').classList.add('text-danger');
                        if (parseInt(err.status) === 422){
                            msg = 'Account details doesn\'t match any record';
                        }
                        $('#verifyingDisplayUpdated').text(msg);
                    }
                });
            }else{
                $('#accountNameUpdated').val("");
                document.querySelector('#verifyingDisplayUpdated').classList.add('d-none');
            }
        }
    });
</script>
@endsection
