<!-- Footer -->
<footer class="main-footer style-seven">
    <div class="auto-container">

        <!-- Widgets Section -->
        <div class="widgets-section">
            <div class="row clearfix">

                <!-- Big Column -->
                <div class="big-column col-lg-6 col-md-12 col-sm-12">
                    <div class="row clearfix">
                        <!-- Footer Column -->
                        <div class="footer-column col-lg-7 col-md-6 col-sm-12">
                            <div class="footer-widget logo-widget">
                                <div class="logo">
                                    <a href="{{ route('home') }}"><img
                                            src="{{ asset('frontEnd/images/header-logo.png') }}" alt="" /></a>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Column -->
                        <div class="footer-column col-lg-4 col-md-6 col-sm-12">
                            <div class="footer-widget links-widget">
                                <ul class="footer-links">
                                    <li><a href="{{ route('home') }}">Home</a></li>
                                    <li><a href="{{ route('about-us') }}">About Us</a></li>
                                    <li><a href="{{ route('gallery') }}">Gallery</a></li>
                                    <li><a href="">Buy</a></li>
                                    <li><a href="">Sell</a></li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Big Column -->
                <div class="big-column col-lg-6 col-md-6 col-sm-12">
                    <div class="row clearfix">
                        <div class="footer-column col-lg-5 col-md-6 col-sm-12">
                            <div class="footer-widget links-widget">
                                <ul class="footer-links">
                                     <li><a href="{{ route('privacy-policy') }}">Privacy Policy</a></li>
                                    <li><a href="{{ route('terms-conditions') }}">Terms And Conditions</a></li>
                                    <li><a href="{{ route('contact-us') }}">Contact Us</a></li>
                                </ul>
                            </div>
                        </div>


                        <!-- Footer Column -->
                        <div class="footer-column col-lg-7 col-md-6 col-sm-12">
                            <div class="footer-widget newsletter-widget">
                                <ul class="contact-list">
                                    <li><span class="icon fa fa-map-marker"></span>{{env('PROJECT_TITLE')}}, Miyapur, Hyderabad</li>
                                    <li><span class="icon fa fa-phone"></span>
                                        +91 0000000000 <br> +91 0000000000</li>
                                    <li><span class="icon fa fa-envelope"></span>
                                        info@{{env('PROJECT_TITLE')}}.com</li>
                                </ul>

                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>
    <div class="auto-container">
        <div class="footer-bottom">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="copyright">2024 &copy; All rights reserved by <a href="#">{{env('PROJECT_TITLE')}}</a></div>

                <!-- Social Box -->
                <ul class="header-social_box-two">
                    <li><a href="#" class="fa-brands fa-facebook-f fa-fw"></a></li>
                    <li><a href="#" class="fa-solid fa-instagram fa-fw"></a></li>
                    <li><a href="#" class="fa-brands fa-youtube fa-fw"></a></li>
                </ul>

            </div>
        </div>
    </div>
</footer>
<!-- Footer -->
