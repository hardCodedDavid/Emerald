@php
use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.user")

@section('title') Add Investment @endsection

@section('farmlist') active @endsection

@section('content')
<div class="section-body">
	<div class="row">
		<div class="col-12 col-md-6 col-lg-6">
			<div class="card">
				<div class="card-header">
					<h4>Add investment</h4>
				</div>

				<div class="card-body">
					<form action="{{ route('invest.add') }}" method="post">
						@csrf

                        <p> <strong>Farm Name:</strong> {{$farmlist->title}} <br>
                            <strong>Price Per Unit:</strong> {{$farmlist->price}} <br>
                            <strong>Current Available Units:</strong> <span style="">{{$farmlist->available_units}} Units</span>
                        </p>

                        <br>

						<div class="form-group">
                            <label>Enter your units to invest</label>
							<input type="number" class="form-control" oninput="computePayment(event)" name="unit" required placeholder="">
                            <input type="hidden" name="id" value="{{ $farmlist->id }}">
                            <div class="small mt-1" id="amountToPay"><strong></strong></div>
                        </div>
{{--                        <div class="form-group">--}}
{{--                            <label>Payment Method</label>--}}
{{--                            <select name="payment_type" onchange="processPaymentType(event)" class="form-control" required id="save-method">--}}
{{--                                <option value="null">Select Method</option>--}}
{{--                                <option value="bank">Bank Transfer / Deposit</option>--}}
{{--                                <option value="wallet">Wallet</option>--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                        <div id="companyBankDetails" class="alert alert-info">--}}
{{--                            Bank name: Access Bank<br>--}}
{{--                            Account Number: 1223409539<br>--}}
{{--                            Account name: Emerald Farms & Consultant Ltd.<br>--}}
{{--                        </div>--}}

                        <!--<div class="form-check">-->
                        <!--    <input type="checkbox" name="rollover" class="form-check-input" id="rollover">-->
                        <!--    <label class="form-check-label" for="rollover">Rollover Investment</label>-->
                        <!--</div>-->
                        <br>
                        <!--<div class="form-group">-->
                        <!--    <label>Referrer's Code (optional)</label>-->
                        <!--    <input type="text" class="form-control" name="referrer" value="{{request('ref') ?? ''}}">-->
                        <!--</div>-->

                        <!--<div class="form-group">-->
                        <!--    <a href="{{route('refer.example', ['slug' => Str::slug($farmlist->title)])}}">-->
                        <!--        Refer this farm to someone? Click here to learn more.-->
                        <!--    </a>-->
                        <!--</div>-->

						<div class="form-group">
							<button id="investButton" onclick="return confirm('You won\'t be able to cancel this once started. Are you sure you want to proceed?');" type="submit" class="btn btn-success btn-block btn-lg">Make Investment</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection

{{--@section('foot')--}}
{{--    <script>--}}
{{--        let companyBankDetails = document.getElementById('companyBankDetails');--}}
{{--        companyBankDetails.style.display = 'none';--}}
{{--        let processPaymentType = function (e) {--}}
{{--            if (e.target.value === 'bank'){--}}
{{--                companyBankDetails.style.display = 'block';--}}
{{--            }else{--}}
{{--                companyBankDetails.style.display = 'none';--}}
{{--            }--}}
{{--        }--}}
{{--    </script>--}}
{{--@endsection--}}

@section('foot')
    <script>
        let amountToPay = document.getElementById('amountToPay');
        let investButton = document.getElementById('investButton');
        let walletBalance = {{ auth()->user()->wallet['total_amount'] }};
        console.log(walletBalance)
        let toPay = 0;
        console.log(walletBalance);
        amountToPay.style.display = 'none';
        let computePayment = function (e) {
            if (e.target.value){
                toPay = {{ $farmlist->price }} * e.target.value;
                amountToPay.style.display = 'block';
                amountToPay.innerHTML = '<strong> Amount: ₦' + numberFormat(toPay) + '</strong>';
                if (walletBalance < toPay){
                    e.target.style.border = '1px solid red';
                    investButton.disabled = true;
                }else{
                    investButton.disabled = false;
                    e.target.style.border = '1px solid #20c997'
                }
            }else{
                amountToPay.style.display = 'none';
            }
        }
        function numberFormat(amount, decimal = ".", thousands = ",") {
            try {
                amount = Number.parseFloat(amount);
                let decimalCount = Number.isInteger(amount) ? 0 : amount.toString().split('.')[1].length;
                const negativeSign = amount < 0 ? "-" : "";
                let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
                let j = (i.length > 3) ? i.length % 3 : 0;
                return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
            } catch (e) {
                console.log(e)
            }
        }
    </script>
@endsection
