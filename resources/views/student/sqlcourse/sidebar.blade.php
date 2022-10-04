<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <img src="{{ asset('lte/dist/img/logo-aplas.png') }}" alt="APLAS logo" class="brand-image elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-light">WebApps</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('lte/dist/img/avatar3.png') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ Auth::user()->name }}</span>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <!------------------------------------- SQL ------------------------------------>
                <li class="treeview">
                    <a href="#" class="nav-link" style="background-color:#CDF1CB;color:black;">
                        <i class="nav-icon fas fa-database"></i>
                        <p>&nbsp;<b>SQL</b>
                        </p>
                    </a>
                    <ul role="menu" class="nav nav-pills nav-sidebar flex-column pt-1">
                        <li class="nav-item">
                            <a href="{{ route('student sql learning') }}"
                                class="nav-link {{ request()->route()->getName() == 'student sql learning'? 'bg-light': '' }}">
                                <i class="nav-icon fas fa-angle-right"></i>
                                <p>Pembelajaran</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('student sql exercise') }}"
                                class="nav-link {{ request()->route()->getName() == 'student sql exercise'? 'bg-light': '' }}">
                                <i class="nav-icon fas fa-angle-right"></i>
                                <p>Latihan Teori</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('student sql exam') }}"
                                class="nav-link {{ request()->route()->getName() == 'student sql exam'? 'bg-light': '' }}">
                                <i class="nav-icon fas fa-angle-right"></i>
                                <p>Ujian Teori</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('student sql practice') }}"
                                class="nav-link {{ request()->route()->getName() == 'student sql practice'? 'bg-light': '' }}">
                                <i class="nav-icon fas fa-angle-right"></i>
                                <p>Ujian Praktek</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <!------------------------------------- SQL ------------------------------------>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
