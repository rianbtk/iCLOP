<nav class="main-header navbar navbar-expand navbar-black navbar-white" style="background-color: dark;">
    <!-- Left navbar links -->

    <ul class="navbar-nav" style="font-size:120%;">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="{{URL::to('home')}}" class="nav-link">Home</a>
      </li>
    <li class="nav-item">
      <a href="{{ route('logout')}}" class="nav-link"
        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        Logout
       </a>

       <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
           {{ csrf_field() }}
       </form>

    </li>
    </ul>

  </nav>
