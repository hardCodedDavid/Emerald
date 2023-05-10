@php
    use App\Http\Controllers\Globals as Util;
    $lastDate = null;
    $nextDate = null;
    $cur = strtotime(date('Y-m-d H:i:s'));
    $mat = strtotime($investment->maturity_date);
    $diff = $mat - $cur;
    $farmlist = Util::getFarmlist($investment->farmlist);
    $interest = $investment->amount_invested*($farmlist->interest/100);
    $add = $investment->amount_invested+$interest;
@endphp

@extends("layouts.user")

@section('title') Investments @endsection

@section('content')
    <div class="section-body">
        <div class="row">
            <div class="col-sm-12 col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Investment Details</h4>
                    </div>
                    <div class="card-body">
                        <div class="">
                            <table class="table table-striped" id="table-1">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>1</th>
                                        <td>Number of Units</td>
                                        <td>
                                            {{$investment->units}}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>2</td>
                                        <td>Farmlist</td>
                                        <td>
                                            {{$investment->farm->title}}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>3</td>
                                        <td>Investment Status</td>
                                        <td>
                                            @if($investment->paid == 1)
                                                <div class="badge badge-success">Paid</div>
                                            @else
                                                <div class="badge @if($investment->status == 'active' || $investment->status == 'approve') badge-primary @elseif($investment->status == 'pending') badge-warning @elseif($investment->status == 'decline' || $investment->status == 'closed') badge-danger @endif py-2 px-2">{{ucwords($investment->status) }}</div>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>4</td>
                                        <td>Maturity Status</td>
                                        <td>
                                            @if($investment->paid == 1)
                                                <div class="badge badge-success">Completed</div>
                                            @else
                                                <div class="badge @if($investment->maturity_status == 'matured') badge-primary @elseif($investment->maturity_status == 'pending') badge-warning @endif py-2 px-2">{{ucwords($investment->maturity_status) }}</div>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>5</td>
                                        <td>Rollover</td>
                                        <td>
                                            {{$investment->rollover ? 'Yes' : 'No'}}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>5</td>
                                        <td>Amount Invested</td>
                                        <td>
                                            NGN {{ number_format(implode("", explode(',',$investment->amount_invested))) .'.00'}}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>6</td>
                                        <td>ROI</td>
                                        <td>
                                            {{$investment->farm->interest}}%
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>6</td>
                                        <td>Expected returns</td>
                                        <td>
                                            NGN {{number_format($add,2)}}

                                        </td>
                                    </tr>

                                    <tr>
                                        <td>7</td>
                                        <td>Date Created</td>
                                        <td>
                                            {{$investment->created_at->format('D M Y')}}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>8</td>
                                        <td>Days Remaining</td>
                                        <td>
                                            @if($investment->maturity_date == null)
                                                <div class="badge badge-warning">Pending</div>
                                            @elseif($investment->maturity_status == 'pending')
                                                {{ round((($diff/24)/60)/60) }}
                                            @else($investment->maturity_status == 'matured')
                                                <div class="badge badge-success">Completed</div>
                                            @endif
                                        </td>
                                        <td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @if($investment->maturity_status != 'matured')
                <div class="col-lg-4 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Days Remaining</h4>
                        </div>
                        <div class="card-body text-center">
                            <h1 style="font-size: 100px">
                                @if($investment->maturity_date == null)
                                    N/A
                                @elseif($investment->maturity_status == 'pending')
                                    {{ round((($diff/24)/60)/60) }}
                                @endif
                            </h1>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
