@php
use App\Http\Controllers\Globals as Util;
$farmlist = Util::getFarmlist($news->farmlist);
@endphp

@extends("layouts.user")

@section('title') News | {{ ucwords($news->title) }} @endsection

@section('head')
<link rel="stylesheet" href="{{ asset('assets/bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection

@section('news') active @endsection

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
						<h3 class="entry-title">{{ ucwords($news->title) }}</h3>
						<div class="entry-meta mt-2 mb-3">
							<span class="author"><a href="#">{{ ucwords($farmlist->title) }}</a></span>
							<span class="time">{{ date('F d, Y', strtotime($news->created_at)) }}</span>
						</div>
						<p>{!! $news->content !!}</p>
					</div>
			    </div>
		    </div>
    	    <!--<div class="entry-content">
    						<h3 class="entry-title">{{ ucwords($news->title) }}</h3>
    						<div class="entry-meta mt-2 mb-3">
    							<span class="author"><a href="#">{{ ucwords($farmlist->title) }}</a></span>
    							<span class="time">{{ date('F d, Y', strtotime($news->created_at)) }}</span>
    						</div>
    					</div>
    	    <p>{{ $news->content }}</p>-->
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