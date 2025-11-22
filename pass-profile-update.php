<?php
session_start();
include('assets/inc/config.php');
//date_default_timezone_set('Africa /Nairobi');
include('assets/inc/checklogin.php');
check_login();
$aid = $_SESSION['pass_id'];
if (isset($_POST['Update_Profile'])) {

  $pass_fname = $_POST['pass_fname'];
  $pass_lname = $_POST['pass_lname'];
  $pass_phone = $_POST['pass_phone'];
  $pass_addr = $_POST['pass_addr'];
  $pass_email = $_POST['pass_email'];
  $pass_uname = $_POST['pass_uname'];
  //$pass_ocupation=$_POST['pass_occupation'];
  //$passwordconf=md5($_POST['passwordconf']);
  //$date = date('d-m-Y h:i:s', time());
  $query = "update  obrs_passenger set pass_fname = ?, pass_lname = ?, pass_phone = ?, pass_addr = ?, pass_email = ?, pass_uname = ? where pass_id=?";
  $stmt = $mysqli->prepare($query);
  $rc = $stmt->bind_param('ssssssi', $pass_fname, $pass_lname, $pass_phone, $pass_addr, $pass_email, $pass_uname, $aid);
  $stmt->execute();
  if ($stmt) {
    $succ = "Your Profile Has Been Updated";
  } else {
    $err = "Please Try Again Later";
  }
  #echo"<script>alert('Your Profile Has Been Updated Successfully');</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<!--Head-->
<?php include('assets/inc/head.php'); ?>
<!--End Head-->

<body style="background-color: #F0F0D7;">
  <div class="be-wrapper be-fixed-sidebar">
    <!--Navigation Bar-->
    <?php include('assets/inc/navbar.php'); ?>
    <!--End Navigation Bar-->

    <!--Sidebar-->
    <?php include('assets/inc/sidebar.php'); ?>
    <!--End Sidebar-->
    <div class="be-content">
      <div class="page-head">
        <h2 class="page-head-title" style="color: black;">Profile Update</h2>
        <nav aria-label="breadcrumb" role="navigation">
          <ol class="breadcrumb page-head-nav">
            <li class="breadcrumb-item"><a href="pass-dashboard.php" style="color: black;">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#" style="color: black;">Profile</a></li>
            <li class="breadcrumb-item active" style="color: black; text-decoration: underline;">Update Profile</li>
          </ol>
        </nav>
      </div>
      <?php if (isset($succ)) { ?>
        <!--This code for injecting an alert-->
        <script>
          setTimeout(function() {
              swal("Success!", "<?php echo $succ; ?>!", "success");
            },
            100);
        </script>

      <?php } ?>
      <?php if (isset($err)) { ?>
        <!--This code for injecting an alert-->
        <script>
          setTimeout(function() {
              swal("Failed!", "<?php echo $err; ?>!", "Failed");
            },
            100);
        </script>

      <?php } ?>
      <div class="main-content container-fluid">
        <?php
        $aid = $_SESSION['pass_id'];
        $ret = "select * from obrs_passenger where pass_id=?";
        $stmt = $mysqli->prepare($ret);
        $stmt->bind_param('i', $aid);
        $stmt->execute(); //ok
        $res = $stmt->get_result();
        //$cnt=1;
        while ($row = $res->fetch_object()) {
        ?>
          <div class="row">
            <div class="col-md-12">
              <div class="card" style="background: #D8E4F8; border-radius: 20px; box-shadow: 0 8px 32px rgba(0,0,0,0.15), 0 4px 8px rgba(0,0,0,0.1); border: 1px solid rgba(0,0,0,0.05);">
                <div class="card-header card-header-divider" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-bottom: 2px solid rgba(0,0,0,0.05); padding: 25px; border-radius: 20px 20px 0 0;">
                  <span style="color: #2C3E50; font-size: 1.4em; font-weight: 600; letter-spacing: 0.5px;" class="card-subtitle">Update Your Profile Details</span>
                </div>
                <div class="card-body" style="padding: 40px;">
                  <form method="POST">
                    <div class="form-group row" style="margin-bottom: 25px;">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;">First Name</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" name="pass_fname" value="<?php echo $row->pass_fname; ?>" id="inputText3" type="text" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                      </div>
                    </div>
                    <div class="form-group row" style="margin-bottom: 25px;">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;">Last Name</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" name="pass_lname" value="<?php echo $row->pass_lname; ?>" id="inputText3" type="text" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                      </div>
                    </div>
                    <div class="form-group row" style="margin-bottom: 25px;">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;">Phone Number</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" name="pass_phone" value="<?php echo $row->pass_phone; ?>" id="inputText3" type="tel" pattern="[0-9]{10}" maxlength="10" oninput="this.value=this.value.replace(/[^0-9]/g,'')" placeholder="Enter 10 digit phone number" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                      </div>
                    </div>
                    <div class="form-group row" style="margin-bottom: 25px;">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;">Address</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" name="pass_addr" value="<?php echo $row->pass_addr; ?>" id="inputText3" type="text" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                      </div>
                    </div>
                    <div class="form-group row" style="margin-bottom: 25px;">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;">Email</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" name="pass_email" value="<?php echo $row->pass_email; ?>" id="inputText3" type="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                      </div>
                    </div>
                    <div class="form-group row" style="margin-bottom: 25px;">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;">Username</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" name="pass_uname" value="<?php echo $row->pass_uname; ?>" id="inputText3" type="text" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <p class="text-center" style="margin-top: 30px;">
                        <input class="btn btn-space btn-success" value="Update Profile" name="Update_Profile" type="submit" style="border-radius: 12px; padding: 12px 30px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2); transition: all 0.3s ease; margin-right: 20px;">
                        <a href="pass-dashboard.php" class="btn btn-space btn-secondary" style="border-radius: 12px; padding: 12px 30px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 4px 15px rgba(108, 117, 125, 0.2); transition: all 0.3s ease;">Cancel</a>
                      </p>
                    </div>
                  </form>
                </div>
              </div>
            </div>

          <?php } ?>

          </div>
          <!--footer-->
          <?php include('assets/inc/footer.php'); ?>
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
    <script type="text/javascript">
      $(document).ready(function() {
        //-initialize the javascript
        App.init();
        App.formElements();
      });
    </script>
</body>

</html>