@php
    use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.admin")

@section('title') Batch Payouts @endsection

@section('head')
    <link rel="stylesheet" href="{{ asset('assets/bundles/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
    <link href="//cdn.jsdelivr.net/npm/featherlight@1.7.14/release/featherlight.min.css" type="text/css" rel="stylesheet" />


@endsection

@section('batch-payouts') active @endsection

@section('content')
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex ">
                            <h4>Batch Payouts</h4>
                            <form method="POST" id="uploadFileForm" action="/admin/batch-payouts/upload" enctype="multipart/form-data">
                                @csrf()
                                <input type="file" name="file" oninput="document.getElementById('uploadFileForm').submit();" id="fileField" class="d-none">
                            </form>
                            <button onclick="document.getElementById('fileField').click();" class="btn btn-primary">Upload New Batch Payout</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1">
                                <thead>
                                <tr>
                                    <th class="text-center">
                                        #
                                    </th>
                                    <th>Batch ID</th>
                                    <th>Name</th>
                                    <th>Email Address</th>
                                    <th>Phone No</th>
                                    <th>No of Units</th>
                                    <th>Initial Investment</th>
                                    <th>Expected Returns</th>
                                    <th>Farm Cycle</th>
                                    <th>Payment Date</th>
                                    <th>Queue</th>
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
@endsection

@section('modal')
    <div class="modal fade bd-example-modal-lg" id="editBasicPayoutModal" tabindex="-1" role="dialog" aria-labelledby="editBasicPayoutModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel">Update Batch Payout</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="editBatchPayoutForm">
                        @method('PUT')
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="batch">Batch ID</label>
                                <input type="text" class="form-control" name="batch" id="batch" placeholder="Batch ID">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Name">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Email Address">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="phone">Phone</label>
                                <input type="text" class="form-control" name="phone" id="phone" placeholder="Phone">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="units">Units</label>
                                <input type="text" class="form-control" name="units" id="units" placeholder="Units">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="amount_invested">Invested Amount</label>
                                <input type="text" class="form-control" name="amount_invested" id="amount_invested" placeholder="Invested Amount">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="expected_returns">Expected Returns</label>
                                <input type="text" class="form-control" name="expected_returns" id="expected_returns" placeholder="Expected Returns">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="farm_cycle">Farm Cycle</label>
                                <input type="text" class="form-control" name="farm_cycle" id="farm_cycle" placeholder="Farm Cycle">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="payment_date">Payment Date</label>
                                <input type="text" class="form-control" name="payment_date" id="payment_date" placeholder="Payment Date">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="queue">Queue</label>
                                <input type="text" class="form-control" name="queue" id="queue" placeholder="Queue">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary m-t-15 waves-effect">Update</button>
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
    <script>
        function prepareEditBatchPayout(id, batch, name, email, phone, units, amount_invested, expected_returns, farm_cycle, payment_date, queue) {
            $('#editBatchPayoutForm').prop('action', `/admin/batch-payouts/${id}/update`);
            $('#batch').val(batch);
            $('#name').val(name);
            $('#email').val(email);
            $('#phone').val(phone);
            $('#units').val(units);
            $('#amount_invested').val(amount_invested);
            $('#expected_returns').val(expected_returns);
            $('#farm_cycle').val(farm_cycle);
            $('#payment_date').val(payment_date);
            $('#queue').val(queue);
        }
        $(document).ready(function () {
            $('#table-1').DataTable({
                "processing": true,
                "serverSide": true,
                "searching": true,
                "ajax":{
                    "url": "{{ url('loadBatchPayouts') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data":{ _token: "{{csrf_token()}}"}
                },
                "columns": [
                    { "data": "sn" },
                    { "data": "batch" },
                    { "data": "name" },
                    { "data": "email" },
                    { "data": "phone" },
                    { "data": "units" },
                    { "data": "amount_invested" },
                    { "data": "expected_returns" },
                    { "data": "farm_cycle" },
                    { "data": "payment_date" },
                    { "data": "queue" },
                    { "data": "action" },
                ],
                "lengthMenu": [[100, 200, 300, 400], [100, 200, 300, 400]]

            });
        });
    </script>
@endsection
