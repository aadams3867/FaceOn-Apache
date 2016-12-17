@extends('layouts.app')

@section('content')
<section id="fh5co-register" class="js-fullheight" style="background-image: url('/images/hero_bg.jpg');" data-next="yes">
    <div class="fh5co-overlay"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-8 panel-display">
                <div class="panel panel-default">
                    <div class="panel-heading">Register</div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ url('/register') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="col-md-4 control-label">Name</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>

                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

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

                            <div class="form-group">
                                <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('gallery_name') ? ' has-error' : '' }}">
                                <label for="gallery_name" class="col-md-4 control-label">Employer Name</label>

                                <div class="col-md-6">
                                    <input id="gallery_name" type="text" class="form-control" name="gallery_name" value="{{ old('gallery_name') }}" required>

                                    @if ($errors->has('gallery_name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('gallery_name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                                <label for="image" class="col-md-4 control-label">Photo ID</label>

                                <div class="col-md-6">
                                    <input id="image" type="file" class="form-control" name="image" value="{{ old('image') }}" required>

                                    @if ($errors->has('image'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('image') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        Register
                                    </button>
                                </div>
                            </div>

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
                        <div><img id="canvasImg" alt="Right-click the img and save as FirstnameLastname.png"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- END #fh5co-register -->

{{-- Scripts --}}
<script src="/js/photo.js"></script>
@endsection