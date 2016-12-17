@extends('layouts.app')

@section('content')
<section id="fh5co-login" class="js-fullheight" style="background-image: url('/images/hero_bg.jpg');" data-next="yes">
    <div class="fh5co-overlay"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-8 panel-display">
                <div class="panel panel-default">
                    <div class="panel-heading">Login</div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="password" class="col-md-4 control-label">Password</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control" name="password" required>

                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                                <label for="image" class="col-md-4 control-label">Image for Facial Verification</label>

                                <div class="col-md-6">
                                    <input id="image" type="file" class="form-control" name="image" value="{{ old('image') }}" accept="image/*" capture="camera" required>

                                    @if ($errors->has('image'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('image') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        Login
                                    </button>

                                    <a class="btn btn-link" href="{{ url('/password/reset') }}">
                                        Forgot Your Password?
                                    </a>
                                </div>
                            </div>

                            {{--@if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif--}}

                            <!-- This makes the Bootstrap alert work -->
                            @if (Session::has('status'))
                                <div class="alert-success alert alert-fade">
                                    {{Session::get('status')}}
                                </div>
                            @endif

                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4 panel-display">
                <!-- Video display -->
                <div class="panel panel-default">
                    <div class="panel-heading">Image Capture</div>
                    <div class="panel-body" style="text-align: center; min-height: 500px;">
                        <video id="video-stream" style="width: 265x; height: 200px;"></video>
                        <p style="margin-bottom: 6px"><button type="button" id="snapPic" class="btn btn-primary"><img src="/images/camerai.png" alt="Camera icon"></button></p>
                        <div><canvas id="capture"></canvas></div>
                        <div><img id="canvasImg" alt="Right-click the img to save"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- END #fh5co-login -->

{{-- Scripts --}}
<script src="/js/photo.js"></script>
@endsection
