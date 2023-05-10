@php
use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.admin")

@section('title') Payouts @endsection

@section('head')
<link rel="stylesheet" href="{{ asset('assets/bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection

@section('transactions') active @endsection

@section('content')
<div class="section-body">
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-header">
					<h4>Payouts</h4>
                    <a href="{{route('download.transactions','payouts')}}" class="badge badge-success">Export Excel</a>

                </div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-striped" id="table-1">
							<thead>
								<tr>
									<th class="text-center">
										#
									</th>
									<th>User</th>
									<th>Amount</th>
									<th>Bank</th>
									<th>Date </th>
									<th>Status</th>
									<th>Action</th>
 								</tr>
							</thead>

						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<button type="button" id="bankModalBtn" class="btn d-none btn-primary" data-toggle="modal"
        data-target=".bd-example-modal-sm">Small
    modal</button>
@endsection

@section('modal')
    <!-- Small Modal -->
    <div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mySmallModalLabel">Bank Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table>
                        <tr>
                            <td>
                                <div class="small m-0">Bank Name</div>
                                <div class="font-weight-bold mb-2"><span id="bankNameKeyModal"></span></div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="small m-0">Account Name</div>
                                <div class="font-weight-bold mb-2"><span id="accountNameKeyModal"></span></div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="small m-0">Account Number</div>
                                <input type="text" id="accountNumberKeyModal" style="background: transparent; border: none; font-weight: bolder; color: #6c757d; padding-left: 0; outline: none" value="435673464376">
                                <button onclick="copyAccountNumber()" class="btn btn-secondary"><span class="fas fa-copy"></span></button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('foot')
<script src="{{ asset('assets/bundles/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/bundles/jquery-ui/jquery-ui.min.js') }}"></script>
<script>
    $(document).ready(function () {

        $('#table-1').DataTable({
            "processing": true,
            "serverSide": true,
            "searching": true,
            "ajax":{
                "url": "{{ url('loadPayouts') }}",
                "dataType": "json",
                "type": "POST",
                "data":{ _token: "{{csrf_token()}}"}
            },
            "columns": [
                { "data": "sn" },
                { "data": "name" },
                { "data": "amount" },
                { "data": "bank" },
                { "data": "date" },
                { "data": "status" },
                { "data": "action" },
            ],
            "lengthMenu": [[100, 200, 300, 400], [100, 200, 300, 400]]
        });
    });
    function showAccountDetails(bank, name, number){
        $('#bankModalBtn').click();
        $('#bankNameKeyModal').text(bank);
        $('#accountNameKeyModal').text(name);
        $('#accountNumberKeyModal').val(number);
    }
    function copyAccountNumber() {
        var copyText = document.getElementById("accountNumberKeyModal");
        copyText.select();
        copyText.setSelectionRange(0, 99999)
        document.execCommand("copy");
    }
</script>
@endsection
