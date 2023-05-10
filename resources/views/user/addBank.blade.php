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
					<h4>Add Banks</h4>
				</div>
				<div class="card-body">
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
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block btn-lg">Submit</button>
                        </div>
                    </form>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection

@section('foot')
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
