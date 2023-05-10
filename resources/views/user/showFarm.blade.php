@php
    use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.user")

@section('title') Farm Details @endsection

@section('farmlist') active @endsection

@section('head')
    <link rel="stylesheet" href="{{ asset('assets/bundles/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection

@section('content')
    <div class="section-body">
        <div class="row">
            <div class="col-sm-12 col-lg-4">
                <div class="section-content">
                    <div class="content-details show">
                        <article class="post type-post">
                            <div class="entry-thumbnail mb-4"><img src="{{ asset($farmlist->cover) }}" alt="Thumbnail Image" style="width:100%;"></div>

                        </article>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="entry-content">
                            <h3 class="entry-title">{{ ucwords($farmlist->title) }}</h3>
                            <div class="entry-meta mt-2 mb-3">
                                <span class="time">{{ date('F d, Y', strtotime($farmlist->created_at)) }}</span>
                            </div>

                            <p>
                            <strong>Price Per Unit:</strong> {{$farmlist->price}} <br>
                            <strong>Current Available Units:</strong> <span style="">{{$farmlist->available_units}} Units</span> <br>
{{--                            <strong>Interest:</strong> {{$farmlist->interest}} % <br>--}}
                            <strong>Maturity Date:</strong> {{$farmlist->maturity_date}} Days<br>
                            <strong>Farm Opening Date:</strong> {{ $farmlist->start_date->format('M d, Y \a\t h:i A') }}<br>
                            <strong>Investment Start Date:</strong> {{ $farmlist->close_date->format('M d, Y \a\t h:i A') }}<br>
                            </p>


                            <strong>Farm Description:</strong>
                            <p>{!! $farmlist->description !!}</p>

                            @if($farmlist->isOpen())
                                <a href="/farmlist/invest/{{$farmlist->slug}}" class="btn btn-success">Invest Now</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

