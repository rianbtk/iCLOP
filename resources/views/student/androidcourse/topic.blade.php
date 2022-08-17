<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Aplas Baru - Index</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Roboto:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{asset('lte/dist/vendor/aos/aos.css')}}" rel="stylesheet">
  <link href="{{asset('lte/dist/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
  <link href="{{asset('lte/dist/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
  <link href="{{asset('lte/dist/vendor/boxicons/css/boxicons.min.css')}}" rel="stylesheet">
  <link href="{{asset('lte/dist/vendor/glightbox/css/glightbox.min.css')}}" rel="stylesheet">
  <link href="{{asset('lte/dist/vendor/swiper/swiper-bundle.min.css')}}" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="{{asset('lte/dist/css/style.css')}}" rel="stylesheet">
  <!-- =======================================================
  * Template Name: BizLand - v3.7.0
  * Template URL: https://bootstrapmade.com/bizland-bootstrap-business-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>


  <main id="main">

    <!-- ======= Featured Services Section ======= -->
    <section id="featured-services" class="featured-services">
      <div class="container" data-aos="fade-up">
        <div class="Title-pembelajaran">
          <h1 class="mb-5 text-center">Pembelajaran</h1>
        </div>
        <div class="row">
          <div onclick="window.open('{{URL::to('student/androidcourse/asynctask')}}','android-aplas');" class="col-md-6 col-lg-4 d-flex align-items-stretch mb-5">
            <div class="icon-box" data-aos="fade-up" data-aos-delay="100">
              <div class="icon"><i class='bx bxl-android'></i></div>
              <h4 class="title"><a href="">AsyncTask</a></h4>
              <p class="description">Voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi</p>
            </div>
          </div>

          <div onclick="window.open('{{URL::to('student/androidcourse/firebase')}}','android-aplas');" class="col-md-6 col-lg-4 d-flex align-items-stretch mb-5">
            <div class="icon-box" data-aos="fade-up" data-aos-delay="200">
              <div class="icon"><i class='bx bxl-android'></i></div>
              <h4 class="title"><a href="">Firebase</a></h4>
              <p class="description">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore</p>
            </div>
          </div>

          <div onclick="window.open('Python','mywindow');" class="col-md-6 col-lg-4 d-flex align-items-stretch mb-5">
            <div class="icon-box" data-aos="fade-up" data-aos-delay="200">
              <div class="icon"><i class='bx bxl-python'></i></div>
              <h4 class="title"><a href="">REST API</a></h4>
              <p class="description">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore</p>
            </div>
          </div>
          <div onclick="window.open('Python','mywindow');" class="col-md-6 col-lg-4 d-flex align-items-stretch mb-5">
            <div class="icon-box" data-aos="fade-up" data-aos-delay="200">
              <div class="icon"><i class='bx bxl-python'></i></div>
              <h4 class="title"><a href="">REST API</a></h4>
              <p class="description">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore</p>
            </div>
          </div>

        </div>

      </div>
    </section><!-- End Featured Services Section -->

    <div class="container py-4">
      <div class="copyright">
        <strong>Copyright &copy; 2020 <a href="https://aplas.org">Android Programming Learning Assistance System (APLAS)</a>.</strong> All rights reserved.
      </div>
    </div>
    </footer><!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="{{asset('lte/dist/vendor/purecounter/purecounter.js')}}"></script>
    <script src="{{asset('lte/dist/vendor/aos/aos.js')}}"></script>
    <script src="{{asset('lte/dist/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('lte/dist/vendor/glightbox/js/glightbox.min.js')}}"></script>
    <script src="{{asset('lte/dist/vendor/isotope-layout/isotope.pkgd.min.js')}}"></script>
    <script src="{{asset('lte/dist/vendor/swiper/swiper-bundle.min.js')}}"></script>
    <script src="{{asset('lte/dist/vendor/waypoints/noframework.waypoints.js')}}"></script>
    <script src="{{asset('lte/dist/vendor/php-email-form/validate.js')}}"></script>

    <!-- Template Main JS File -->
    <script src="{{asset('lte/dist/js/main.js')}}"></script>

</body>

</html>