
<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  <title>L.M.S</title>

  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="/css/app.css">
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"> 
     </script>
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet">

</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper" id="app">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
     <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
      <img src="./img/fire.png" alt="Laravel Vue Js" class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light">L.M.S</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="./img/person.png" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">{{Auth()->user()->name}}</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          @can('isAdmin')

          <li class="nav-item has-treeview">
            <router-link to="/home" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt yellow"></i>
              <p>
                Dashboard
              </p>
            </router-link>
          </li>

          @endcan

          @canany(['isTeacher', 'isStudent'])

          <li class="nav-item has-treeview">
            <router-link to="/profile" class="nav-link">
              <i class="nav-icon fas fa-user orange"></i>
              <p>
                Profile
              </p>
            </router-link>
          </li>

          @endcan

          @can('isAdmin')

          <li class="nav-item has-treeview">
            <router-link to="/contacts" class="nav-link">
              <i class="nav-icon fas fa-address-book orange"></i>
              <p>
                Contacts
              </p>
            </router-link>
          </li>

          <li class="nav-item has-treeview">
            <router-link to="/programs" class="nav-link">
              <i class="nav-icon fas fa-graduation-cap yellow"></i>
              <p>
                Programs
              </p>
            </router-link>
          </li>

          <li class="nav-item has-treeview">
            <router-link to="/schools" class="nav-link">
              <i class="nav-icon fas fa-school orange"></i>
              <p>
                Schools
              </p>
            </router-link>
          </li>

          <li class="nav-item has-treeview">
            <router-link to="/classrooms" class="nav-link">
              <i class="nav-icon fas fa-chalkboard yellow"></i>
              <p>
                Classrooms
              </p>
            </router-link>
          </li>

          <li class="nav-item has-treeview">
            <router-link to="/sections" class="nav-link">
              <i class="nav-icon fas fa-sort-alpha-down orange"></i>
              <p>
                Sections
              </p>
            </router-link>
          </li>

          <li class="nav-item has-treeview">
            <router-link to="/courses" class="nav-link">
              <i class="nav-icon fas fa-book yellow"></i>
              <p>
                Courses
              </p>
            </router-link>
          </li>

          <li class="nav-item has-treeview">
            <router-link to="/users" class="nav-link">
              <i class="nav-icon fas fa-users orange"></i>
              <p>
                Users
              </p>
            </router-link>
          </li>

          @endcan

          @canany(['isAdmin', 'isStudent'])

          <li class="nav-item has-treeview">
            <router-link to="/students" class="nav-link">
              <i class="nav-icon fas fa-user-graduate yellow"></i>
              <p>
                Student
              </p>
            </router-link>
          </li>

          @endcanany

          @canany(['isAdmin', 'isTeacher'])

          <li class="nav-item has-treeview">
            <router-link to="/teachers" class="nav-link">
              <i class="nav-icon fas fa-chalkboard-teacher orange"></i>
              <p>
                Teachers
              </p>
            </router-link>
          </li>

          @endcanany
          
          @canany(['isAdmin', 'isTeacher','isStudent'])

          <li class="nav-item has-treeview">
            <router-link to="/sessions" class="nav-link">
              <i class="nav-icon fas fa-video yellow"></i>
              <p>
                Session
              </p>
            </router-link>
          </li>

          <li class="nav-item has-treeview">
            <router-link to="/lectureIndex" class="nav-link">
              <i class="nav-icon fab fa-youtube red"></i>
              <p>
                Lectures
              </p>
            </router-link>
          </li>

          <li class="nav-item has-treeview">
            <a id="att" href="#" class="nav-link">
              <i class="nav-icon fas fa-user-clock orange"></i>
              <p>
                Mark Today's Attendance
              </p>
            </a>
           </li>

          @endcanany

          <li class="nav-item has-treeview">
            <a class="nav-link" href="{{ route('logout') }}"
              onclick="event.preventDefault();
              document.getElementById('logout-form').submit();">
              <i class=" nav-icon fas fa-power-off red"></i>
              <p>
                {{ __('Logout') }}
              </p>
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              @csrf
            </form>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <router-view></router-view>
        <vue-progress-bar></vue-progress-bar>
        <div class="modal fade" id="AddAttendance" aria-hidden="true">
            <div class="modal-dialog modal-md">
              <div class="modal-content">
                <div class="modal-header">
                  <h4 class="modal-title">Attendance</h4>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <!-- /Add Form Content -->
                  <div class="row" style="margin: auto; width: 40%;">
                      <button id="check-in">
                        <i class="fas fa-sign-in-alt fa-9x"></i>
                        Check-In
                      </button>
                      <button id="check-out" class="btn-warning">
                        <i class="fas fa-sign-out-alt fa-9x"></i>
                        Check-Out
                      </button>
                      <h5 class = "" id="attendance_recorded">You have checked-out</h5>
                      <span id="check_in_time" style="font-size: 22px;"></span>
                  </div>

                  <!-- /.Add Form Content -->
                </div>
              </div>
              <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
            </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->


  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
      Anything you want
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; 2014-2019 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

@auth
<script>
  window.user = @json(auth()->user())
</script>
@endauth

<!-- REQUIRED SCRIPTS -->

<script src="/js/app.js"></script>
<script type="text/javascript">
var user_id = <?php echo auth()->user()->id; ?>;

$('#att').on('click',function () {
  $.ajax({
      type : 'get',
      url : '{{URL::to("api/statusURL")}}',
      data:{"user_id":user_id},
      dataType: "JSON",
        success:function(data)
        {
          $('#AddAttendance').modal('show');
          if(data.length > 0)
          {
            if(data[0].check_out)
            {
              $('#attendance_recorded').show();
              $('#check-in').hide();
              $('#check-out').hide();
            }
            else
            {
              $('#attendance_recorded').hide();
              $('#check-in').hide();
              $('#check-out').show();
            }
          }
          else
          {
            $('#attendance_recorded').hide();
            $('#check-out').hide();
            $('#check-in').show(); 
          }
        }
    });               
});

$('#check-in').on('click',function () {
  $('#check-in').hide();
  $.ajax({
      type : 'get',
      url : '{{URL::to("api/checkInURL")}}',
      data:{"user_id":user_id},
        success:function(data){
          $('#check_in_time').text('Check-In Time:'+data);
          $('#check_in_time').fadeOut(4000,function(){
          $('#check-out').show();
            
          });
          location.reload();
        }
    });                          
});

$('#check-out').on('click',function () {
  $('#check-out').hide();
  $.ajax({
      type : 'get',
      url : '{{URL::to("api/checkOutURL")}}',
      data:{"user_id":user_id},
      dataType: "JSON",
        success:function(data){}
    });
  $('#attendance_recorded').show();                           
});            

</script>

</body>
</html>
