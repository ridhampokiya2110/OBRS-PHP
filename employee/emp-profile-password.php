<?php
    session_start();
    include('assets/inc/config.php');
    //date_default_timezone_set('Africa /Nairobi');
    include('assets/inc/checklogin.php');
    check_login();
    $aid=$_SESSION['emp_id'];

    if(isset($_POST['Update_Password'])) {
        $aid=$_SESSION['emp_id'];
        $old_pwd=sha1(md5($_POST['old_pwd']));
        $new_pwd=$_POST['new_pwd'];
        $confirm_pwd=$_POST['confirm_pwd'];

        // Verify old password
        $verify_pwd = "SELECT emp_pwd FROM obrs_employee WHERE emp_id=? AND emp_pwd=?";
        $stmt = $mysqli->prepare($verify_pwd);
        $stmt->bind_param('is', $aid, $old_pwd);
        $stmt->execute();
        $stmt->store_result();

        if($stmt->num_rows == 0) {
            $err = "Current password is incorrect";
        }
        else if($new_pwd != $confirm_pwd) {
            $err = "New passwords do not match";
        }
        else {
            $new_pwd = sha1(md5($new_pwd));
            $query="update obrs_employee set emp_pwd = ? where emp_id=?";
            $stmt = $mysqli->prepare($query);
            $rc=$stmt->bind_param('si', $new_pwd, $aid);
            $stmt->execute();
            if($stmt) {
                $succ1 = "Password Updated Successfully";
            }
            else {
                $err = "Please Try Again Later";
            }
        }
    }
?>
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
          <h2 class="page-head-title" style="color: black;">Change Password</h2>
          <nav aria-label="breadcrumb" role="navigation">
            <ol class="breadcrumb page-head-nav">
              <li class="breadcrumb-item"><a href="pass-dashboard.php" style="color: black;">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="#" style="color: black;">Profile</a></li>
              <li class="breadcrumb-item active" style="color: black; text-decoration: underline;">Change Password</li>
            </ol>
          </nav>
        </div>
        <?php if(isset($succ1)) {?>
                                <!--This code for injecting an alert-->
                <script>
                            setTimeout(function () 
                            { 
                                swal("Success!","<?php echo $succ1;?>!","success");
                            },
                                100);
                </script>

        <?php } ?>
        <?php if(isset($err)) {?>
        <!--This code for injecting an alert-->
                <script>
                            setTimeout(function () 
                            { 
                                swal("Failed!","<?php echo $err;?>!","error");
                            },
                                100);
                </script>

        <?php } ?>
        <div class="main-content container-fluid">
        <?php
            $aid=$_SESSION['emp_id'];
            $ret="select * from obrs_employee where emp_id=?";
            $stmt= $mysqli->prepare($ret) ;
            $stmt->bind_param('i',$aid);
            $stmt->execute() ;//ok
            $res=$stmt->get_result();
            //$cnt=1;
            while($row=$res->fetch_object())
        {
        ?>     
            <div class="col-md-12">
              <div class="card" style="background: #D8E4F8; border-radius: 20px; box-shadow: 0 8px 32px rgba(0,0,0,0.15), 0 4px 8px rgba(0,0,0,0.1); border: 1px solid rgba(0,0,0,0.05);">
                <div class="card-header card-header-divider" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-bottom: 2px solid rgba(0,0,0,0.05); padding: 25px; border-radius: 20px 20px 0 0;">
                  <span style="color: #2C3E50; font-size: 1.4em; font-weight: 600; letter-spacing: 0.5px;" class="card-subtitle">Change Your Password</span>
                </div>
                <div class="card-body" style="padding: 40px;">
                  <form method="POST">
                    <div class="form-group row" style="margin-bottom: 25px;">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="old_pwd" style="color: #34495E; font-weight: 600; font-size: 1.1em;">Old Password</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <div class="input-group">
                          <input class="form-control" name="old_pwd" id="old_pwd" type="password" style="border-radius: 12px 0 0 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                          <div class="input-group-append">
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="old_pwd" style="border-radius: 0 12px 12px 0; border: 2px solid #E8EEF4; border-left: none;">
                              <i class="fa fa-eye-slash"></i>
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row" style="margin-bottom: 25px;">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="new_pwd" style="color: #34495E; font-weight: 600; font-size: 1.1em;">New Password</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <div class="input-group">
                          <input class="form-control" name="new_pwd" id="new_pwd" type="password" style="border-radius: 12px 0 0 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                          <div class="input-group-append">
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_pwd" style="border-radius: 0 12px 12px 0; border: 2px solid #E8EEF4; border-left: none;">
                              <i class="fa fa-eye-slash"></i>
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group row" style="margin-bottom: 25px;">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="confirm_pwd" style="color: #34495E; font-weight: 600; font-size: 1.1em;">Confirm New Password</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" name="confirm_pwd" id="confirm_pwd" type="password" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <p class="text-center" style="margin-top: 30px;">
                        <input class="btn btn-space btn-success" value="Change Password" name="Update_Password" type="submit" style="border-radius: 12px; padding: 12px 30px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2); transition: all 0.3s ease; margin-right: 20px;">
                        <button class="btn btn-space btn-secondary" style="border-radius: 12px; padding: 12px 30px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 4px 15px rgba(108, 117, 125, 0.2); transition: all 0.3s ease;">Cancel</button>
                      </p>
                    </div>
                  </form>
                </div>
              </div>
            </div>
        </div>
       
        <?php }?>
        
      </div>
      <!--footer-->
      <?php include('assets/inc/footer.php');?>
        <!--EndFooter-->

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

        // Handle password toggle
        $('.toggle-password').click(function() {
          const button = $(this);
          const icon = button.find('i');
          const input = $('#' + button.data('target'));
          
          if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
          } else {
            input.attr('type', 'password'); 
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
          }
        });
      });
    </script>
  </body>

</html>