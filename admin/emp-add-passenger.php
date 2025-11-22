<!--Server side code to handle passenger sign up-->
<?php
  session_start();
  include('assets/inc/config.php');
  include('assets/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['admin_id'];

  if(isset($_POST['Create_Profile']))
  {
    $pass_fname=$_POST['pass_fname'];
    #$mname=$_POST['mname'];
    $pass_lname=$_POST['pass_lname'];
    $pass_phone=$_POST['pass_phone'];
    $pass_addr=$_POST['pass_addr'];
    $pass_uname=$_POST['pass_uname'];
    $pass_email=$_POST['pass_email'];
    $pass_pwd=$_POST['pass_pwd'];
    $confirm_pwd=$_POST['confirm_pwd'];

    // Check if passwords match
    if($pass_pwd != $confirm_pwd) {
      $err = "Passwords do not match!";
    } else {
      $pass_pwd = sha1(md5($pass_pwd));
      //sql to insert captured values
      $query="insert into obrs_passenger (pass_fname, pass_lname, pass_phone, pass_addr, pass_uname, pass_email, pass_pwd) values(?,?,?,?,?,?,?)";
      $stmt = $mysqli->prepare($query);
      $rc=$stmt->bind_param('sssssss',$pass_fname, $pass_lname, $pass_phone, $pass_addr, $pass_uname, $pass_email, $pass_pwd);
      $stmt->execute();
      /*
      *Use Sweet Alerts Instead Of This Fucked Up Javascript Alerts
      *echo"<script>alert('Successfully Created Account Proceed To Log In ');</script>";
      */ 
      //declare a varible which will be passed to alert function
      if($stmt)
      {
        $success = "Passenger's Account Has Been Created";
      }
      else {
        $err = "Please Try Again Or Try Later";
      }
    }
    
  }
?>
<!--End Server Side-->

<!DOCTYPE html>
<html lang="en">
<!--Head-->
<?php include('assets/inc/head.php');?>
<!--End Head-->
  <body style="background-color: #F0F0D7;">
    <div class="be-wrapper be-fixed-sidebar">
    <!--Navigation Bar-->
      <?php include('assets/inc/navbar.php');?>
      <!--End Navigation Bar-->

      <!--Sidebar-->
      <?php include('assets/inc/sidebar.php');?>
      <!--End Sidebar-->
      <div class="be-content">
        <div class="page-head">
          <h2 class="page-head-title" style="color: black;">Create Passenger</h2>
          <nav aria-label="breadcrumb" role="navigation">
            <ol class="breadcrumb page-head-nav">
              <li class="breadcrumb-item"><a href="pass-dashboard.php" style="color: black;">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="#" style="color: black;">Passenger</a></li>
              <li class="breadcrumb-item active" style="color: black; text-decoration: underline;">Add</li>
            </ol>
          </nav>
        </div>
        <?php if(isset($success)) {?>
                                <!--This code for injecting an alert-->
                <script>
                            setTimeout(function () 
                            { 
                                swal("Success!","<?php echo $success;?>!","success");
                            },
                                100);
                </script>

        <?php } ?>
        <?php if(isset($err)) {?>
        <!--This code for injecting an alert-->
                <script>
                            setTimeout(function () 
                            { 
                                swal("Failed!","<?php echo $err;?>!","Failed");
                            },
                                100);
                </script>

        <?php } ?>
        <div class="main-content container-fluid">
       
          <div class="row">
            <div class="col-md-12">
              <div class="card" style="background: #D8E4F8; border-radius: 20px; box-shadow: 0 8px 32px rgba(0,0,0,0.15), 0 4px 8px rgba(0,0,0,0.1); border: 1px solid rgba(0,0,0,0.05);">
                <div class="card-header card-header-divider" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-bottom: 2px solid rgba(0,0,0,0.05); padding: 25px; border-radius: 20px 20px 0 0;">Create Passenger Profile<span class="card-subtitle" style="color: #2C3E50; font-size: 1.4em; font-weight: 600; letter-spacing: 0.5px;">Fill All Details</span></div>
                <div class="card-body" style="padding: 40px;">
                  <form method ="POST">
                    <div class="form-group row" style="margin-bottom: 25px;">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;"> First Name</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" name="pass_fname"  id="inputText3" type="text" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                      </div>
                    </div>
                    <div class="form-group row" style="margin-bottom: 25px;">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;">Last Name</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" name="pass_lname"  id="inputText3" type="text" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                      </div>
                    </div>
                    <div class="form-group row" style="margin-bottom: 25px;">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;">Contact Number</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" name="pass_phone"  id="inputText3" type="number" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                      </div>
                    </div>
                    <div class="form-group row" style="margin-bottom: 25px;">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;">Address</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" name="pass_addr"  id="inputText3" type="text" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                      </div>
                    </div>
                    <div class="form-group row" style="margin-bottom: 25px;">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;">Email</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" name="pass_email"  id="inputText3" type="email" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                      </div>
                    </div>
                    <div class="form-group row" style="margin-bottom: 25px;">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;">Username</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" name="pass_uname"  id="inputText3" type="text" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                      </div>
                    </div>
                    
                    <div class="form-group row" style="margin-bottom: 25px;">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="pass_pwd" style="color: #34495E; font-weight: 600; font-size: 1.1em;">Password</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <div class="input-group">
                          <input class="form-control" name="pass_pwd" id="pass_pwd" type="password" style="border-radius: 12px 0 0 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                          <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('pass_pwd', this)" style="border-radius: 0 12px 12px 0; border: 2px solid #E8EEF4; border-left: none;">
                              <i class="fa fa-eye-slash"></i>
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="form-group row" style="margin-bottom: 25px;">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="confirm_pwd" style="color: #34495E; font-weight: 600; font-size: 1.1em;">Confirm Password</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" name="confirm_pwd" id="confirm_pwd" type="password" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                      </div>
                    </div>

                    <div class="col-sm-12">
                      <p class="text-center" style="margin-top: 30px;">
                        <input class="btn btn-space btn-success" value ="Create Passenger" name = "Create_Profile" type="submit" style="border-radius: 12px; padding: 12px 30px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2); transition: all 0.3s ease; margin-right: 20px;">
                        <a href="emp-manage-passengers.php" class="btn btn-space btn-danger" style="border-radius: 12px; padding: 12px 30px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2); transition: all 0.3s ease;">Cancel</a>
                      </p>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!--footer-->
        <?php include('assets/inc/footer.php');?>
        <!--EndFooter-->
      </div>

    </div>
    <script src="assets/lib/jquery/jquery.min.js" type="text/javascript"></script>
    <script src="assets/lib/perfect-scrollbar/js/perfect-scrollbar.min.js" type="text/javascript"></script>
    <script src="assets/lib/bootstrap/dist/js/bootstrap.bundle.min.js" type="text/javascript"></script>
    <script src="assets/js/app.js" type="text/javascript"></script>
    <script src="assets/lib/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
    <script src="assets/lib/jquery.nestable/jquery.nestable.js" type="text/javascript"></script>
    <script src="assets/lib/moment.js/min/moment.min.js" type="text/javascript"></script>
    <script src="assets/lib/datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="assets/lib/select2/js/select2.min.js" type="text/javascript"></script>
    <script src="assets/lib/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script src="assets/lib/bootstrap-slider/bootstrap-slider.min.js" type="text/javascript"></script>
    <script src="assets/lib/bs-custom-file-input/bs-custom-file-input.js" type="text/javascript"></script>
    <script
        src="https://kit.fontawesome.com/f766ed9c4c.js"
        crossorigin="anonymous"></script>
    <script type="text/javascript">
      $(document).ready(function(){
      	//-initialize the javascript
      	App.init();
      	App.formElements();
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