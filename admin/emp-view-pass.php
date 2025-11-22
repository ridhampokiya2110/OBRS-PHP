<!--Server side code to give us and hold session-->
<?php
  session_start();
  include('assets/inc/config.php');
  include('assets/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['admin_id'];
?>
<!--End Server side scriptiing-->
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
        <h2 class="page-head-title" style="color: black;">View Passenger</h2>
        <nav aria-label="breadcrumb" role="navigation">
          <ol class="breadcrumb page-head-nav">
            <li class="breadcrumb-item"><a href="emp-dashboard.php" style="color: black;">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#" style="color: black;">Passengers</a></li>
            <li class="breadcrumb-item active" style="color: black; text-decoration: underline;">View</li>
          </ol>
        </nav>
      </div>

      <div class="main-content container-fluid">
        <?php
          $aid=$_GET['pass_id'];
          $ret="select * from obrs_passenger where pass_id=?";
          $stmt= $mysqli->prepare($ret);
          $stmt->bind_param('i',$aid);
          $stmt->execute();
          $res=$stmt->get_result();
          while($row=$res->fetch_object())
          {
        ?>
        <div class="row justify-content-center">
          <div class="col-md-8">
            <div class="card" style="background: linear-gradient(135deg, #E0EAFC 0%, #CFDEF3 100%); border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
              <div class="card-header text-center" style="background: rgba(255,255,255,0.1); border-bottom: 2px solid rgba(44,62,80,0.1); padding: 30px;">
                <div class="avatar" style="width: 100px; height: 100px; margin: 0 auto 20px; background: #3498db; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                  <i class="fa fa-user" style="font-size: 40px; color: white;"></i>
                </div>
                <h3 style="margin: 0; color: #2C3E50; font-weight: 700;"><?php echo $row->pass_fname;?> <?php echo $row->pass_lname;?></h3>
              </div>
              
              <div class="card-body" style="padding: 40px;">
                <div class="info-group" style="margin-bottom: 25px; padding: 20px; background: rgba(255,255,255,0.4); border-radius: 15px;">
                  <label style="display: block; color: #34495E; font-weight: 600; margin-bottom: 10px;">Contact Information</label>
                  <div class="d-flex align-items-center" style="margin-bottom: 15px;">
                    <i class="fa fa-phone" style="font-size: 20px; color: #3498db; margin-right: 15px;"></i>
                    <span style="color: #2C3E50; font-size: 16px;"><?php echo $row->pass_phone;?></span>
                  </div>
                  <div class="d-flex align-items-center">
                    <i class="fa fa-envelope" style="font-size: 20px; color: #3498db; margin-right: 15px;"></i>
                    <span style="color: #2C3E50; font-size: 16px;"><?php echo $row->pass_email;?></span>
                  </div>
                </div>

                <div class="info-group" style="padding: 20px; background: rgba(255,255,255,0.4); border-radius: 15px;">
                  <label style="display: block; color: #34495E; font-weight: 600; margin-bottom: 10px;">Address</label>
                  <div class="d-flex align-items-center">
                    <i class="fa fa-map-marker" style="font-size: 20px; color: #3498db; margin-right: 15px;"></i>
                    <span style="color: #2C3E50; font-size: 16px;"><?php echo $row->pass_addr;?></span>
                  </div>
                </div>
              </div>

              <div class="card-footer text-center" style="background: rgba(255,255,255,0.1); padding: 20px; border-top: 2px solid rgba(44,62,80,0.1);">
                <a href="emp-manage-passengers.php" class="btn btn-primary" style="padding: 12px 30px; border-radius: 30px; background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); border: none; box-shadow: 0 4px 15px rgba(52,152,219,0.3);">
                  <i class="fa fa-arrow-left mr-2"></i> Back to Passengers List
                </a>
              </div>
            </div>
          </div>
        </div>
        <?php } ?>
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
  </script>
</body>

</html>