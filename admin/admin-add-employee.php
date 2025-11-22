<!--Server side code to give us and hold session-->
<?php
  session_start();
  include('assets/inc/config.php');
  include('assets/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['admin_id'];

  if(isset($_POST['Create_Profile'])) {
    $emp_fname=$_POST['emp_fname'];
    $emp_lname=$_POST['emp_lname'];
    $emp_nat_idno=$_POST['emp_nat_idno'];
    $emp_phone=$_POST['emp_phone'];
    $emp_addr = $_POST['emp_addr'];
    $emp_uname=$_POST['emp_uname'];
    $emp_email=$_POST['emp_email'];
    $emp_dept=$_POST['emp_dept'];
    $emp_pwd=$_POST['emp_pwd'];
    $confirm_pwd=$_POST['confirm_pwd'];

    if($emp_pwd != $confirm_pwd) {
      $err = "Passwords do not match";
    } else {
      $emp_pwd = sha1(md5($emp_pwd));
      $query="insert into obrs_employee (emp_fname, emp_lname, emp_phone, emp_addr, emp_nat_idno, emp_uname, emp_email, emp_dept, emp_pwd) values(?,?,?,?,?,?,?,?,?)";
      $stmt = $mysqli->prepare($query);
      $rc=$stmt->bind_param('sssssssss',$emp_fname, $emp_lname, $emp_phone, $emp_addr, $emp_nat_idno, $emp_uname, $emp_email, $emp_dept, $emp_pwd);
      $stmt->execute();

      if($stmt) {
        $success = "Employee Account Created";
      } else {
        $err = "Please Try Again Or Try Later";
      }
    }
  }
?>
<!--End Server Side-->

<!DOCTYPE html>
<html lang="en">
<!--Head-->
<?php include("assets/inc/head.php"); ?>
<!--End Head-->

<body style="background-color: #F0F0D7;">
  <div class="be-wrapper be-fixed-sidebar">
    <!--Navbar-->
    <?php include("assets/inc/navbar.php"); ?>
    <!--End Nav Bar-->

    <!--Sidebar-->
    <?php include('assets/inc/sidebar.php'); ?>
    <!--End Sidebar-->

    <div class="be-content">
      <div class="page-head">
        <h2 class="page-head-title" style="color: black;">Add Employee</h2>
        <nav aria-label="breadcrumb" role="navigation">
          <ol class="breadcrumb page-head-nav">
            <li class="breadcrumb-item"><a href="pass-dashboard.php" style="color: black;">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#" style="color: black;">Employee</a></li>
            <li class="breadcrumb-item active" style="color: black; text-decoration: underline;">Add</li>
          </ol>
        </nav>
      </div>

      <?php if(isset($success)) {?>
        <script>
          setTimeout(function() { 
            swal("Success!","<?php echo $success;?>!","success");
          }, 100);
        </script>
      <?php } ?>

      <?php if(isset($err)) {?>
        <script>
          setTimeout(function() { 
            swal("Failed!","<?php echo $err;?>!","error");
          }, 100);
        </script>
      <?php } ?>

      <div class="main-content container-fluid">
        <div class="row justify-content-center">
          <div class="col-md-8">
            <div class="card" style="background: linear-gradient(135deg, #E0EAFC 0%, #CFDEF3 100%); border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
              <div class="card-header text-center" style="background: rgba(255,255,255,0.1); border-bottom: 2px solid rgba(44,62,80,0.1); padding: 30px;">
                <h3 style="margin: 0; color: #2C3E50; font-weight: 700;">Create Employee Profile</h3>
                <p style="margin: 10px 0 0; color: #34495E;">Fill All Details</p>
              </div>

              <div class="card-body" style="padding: 40px;">
                <form method="POST">
                  <div class="form-group row mb-4">
                    <label class="col-12 col-sm-3 col-form-label" style="color: #2C3E50; font-weight: 600;">First Name</label>
                    <div class="col-12 col-sm-9">
                      <input class="form-control" name="emp_fname" type="text" style="border-radius: 10px; padding: 12px;">
                    </div>
                  </div>

                  <div class="form-group row mb-4">
                    <label class="col-12 col-sm-3 col-form-label" style="color: #2C3E50; font-weight: 600;">Last Name</label>
                    <div class="col-12 col-sm-9">
                      <input class="form-control" name="emp_lname" type="text" style="border-radius: 10px; padding: 12px;">
                    </div>
                  </div>

                  <div class="form-group row mb-4">
                    <label class="col-12 col-sm-3 col-form-label" style="color: #2C3E50; font-weight: 600;">National ID</label>
                    <div class="col-12 col-sm-9">
                      <input class="form-control" name="emp_nat_idno" type="text" style="border-radius: 10px; padding: 12px;">
                    </div>
                  </div>

                  <div class="form-group row mb-4">
                    <label class="col-12 col-sm-3 col-form-label" style="color: #2C3E50; font-weight: 600;">Phone Number</label>
                    <div class="col-12 col-sm-9">
                      <input class="form-control" name="emp_phone" type="tel" pattern="[0-9]{10}" maxlength="10" oninput="this.value=this.value.replace(/[^0-9]/g,'')" placeholder="Enter 10 digit phone number" style="border-radius: 10px; padding: 12px;">
                    </div>
                  </div>

                  <div class="form-group row mb-4">
                    <label class="col-12 col-sm-3 col-form-label" style="color: #2C3E50; font-weight: 600;">Address</label>
                    <div class="col-12 col-sm-9">
                      <input class="form-control" name="emp_addr" type="text" style="border-radius: 10px; padding: 12px;">
                    </div>
                  </div>

                  <div class="form-group row mb-4">
                    <label class="col-12 col-sm-3 col-form-label" style="color: #2C3E50; font-weight: 600;">Department</label>
                    <div class="col-12 col-sm-9">
                      <input class="form-control" name="emp_dept" type="text" style="border-radius: 10px; padding: 12px;">
                    </div>
                  </div>

                  <div class="form-group row mb-4">
                    <label class="col-12 col-sm-3 col-form-label" style="color: #2C3E50; font-weight: 600;">Email</label>
                    <div class="col-12 col-sm-9">
                      <input class="form-control" name="emp_email" type="email" style="border-radius: 10px; padding: 12px;">
                    </div>
                  </div>

                  <div class="form-group row mb-4">
                    <label class="col-12 col-sm-3 col-form-label" style="color: #2C3E50; font-weight: 600;">Username</label>
                    <div class="col-12 col-sm-9">
                      <input class="form-control" name="emp_uname" type="text" style="border-radius: 10px; padding: 12px;">
                    </div>
                  </div>

                  <div class="form-group row mb-4">
                    <label class="col-12 col-sm-3 col-form-label" style="color: #2C3E50; font-weight: 600;">Password</label>
                    <div class="col-12 col-sm-9">
                      <div class="input-group">
                        <input class="form-control" name="emp_pwd" id="emp_pwd" type="password" style="border-radius: 12px 0 0 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                        <div class="input-group-append">
                          <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('emp_pwd', this)" style="border-radius: 0 12px 12px 0; border: 2px solid #E8EEF4; border-left: none;">
                            <i class="fa fa-eye-slash"></i>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="form-group row mb-4">
                    <label class="col-12 col-sm-3 col-form-label" style="color: #2C3E50; font-weight: 600;">Confirm Password</label>
                    <div class="col-12 col-sm-9">
                      <div class="input-group">
                        <input class="form-control" name="confirm_pwd" id="confirm_pwd" type="password" style="border-radius: 12px 0 0 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                        <div class="input-group-append">
                          <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_pwd', this)" style="border-radius: 0 12px 12px 0; border: 2px solid #E8EEF4; border-left: none;">
                            <i class="fa fa-eye-slash"></i>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-12 text-center">
                      <button type="submit" name="Create_Profile" class="btn btn-success" style="padding: 12px 30px; border-radius: 30px; background: linear-gradient(135deg, #50C878 0%, #228B22 100%); border: none; box-shadow: 0 4px 15px rgba(80,200,120,0.3); margin-right: 10px;">
                        <i class="fa fa-save mr-2"></i> Create Profile
                      </button>
                      <a href="admin-manage-employee.php" class="btn btn-danger" style="padding: 12px 30px; border-radius: 30px; background: linear-gradient(135deg, #FF6B6B 0%, #dc3545 100%); border: none; box-shadow: 0 4px 15px rgba(220,53,69,0.3);">
                        <i class="fa fa-times mr-2"></i> Cancel
                      </a>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--Footer-->
      <?php include('assets/inc/footer.php');?>
      <!--End Footer-->
    </div>
  </div>

  <script src="assets/lib/jquery/jquery.min.js" type="text/javascript"></script>
  <script src="assets/lib/perfect-scrollbar/js/perfect-scrollbar.min.js" type="text/javascript"></script>
  <script src="assets/lib/bootstrap/dist/js/bootstrap.bundle.min.js" type="text/javascript"></script>
  <script src="assets/js/app.js" type="text/javascript"></script>
  <script src="https://kit.fontawesome.com/f766ed9c4c.js" crossorigin="anonymous"></script>
  <script type="text/javascript">
    $(document).ready(function(){
      App.init();
    });

    function togglePassword(inputId, button) {
      var x = document.getElementById(inputId);
      var icon = button.querySelector('i');
      if (x.type === "password") {
        x.type = "text";
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      } else {
        x.type = "password";
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      }
    }
  </script>
</body>

</html>