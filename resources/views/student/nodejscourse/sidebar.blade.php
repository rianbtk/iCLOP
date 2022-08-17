<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
      <img src="{{asset('lte/dist/img/iclop-logo.png')}}" alt="iCLOP logo" class="brand-image elevation-3"
           style="width:120px;height:60px;">
           <br>
      <span class="brand-text font-weight-light" style="font-size:160%;"> &nbsp;  Nodejs  Course</span>
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
	<a href="#" class="nav-link" style="background-color:powderblue;color:black;"> 
	<i class="nav-icon fas fa-space-shuttle"></i>
	<p><b>Start Learning</b></p>  
	</a> 
	<ul role="menu" class="nav nav-pills nav-sidebar flex-column"> 
		<li class="nav-item">
			<a href="{{URL::to('student/nodejscourse/tasks')}}" class="nav-link"><i class="nav-icon fas fa-angle-right"></i>
			<p>Download Materials</p>
			</a > 
		</li> 

		<li class="nav-item">
            <a href="{{URL::to('student/nodejscourse/results')}}" class="nav-link">
              <i class="nav-icon fas fa-angle-right"></i>
              <p>Submit Your Project </p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
