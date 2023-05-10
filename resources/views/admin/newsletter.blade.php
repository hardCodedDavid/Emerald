@php
use App\Http\Controllers\Globals as Util;
@endphp

@extends("layouts.admin")

@section('title') News Letter @endsection

@section('newsletter') active @endsection

@section('head')
    <script src="{{ asset('assets/plugins/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/tinymce/jquery.tinymce.min.js') }}"></script>
    <script type="text/javascript">
        tinymce.init({
            selector:"textarea#message",
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

    <div class="row">
        <div class="col-8 mx-auto">
            <form action="{{ route('newsletter.send') }}" method="post">
                @csrf
                <div class="form-group">
                    <label>Subject</label>
                    <input type="text" class="form-control" name="subject" value="" required>
                </div>

                <div class="form-group">
                    <label>Emails (comma seperated)</label>
                    <textarea  class="form-control" name="emails" value="{{old('emails')}}"></textarea>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="users" name="users" value="true">
                    <label class="form-check-label" for="users">
                        Send to all Users
                    </label>
                </div>

                <div class="form-group">
                    <label>Message</label>
                    <textarea class="form-control" id="message" name="message" >{{old('message') ?? ''}}</textarea>
                </div>

                <div class="form-group">
                    <p>
                        To add the user's name to the mailing list message use <code>[name] </code> any where you want the name to appear
                        <br>
                        Note: this only applies to sending mail to all users
                    </p>

                </div>


                <div class="form-group">
                    <button type="submit" class="btn btn-success btn-lg btn-block">Submit</button>
                </div>
            </form>

        </div>
    </div>

@endsection
