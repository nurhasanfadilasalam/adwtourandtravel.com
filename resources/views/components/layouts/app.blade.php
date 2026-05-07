<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SISFO ADW' }}</title>


    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <meta name="description" content="This is meta description">
    <meta name="author" content="Themefisher">
    <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">
    <link rel="icon" href="images/favicon.png" type="image/x-icon">

    <!-- # Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- # CSS Plugins -->
    <link rel="stylesheet" href="{{ asset('frontend/plugins/slick/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/plugins/font-awesome/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/plugins/font-awesome/brands.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/plugins/font-awesome/solid.css') }}">

    <!-- # Main Style Sheet -->
    <link rel="stylesheet" href="{{ asset('frontend/css/style.css') }}">


    @livewireStyles

</head>

<body>
    <!-- /navigation -->
    <!-- navigation -->
    <header class="navigation bg-tertiary">
        <nav class="navbar navbar-expand-xl navbar-light text-center py-3">
            <div class="container">
                <a class="navbar-brand" href="index.html">
                    <img loading="prelaod" decoding="async" class="img-fluid" width="160"
                        src="{{ asset('frontend/images/icon_adw.png') }}" alt="Logo">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation"> <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav m-auto mb-2 mb-lg-0">
                        <li class="nav-item"> <a class="nav-link" href="{{ route('beranda') }}">Beranda</a></li>
                        <li class="nav-item"> <a class="nav-link" href="{{ route('paketumroh') }}">Paket Umroh</a></li>
                        <li class="nav-item "> <a class="nav-link" href="{{ route('testimoni') }}">Testimoni</a></li>
                        <li class="nav-item "> <a class="nav-link" href="{{ route('gallery') }}">Gallery</a></li>
                    </ul>
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-warning">Daftar/Masuk</a>
                        {{-- <div class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="{{ route('beranda') }}" id="btn-warning" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Hi, ADWers </a>
            <div class="dropdown-menu" aria-labelledby="btn-warning">
              <a class="dropdown-item" href="{{ route('profilepeserta') }}">S</a>
              <a class="dropdown-item" href="{{ url('/generate-pdf') }}">Download Sertifikat</a>
            </div>
        </div> --}}
                    @else
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="btn-warning" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">Hi, {{ Auth::user()->name }}</a>
                            <div class="dropdown-menu" aria-labelledby="btn-warning">
                                {{-- <a class="dropdown-item" href="{{ route('testimoni') }}">Testimoni</a> --}}
                                {{-- <a class="dropdown-item" href="{{ route('gallery') }}">Gallery</a> --}}
                                <a class="dropdown-item" href="{{ route('logout') }}">Keluar</a>

                                {{-- <form action="{{route('User.logout')}}" method="post" class="inline">
                Keluar
                @csrf
                @method('POST')
              </form> --}}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </nav>
        </header>
        <!-- /navigation -->


        {{ $slot }}
        <div class="modal applyLoanModal1 fade" id="applyLoan" tabindex="-1" aria-labelledby="applyLoanLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-bottom-0">
                        <h4 class="modal-title" id="exampleModalLabel">How much do you need?</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="#!" method="post">
                            <div class="row">
                                <div class="col-lg-6 mb-4 pb-2">
                                    <div class="form-group">
                                        <label for="loan_amount" class="form-label">Amount</label>
                                        <input type="number" class="form-control shadow-none" id="loan_amount"
                                            placeholder="ex: 25000">
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-4 pb-2">
                                    <div class="form-group">
                                        <label for="loan_how_long_for" class="form-label">How long for?</label>
                                        <input type="number" class="form-control shadow-none" id="loan_how_long_for"
                                            placeholder="ex: 12">
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-4 pb-2">
                                    <div class="form-group">
                                        <label for="loan_repayment" class="form-label">Repayment</label>
                                        <input type="number" class="form-control shadow-none" id="loan_repayment"
                                            disabled>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-4 pb-2">
                                    <div class="form-group">
                                        <label for="loan_full_name" class="form-label">Full Name</label>
                                        <input type="text" class="form-control shadow-none" id="loan_full_name">
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-4 pb-2">
                                    <div class="form-group">
                                        <label for="loan_email_address" class="form-label">Email address</label>
                                        <input type="email" class="form-control shadow-none" id="loan_email_address">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <button type="submit" class="btn btn-primary w-100">Get Your Loan Now</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>




        <footer class="section-sm bg-tertiary">
            <div class="container">
                <div class="row justify-content-between">
                    <div class="col-lg-4 col-md-4 col-6 mb-4">
                        <div class="footer-widget">
                            <h5 class="mb-4 text-primary font-secondary">Hubungi Kami</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2"><a href="service-details.html">Alamat: <br> Jl. Harapan Raya, Ruko No.
                                        137</a>
                                </li>
                                <li class="mb-2"><a href="service-details.html">Nomor Telepon: 6282391586008</a>
                                </li>
                                <li class="mb-2"><a href="service-details.html">WhatsApp: 6282391586008</a>
                                </li>
                                <li class="mb-2"><a href="service-details.html">Email: info@aetduniawisata.com</a>

                            </ul>
                        </div>
                    </div>
                    {{-- <div class="col-lg-2 col-md-4 col-6 mb-4">
				<div class="footer-widget">
					<h5 class="mb-4 text-primary font-secondary">Quick Links</h5>
					<ul class="list-unstyled">
						<li class="mb-2"><a href="#!">About Us</a>
						</li>
						<li class="mb-2"><a href="#!">Contact Us</a>
						</li>
						<li class="mb-2"><a href="#!">Blog</a>
						</li>
						<li class="mb-2"><a href="#!">Team</a>
						</li>
					</ul>
				</div>
			</div> --}}

                    <div class="col-lg-4 col-md-4 col-6 mb-4">
                        <div class="footer-widget">
                            <h5 class="mb-4 text-primary font-secondary">PT. AET DUNIA WISATA</h5>
                            <p>
                                PT. AET Dunia Wisata (ADW TRAVEL) yang di pimpin Oleh ABDUL AZIS ini Bergerak di Bidang
                                Umrah, Haji dan Wisata Domistik beserta Wisata Dunia. PT. AET Dunia Wisata Ini berdiri dari
                                Tahun 2019.
                            </p>
                            <ul class="list-unstyled">
                                <li class="list-inline-item me-4"><a class="text-black" href="#">Privacy Policy</a>
                                </li>
                                <li class="list-inline-item me-4"><a class="text-black" href="#">Terms &amp;
                                        Conditions</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </footer>

        <!-- # JS Plugins -->
        <script src="{{ asset('frontend/plugins/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('frontend/plugins/bootstrap/bootstrap.min.js') }}"></script>
        {{-- hover zoom effect --}}
        <script src="https://cdn.rawgit.com/elevateweb/elevatezoom/master/jquery.elevatezoom.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <script>
        $(document).ready(function() {
            $('.card-img img').elevateZoom();  // Initialize the zoom effect
        });
        </script>

        <!-- Main Script -->
        <script src="{{ asset('frontend/js/script.js') }}"></script>



        @livewireScripts
    </body>

    </html>
