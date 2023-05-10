@php
use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.user")

@section('title') {{ ucwords($farmlist->title) }} | updates @endsection

@section('head')
<link rel="stylesheet" href="{{ asset('assets/bundles/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection

@section('news') active @endsection

@section('content')
<div class="section-body">
	<h2 class="section-title">{{ ucwords($farmlist->title) }} Updates</h2>
	<form method="get" class="mb-4">
	    <input type="text" name="q" class="form-control" placeholder="Search update here">
	</form>
	<div class="row">
	    @php
	    $i = 1;
	    @endphp
	    @foreach($news as $key => $new)
	    @php
	    $farmlist = Util::getFarmlist($new->farmlist);
	    @endphp
		<div class="col-12 col-sm-6 col-md-6 col-lg-3">
			<article class="article article-style-b">
				<div class="article-header">
					<div class="article-image" data-background="{{ asset($farmlist->cover) }}">
					</div>
					@if($key == 0)
					<div class="article-badge">
						<div class="article-badge-item bg-danger"><i class="fas fa-fire"></i> Latest</div>
					</div>
					@endif
				</div>
				<div class="article-details">
					<div class="article-title">
						<h2><a href="/news/{{ $new->slug }}">{{ ucwords($new->title) }}</a></h2>
					</div>
					<!--<p>{!! substr($new->content, 0, 50) !!}</p>-->
					<div class="article-cta">
						<a href="/news/{{ $new->slug }}">Read More <i class="fas fa-chevron-right"></i></a>
					</div>
				</div>
			</article>
		</div>
		@endforeach
	</div>
</div>

@endsection

@section('foot')
<script src="{{ asset('assets/bundles/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/bundles/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/js/page/datatables.js') }}"></script>
@endsection