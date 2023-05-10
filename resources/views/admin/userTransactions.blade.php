@php
    use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.admin")

@section('title') Transactions @endsection

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
                        <h4>Transactions</h4>
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
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                                </thead>

                                <tbody>
                                    @foreach($transactions as $key => $transaction)
                                        @php
                                            $links = '';
                                            if ($transaction->type == 'deposits' && $transaction->status == 'pending') {
                                                   $links .= '<a href="/admin/transactions/deposits/approve/' . $transaction->id . '" class="dropdown-item" onclick="return confirm("Are you sure you want to approve this deposits?");">Approve</a>';
                                                   $links .= '<a href="/admin/transactions/deposits/decline/' . $transaction->id . '" class="dropdown-item" onclick="return confirm("Are you sure you want to decline this deposits?");">Decline</a>';
                                               } elseif ($transaction->type == 'payouts' && $transaction->status == 'pending') {
                                                   $links .= '<a href="/admin/transactions/payouts/approve/' . $transaction->id . '" class="dropdown-item" onclick="return confirm("Are you sure you want to approve this payouts?");">Approve</a>';
                                                   $links .= '<a href="/admin/transactions/deposits/decline/' . $transaction->id . '" class="dropdown-item" onclick="return confirm("Are you sure you want to decline this payouts?");">Decline</a>';
                                               }else if($transaction->type == 'booking' && $transaction->status == 'pending'){
                                                   $links .= '<a href="/admin/bookings/' . $transaction->booking_id . '/approve" class="dropdown-item" onclick="return confirm("Are you sure you want to approve this payouts?");">Approve</a>';
                                                   $links .= '<a href="/admin/bookings/' . $transaction->booking_id . '/decline" class="dropdown-item" onclick="return confirm("Are you sure you want to decline this payouts?");">Decline</a>';
                                               }
                                        @endphp
                                        <tr>
                                            <td>{{$key + 1}}</td>
                                            <td>
                                                {{Util::getUserByEmail($transaction->user)->name}}
                                            </td>
                                            <td>{{'₦' . number_format(implode("", explode(',',$transaction->amount))) .'.00'}}</td>
                                            <td>{{ucwords($transaction->type)}}</td>
                                            <td>
                                                <div class="badge @if($transaction->status == 'paid' || $transaction->status == 'approved') badge-success @elseif($transaction->status == 'pending') badge-warning @elseif($transaction->status == 'declined') badge-danger @endif py-2 px-2">{{ucwords($transaction->status) }}</div>
                                            </td>
                                            <td>{{$transaction->created_at->format('M d, Y')}}</td>
                                            <td>
                                                @if(($transaction->type == 'deposits' && $transaction->status == 'pending') || ($transaction->type == 'payouts' && $transaction->status == 'pending') || ($transaction->type == 'booking' && $transaction->status == 'pending'))
                                                    <div class="dropdown show">
                                                        <a class="btn btn-success dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            Action
                                                        </a>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                                            {!! $links !!}
                                                        </div>
                                                    </div>
                                                @endif
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

@section('foot')
    <script src="{{ asset('assets/bundles/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/bundles/jquery-ui/jquery-ui.min.js') }}"></script>
@endsection
