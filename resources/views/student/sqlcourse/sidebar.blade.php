<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
      <img src="{{asset('lte/dist/img/iclop-logo.png')}}" alt="iCLOP logo" class="brand-image elevation-3"
           style="width:120px;height:60px;">
           <br>
      <span class="brand-text font-weight-light" style="font-size:160%;"> &nbsp;  Python  Course</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{asset('lte/dist/img/avatar3.png')}}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">{{ Auth::user()->name }}</span>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->


               <li class="treeview">
                            <a href="#" class="nav-link" style="background-color:#dce775;color:black;">
                                <i class="nav-icon fab fa-python"></i>
                                <p>&nbsp;<b>Learning Basic Python</b>
                                </p>
                            </a>
                            <ul role="menu" class="nav nav-pills nav-sidebar flex-column">

                                <li class="nav-item">
                                    <a href="{{URL::to('student/pythoncourse/python/task')}}" class="nav-link">
                                        <i class="nav-icon fas fa-angle-right"></i>
                                        <p>
                                            Start Learning <span class="right badge badge-danger">New</span>
                                        </p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{URL::to('student/pythoncourse/python/result')}}" class="nav-link"><i
                                            class="nav-icon fas fa-angle-right"></i>
                                        <p>Learning Results
                                        </p>
                                    </a>
                                </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
