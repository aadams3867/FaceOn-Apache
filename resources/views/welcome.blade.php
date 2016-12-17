@extends('layouts.app')

@section('content')
    <section id="fh5co-hero" class="js-fullheight" style="background-image: url('/images/hero_bg.jpg');" data-next="yes">
        <div class="fh5co-overlay"></div>
        <div class="container">
            <div class="fh5co-intro js-fullheight">
                <div class="fh5co-intro-text">
                    <!--
                        INFO:
                        Change the class to 'fh5co-right-position' or 'fh5co-center-position' to change the layout position
                        Example:
                        <div class="fh5co-right-position">
                    -->
                    <div class="fh5co-left-position">
                        <h2 class="animate-box">Use your face</h2>
                        <h3>to log on securely</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- END #fh5co-hero -->

    <footer id="fh5co-footer">
        <div class="container">
            <div class="row row-bottom-padded-md">

                <div class="fh5co-footer-widget">
                    <h3>Contact Me</h3>
                    <div class="links">
                        <a href="mailto:aadams3867@gmail.com">aadams3867@gmail.com</a> <br>
                        <a href="https://github.com/aadams3867" target="_blank">GitHub</a> <br>
                        <a href="https://www.linkedin.com/in/angela-adams" target="_blank">LinkedIn</a> <br>
                        <a href="https://stackoverflow.com/users/6513357/anazul" target="_blank">Stack Overflow</a> <br>
                    </div>
                </div>

            </div>
        </div>
    </footer>
    <!-- END #fh5co-footer -->
@endsection