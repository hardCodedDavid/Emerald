@php
    use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.admin")

@section('title') View Payout @endsection

@section('content')
    <div class="row mt-sm-4">
        <div class="col-12 col-md-12 col-lg-7">
            <div class="card author-box">
                <div class="card-body">
                    <div class="author-box-center">
                        <img alt="image" src="{{ asset($farmlist->cover) }}" class="rounded-circle author-box-picture">
                        <div class="clearfix"></div>
                        <div class="author-box-name">
                            <a href="#">{{ ucwords($farmlist->name) }}</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-12 col-md-12 col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h4>Farm Details</h4>
                </div>
                <div class="card-body">
                    <div class="">
                        <p class="clearfix">
						<span class="float-left">
						Total Investors
						</span>
                            <span class="float-right text-muted">
						    {{$farmlist->investments()->count()}}
						</span>
                        </p>

                    </div>

                    <div class="">
                        <p class="clearfix">
						<span class="float-left">
						Available Units
						</span>
                            <span class="float-right text-muted">
						    {{$farmlist->available_units}}
						</span>
                        </p>

                    </div>

                    <div class="">
                        <p class="clearfix">
						<span class="float-left">
						Interest
						</span>
                            <span class="float-right text-muted">
						    {{$farmlist->interest}}%
						</span>
                        </p>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4>Investors for {{$farmlist->title}}</h4>
                    <a href="{{route('payout.investment.all', $farmlist->id)}}" class="badge badge-success">Payout All</a>
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
                                <th>Units</th>
                                <th>Amount Invested</th>
                                <th>Expected Returns</th>
                                <th>Rollover</th>
                                <th>Maturity Status</th>
                                <th>Status</th>
                                <th>Paid</th>
                                <th>Action</th>
                            </tr>
                            </thead>

                            <tbody>
                            @php($investments = $farmlist->investments()->latest()->get())

                            @foreach($investments as $key => $investment)
                                <tr>
                                    <td>{{$key + 1}}</td>
                                    <td>
                                        <a href="/admin/users/view/{{ \App\User::where('email', $investment->user)->first()['id']}}" target="_blank">
                                            {{ucwords($investment->user)}}
                                        </a>
                                    </td>
                                    <td>{{$investment->units}}</td>
                                    <td>{{number_format($investment->amount_invested)}}</td>
                                    <td>{{number_format($investment->amount_invested * (($investment->farm->interest  + 100)/100))}}</td>
                                    <td>{{$investment->rollover == 1? 'Yes' : 'No'}}</td>
                                    <td>
                                        @if($investment->maturity_status == 'pending')
                                            <div class="badge badge-warning">Pending</div>
                                        @elseif($investment->maturity_status == 'matured')
                                            <div class="badge badge-success">{{ucwords($investment->maturity_status)}}</div>
                                        @endif
                                    </td>

                                    <td>
                                        @if($investment->status == 'pending')
                                            <div class="badge badge-warning">Pending</div>
                                        @elseif($investment->status == 'closed' || $investment->status == 'active' || $investment->status =='paid' )
                                            <div class="badge @if($investment->status == 'closed') badge-danger @else badge-success @endif">{{ucwords($investment->status)}}</div>
                                        @elseif($investment->status == 'declined')
                                            <div class="badge badge-danger">Declined</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($investment->isPaid())
                                            <div class="badge badge-success">Yes</div>
                                        @else
                                            <div class="badge badge-warning">No</div>
                                        @endif
                                    </td>

                                    <td>
                                        @if(! $investment->isPaid())
                                            <a href="{{route('payout.investment', $investment->id)}}" class="btn btn-success">Payout</a>
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

@endsection

@section('foot')
    <script src="{{ asset('assets/bundles/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/bundles/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/page/datatables.js') }}"></script>
@endsection
