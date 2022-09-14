<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>iCLOP</title>
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


  <!-- ======= Hero Section ======= -->
  <section id="hero" class="d-flex align-items-center text-center">
    <div class="circles">
      <div class="circle-small-grey"></div>
      <div class="circle-medium-yellow"></div>
      <div class="circle-small-blue"></div>
    </div>
    <div class="container">
      <div class="col-md-12 d-flex justify-content-end">
        <a class="btn btn-danger logout-button" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i></i>Logout</a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST">
          {{ csrf_field() }}
        </form>
      </div>
      <div class="row mb-4 d-flex justify-content-center">
        <div class="col-md-5">
          <img src="{{asset('lte/dist/img/iclop-logo.png')}}" class="img-fluid" alt="Responsive image">
        </div>
      </div>
      <div class="d-flex justify-content-center mt-4">
        <a href="https://www.youtube.com/watch?v=jDDaplaOz7Q" class="glightbox btn-watch-video"><i class="bi bi-play-circle"></i><span>Watch Video</span></a>
      </div>
    </div>
  </section><!-- End Hero -->

  <main id="main">

    <!-- ======= Featured Services Section ======= -->
    <section id="featured-services" class="featured-services">
      <div class="container" data-aos="fade-up">
        <div class="Title-pembelajaran">
          <h1 class="mb-5 text-center">Pembelajaran</h1>
        </div>
        <div class="row">
          <div onclick="window.open('{{URL::to('/student/androidcourse/')}}','android-aplas');" class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5">
            <div class="icon-box" data-aos="fade-up" data-aos-delay="100">
              <div class="icon"><i class='bx bxl-android'></i></div>
              <h4 class="title"><a href="">Android</a></h4>
              <p class="description">Android is an open source operating system, and Google releases the code under the Apache License.[2] The open source code and licensing licenses on Android allow the software to be freely modified and distributed by device makers</p>
            </div>
          </div>

          <div onclick="window.open('{{URL::to('/student/nodejscourse')}}','nodejscourse-aplas');" class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5">
            <div class="icon-box" data-aos="fade-up" data-aos-delay="200">
              <div class="icon"><i class='bx bxl-nodejs'></i></div>
              <h4 class="title"><a href="">NodeJs</a></h4>
              <p class="description">Node.js is an open-source, cross-platform, back-end JavaScript runtime environment that runs on a JavaScript Engine and executes JavaScript code outside a web browser, which was designed to build scalable network applications.</p>
            </div>
          </div>

          <div onclick="window.open('{{URL::to('/student/pythoncourse/')}}','python-aplas');" class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5">
            <div class="icon-box" data-aos="fade-up" data-aos-delay="100">
              <div class="icon"><i class='bx bxl-python'></i></div>
              <h4 class="title"><a href="">Python</a></h4>
              <p class="description">Python is a high-level, general-purpose programming language. Its design philosophy emphasizes code readability with the use of significant indentation. Its language constructs and object-oriented approach aim to help programmers write clear, logical code for small- and large-scale projects.</p>
            </div>
          </div>
          {{-- mysql --}}
          <div onclick="window.open('{{URL::to('/student/sql/')}}','sql-aplas');" class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5">
            <div class="icon-box" data-aos="fade-up" data-aos-delay="300">
              <div class="icon"><i class='bx bxs-data'></i></div>
              <h4 class="title"><a href="">Database</a></h4>
              <p class="description">Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia</p>
            </div>
          </div>

          <div onclick="window.open('english','mywindow');" class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5">
            <div class="icon-box" data-aos="fade-up" data-aos-delay="400">
              <div class="icon"><i class='bx bxs-graduation'></i></div>
              <h4 class="title"><a href="">English</a></h4>
              <p class="description">At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis</p>
            </div>
          </div>

          <div onclick="window.open('{{URL::to('/student/unitycourse/')}}','unitycourse-aplas');" class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5">
            <div class="icon-box" data-aos="fade-up" data-aos-delay="100">
              <div class="icon"><i class='bx bxs-game'></i></div>
              <h4 class="title"><a href="">Unity</a></h4>
              <p class="description">Unity 3D is a software engine for developing games with casual, AR (Augmented Reality) and VR (Virtual Reality) genres. The game results from Unity 3D are cross-platform. Which means you can publish your game to multiple platforms.</p>
            </div>
          </div>

          <!-- <div onclick="window.open('{{URL::to('/student/flutter')}}','fluttercourse-aplas');" class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5">
            <div class="icon-box" data-aos="fade-up" data-aos-delay="200">
              <div class="icon"><i class='bx bxl-flutter'></i></div>
              <h4 class="title"><a href="">Flutter</a></h4>
              <p class="description">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore</p>
            </div>
          </div> -->
          <div onclick="window.open('{{URL::to('/student/fluttercourse/')}}','fluttercourse-aplas');" class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5">
            <div class="icon-box" data-aos="fade-up" data-aos-delay="100">
              <div class="icon"><i class='bx bxl-flutter'></i></div>
              <h4 class="title"><a href="">Flutter Course</a></h4>
              <p class="description">   Flutter was developed by Google which is a multiplatform open source framework with one programming code base, the Dart language. Flutter provides an easy-to-use UI and Widget to build multiplatform apps</p>
            </div>
          </div>

          <!-- // postgree // -->

            <div onclick="window.open('{{URL::to('/student/sqlcourse/')}}','sqlcourse-aplas');" class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5">
            <div class="icon-box" data-aos="fade-up" data-aos-delay="100">
              <div class="icon"><i class='bx bxl-medium-old'></i></div>
              <h4 class="title"><a href="">MysqlCourse</a></h4>
              <p class="description">MySQL is a DBMS (Database Management System) using SQL (Structured Query Language) commands.So, MySQL is a free database server licensed under the GNU General Public License (GPL) so you can use it for personal or commercial purposes without having to pay for an existing license.</p>
            </div>
          </div>

      </div>
    </section><!-- End Featured Services Section -->

    <!-- ======= About Section ======= -->
    <section id="about" class="about section-bg">
      <div class="container" data-aos="fade-up">
        <div class="Title-about">
          <h1 class="mb-5 text-center">About</h1>
        </div>

        <div class="row">
          <div class="col-lg-6" data-aos="fade-right" data-aos-delay="100">
            <img src="{{asset('lte/dist/img/about.jpg')}}" class="img-fluid" alt="">
          </div>
          <div class="col-lg-6 pt-4 pt-lg-0 content d-flex flex-column justify-content-center" data-aos="fade-up" data-aos-delay="100">
            <h3>Welcome To iCLOP.</h3>
            <br>
            <p class="fst-italic">
            &nbsp iCLOP (intelligent computer assisted programming learning platform)
            where your education experience has no limits
            With our easy-to-follow tutorials and examples, you can learn to code in no time. Learn to code by reading tutorials, trying out examples, and writing applications.
            </p>
            <h3>iCLOP Provide.</h3>
            <ul>
              <li>
                <i class="bx bx-cog"></i>
                <div>
                  <h5>Automatic Learning Assistance</h5>
                  <p>Nothing can stop you from learning new technologies.</p>
                </div>
              </li>
              <li>
                <i class="bx bx-cube"></i>
                <div>
                  <h5>Inteligente Guidance</h5>
                  <p>Nothing can stop you from learning new technologies</p>
                </div>
              </li>
              <li>
                <i class="bx bx-edit"></i>
                <div>
                  <h5>Auto Grading</h5>
                  <p>Nothing can stop you from learning new technologies.</p>
                </div>
              </li>
            </ul>
          </div>
        </div>

      </div>
    </section><!-- End About Section -->

    <div class="container py-4">
      <div class="copyright">
      <strong>Copyright &copy; 2022 <a href="http://learning.aplas.online/iclop/">Intelligent Computer Assisted Programming Learning Platform(iCLOP)</a>.</strong> All rights reserved.
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