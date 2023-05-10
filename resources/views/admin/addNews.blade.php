@php
use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.admin")

@section('title') News | {{ (isset($edit))?'Edit News':'Add News' }} @endsection

@section('news') active @endsection

@section('head')
<script src="{{ asset('assets/plugins/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('assets/plugins/tinymce/jquery.tinymce.min.js') }}"></script>
<script type="text/javascript">
  tinymce.init({
    selector:"textarea",
    themes: "modern",
    skin: "oxide",
    height:300,
    plugins:["advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker","searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking","save table contextmenu directionality emoticons template paste textcolor"],
    toolbar:"insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l      ink image | print preview media fullpage | forecolor backcolor emoticons",
    style_formats:[{title:"Bold text",inline:"b"},{title:"Red text",inline:"span",styles:{color:"#ff0000"}},{title:"Red header",block:"h1",styles:{color:"#ff0000"}},{title:"Example 1",inline:"span",classes:"example1"},{title:"Example 2",inline:"span",classes:"example2"},{title:"Table styles"},{title:"Table row 1",selector:"tr",classes:"tablerow1"}]
	});
  </script>
@endsection

@section('content')
<div class="section-body">
	<div class="row">
		<div class="col-12 col-md-6 col-lg-6">
			<div class="card">
				<div class="card-header">
					<h4>{{ (isset($edit))?'Edit':'New' }} News</h4>
				</div>
				<div class="card-body">
					@if(isset($edit))
					<form action="{{ route('news.edit') }}" method="post">
						@csrf
						<div class="form-group">
							<label>Title</label>
							<input type="text" class="form-control" name="title" required value="{{ $new->title }}">
							<input type="hidden" name="id" required value="{{ $new->id }}">
						</div>
						<div class="form-group">
							<label>Farmlists </label>
							<select name="farmlist" class="form-control">
							    @foreach($farmlists as $farmlist)
							    <option value="{{ $farmlist->slug }}" {{ ($new->farmlist == $farmlist->slug)?'selected':'' }}>{{ ucwords($farmlist->title) }}</option>
							    @endforeach
							</select>
						</div>
						<div class="form-group">
							<label>Content</label>
							<textarea class="form-control" name="content" required>{{ $new->content }}</textarea>
						</div>
						<div class="form-group">
							<button type="submit" class="btn btn-success btn-lg  btn-block">Submit</button>
						</div>
					</form>
					@else
					<form action="{{ route('news.add') }}" method="post">
						@csrf
						<div class="form-group">
							<label>Title</label>
							<input type="text" class="form-control" name="title" required>
						</div>
						<div class="form-group">
							<label>Farmlists </label>
							<select name="farmlist" class="form-control">
							    @foreach($farmlists as $farmlist)
							    <option value="{{ $farmlist->slug }}">{{ ucwords($farmlist->title) }}</option>
							    @endforeach
							</select>
						</div>
						<div class="form-group">
							<label>Content</label>
							<textarea class="form-control" name="content" required></textarea>
						</div>
						<div class="form-group">
							<button type="submit" class="btn btn-success btn-lg  btn-block">Submit</button>
						</div>
					</form>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
