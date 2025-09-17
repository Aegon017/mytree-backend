<!-- Main Header / Header Style Three -->
<header class="main-header header-style-three">
    <!-- Header Upper -->
    <div class="header-upper">
        <div class="auto-container">
            <div class="inner-container d-flex justify-content-between align-items-center flex-wrap">
                <!-- Logo Box -->
                <div class="logo"><a href="{{ route('home') }}"><img src="{{ asset('frontEnd/images/header-logo.png') }}"
                            alt="" title=""></a>
                </div>
                <div class="headsocialicons">
                    <ul>
                        <li><a href="https://www.facebook.com/"><i
                                    class="fa-brands fa-square-facebook"></i></a></li>
                        <li><a href="https://www.instagram.com//"><i class="fab fa-instagram"></i></a></li>
                        <li><a href="https://www.youtube.com/"><i
                                    class="fa-brands fa-square-youtube"></i></a></li>
                    </ul>
                </div>
                <!-- Upper Right -->
                <div class="upper-right d-flex align-items-center flex-wrap">
                    <!-- Info Box -->
                    <div class="upper-column info-box">
                        <div class="icon-box flaticon-phone-call"></div>
                        <strong><a href="tel:+210-123-451">Call Us:</a></strong>
                        <p>+91 00000000000</p>
                    </div>
                    <!-- Info Box -->
                    <div class="upper-column info-box">
                        <div class="icon-box flaticon-mail"></div>
                        <strong>Mail us for help:</strong>
                        <p>info@info.com</p>
                    </div>
                    <!-- Info Box -->
                    <div class="upper-column info-box">
                        <div class="icon-box flaticon-pin"></div>
                        <strong>Address:</strong>
                        <p>Miyapur, Hyderabad</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Header Lower -->
    <div class="header-lower">

        <div class="auto-container">
            <div class="inner-container">

                <div class="nav-outer d-flex justify-content-between align-items-center flex-wrap">

                    <!-- Main Menu -->
                    <nav class="main-menu show navbar-expand-md">
                        <div class="navbar-header">
                            <button class="navbar-toggler" type="button" data-toggle="collapse"
                                data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                                aria-expanded="false" aria-label="Toggle navigation">
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                        </div>

                        <div class="navbar-collapse collapse clearfix" id="navbarSupportedContent">
                            <ul class="navigation clearfix">
                                <li><a href="{{ route('home') }}">Home</a></li>
                                <li><a href="{{ route('about-us') }}">About us</a></li>
                                <li><a href="{{ route('gallery') }}">Gallery</a></li>
                                <li><a href="">Buy</a></li>
                                <li><a href="">Sell</a></li>
                                <li class="dropdown"><a href="#">Agents</a>
                                    <ul>
                                        <li><a href="">Find an Agent</a></li>
                                         <li><a href="">Join as a PK Agent</a></li>
                                        <li><a href="">Join our referral network</a></li>
                                        <!-- <li><a href="{{ route('pricing') }}">PRICING</a></li> -->
                                    </ul>
                                </li>
                                <li><a href="{{ route('contact-us') }}">Contact</a></li>
                                <li><a href="">PK Services</a></li>
                            </ul>
                        </div>

                    </nav>
                    <!-- Main Menu End-->

                    <div class="outer-box d-flex align-items-center">
                        <!-- Cart Box -->
                        <div class="cart-box">
                            <a href="{{ route('locations') }}"> <button class="booknowbutton">Learn  More</button></a>
                        </div>
                    </div>

                    <!-- Mobile Navigation Toggler -->
                    <div class="mobile-nav-toggler"><span class="icon fa-solid fa-bars fa-fw"></span></div>

                </div>

            </div>

        </div>
    </div>
    <!-- End Header Lower -->

    <!-- Sticky Header  -->
    <div class="sticky-header">
        <div class="auto-container">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Logo -->
                <div class="logo">
                    <a href="{{ route('home') }}" title=""><img
                            src="{{ asset('frontEnd/images/sticky-header-logo.png') }}" alt=""
                            title=""></a>
                </div>

                <!-- Right Col -->
                <div class="right-box d-flex align-items-center flex-wrap">
                    <!-- Main Menu -->
                    <nav class="main-menu">
                        <!--Keep This Empty / Menu will come through Javascript-->
                    </nav>
                    <!-- Main Menu End-->

                    <div class="outer-box d-flex align-items-center">

                        <!-- Cart Box -->
                        <div class="cart-box">
                            <a href="{{ route('locations') }}"> <button class="booknowbutton">BOOK NOW</button></a>
                        </div>

                        <!-- Mobile Navigation Toggler -->
                        <div class="mobile-nav-toggler"><span class="icon fa-solid fa-bars fa-fw"></span></div>

                    </div>

                </div>

            </div>
        </div>
    </div>
    <!-- End Sticky Menu -->

    <!-- Mobile Menu  -->
    <div class="mobile-menu">
        <div class="menu-backdrop"></div>
        <div class="close-btn"><span class="icon fas fa-window-close fa-fw"></span></div>
        <nav class="menu-box">
            <div class="nav-logo"><a href="index.html"><img style="max-width: 60%;"
                        src="{{ asset('frontEnd/images/sticky-header-logo.png') }}" alt="" title=""></a>
            </div>
            <!-- Search -->
            <div class="menu-outer">
                <!--Here Menu Will Come Automatically Via Javascript / Same Menu as in Header-->
            </div>
        </nav>
    </div>
    <!-- End Mobile Menu -->

</header>
<!-- End Main Header -->
