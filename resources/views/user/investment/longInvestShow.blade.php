@php
    use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.user")

@section('title') Investments @endsection

@section('head')
    <link rel="stylesheet" href="{{ asset('assets/bundles/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection

@section('investments') active @endsection

@section('content')
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Long Term Package Investments</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1">
                                <thead>
                                <tr>
                                    <th class="text-center">
                                        #
                                    </th>
                                    <th>Date created</th>
                                    <th>Farmlist</th>
                                    <th>Amount invested</th>
                                    <th>Milestone</th>
                                    <th>Maturity Status</th>
                                    <th>Due Date</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i = 1;
                                    @endphp
                                    @foreach($investments as $invest)
                                        @foreach($invest->milestoneDates() as $key => $date)
                                            @if ($loop->last)
                                                @php $lastDate = $date @endphp
                                            @endif
                                        @endforeach
                                        <tr>
                                            <td>
                                                {{ $i++ }}
                                            </td>
                                            <td>{{  $invest->created_at->format('d M, Y h:i A') }}</td>
                                            <td>{{ ucwords($invest->farm->title) }}</td>
                                            <td>
                                                ₦{{ number_format($invest->amount_invested,2) }}
                                            </td>
                                            <td>{{ count($invest->payments).'/'.count($invest->milestoneDates()) }}</td>
                                            <td>
                                                @if(strtotime($lastDate) < strtotime(now()))
                                                    <span class="badge badge-success">completed</span>
                                                @else
                                                    <span class="badge badge-warning">pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ count($invest->payments) < count($invest->milestoneDates()) ? $invest->milestoneDates()[count($invest->payments)]->format('d M, Y') : 'Fully paid' }}
                                            </td>
                                            <td>
                                                <a href="{{route('long-investment.show', $invest->id)}}" class="btn btn-success">View Investment</a>
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
    <script src="{{ asset('assets/js/page/datatables.js') }}"></script>
@endsection
