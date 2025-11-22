<?php
  session_start();
  include('assets/inc/config.php');
  include('assets/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['admin_id'];
?>
<!DOCTYPE html>
<html lang="en">
  <!--Head-->
    <?php include('assets/inc/head.php');?>
  <!--End Head-->
  <body style="background-color: #F0F0D7;">
    <div class="be-wrapper be-fixed-sidebar">
      <!--Nav Bar-->
      <?php include('assets/inc/navbar.php');?>
      <!--End Navbar-->
      <!--Sidebar-->
      <?php include('assets/inc/sidebar.php');?>
      <!--End Sidebar-->
      <div class="be-content">
        <div class="page-head">
          <h2 class="page-head-title" style="color: black;">Bus Details</h2>
          <nav aria-label="breadcrumb" role="navigation">
            <ol class="breadcrumb page-head-nav">
              <li class="breadcrumb-item"><a href="emp-dashboard.php" style="color: black;">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="emp-manage-bus.php" style="color: black;">Buses</a></li>
              <li class="breadcrumb-item active" style="color: black; text-decoration: underline;">View Details</li>
            </ol>
          </nav>
        </div>

        <?php
            $aid=$_GET['id'];
            $ret="select * from obrs_bus where id=?";
            $stmt= $mysqli->prepare($ret);
            $stmt->bind_param('i',$aid);
            $stmt->execute();
            $res=$stmt->get_result();
            while($row=$res->fetch_object())
            {
        ?>
        <div class="main-content container-fluid">
          <div class="row">
            <div class="col-lg-12">
              <div id='printReceipt' class="card" style="background: linear-gradient(120deg, #E0EAFC 0%, #CFDEF3 100%); color: #2C3E50; box-shadow: 0 4px 20px rgba(0,0,0,0.1); border-radius: 15px;">
                <div class="card-header" style="border-bottom: 1px solid rgba(44,62,80,0.1); padding: 25px;">
                  <div class="d-flex justify-content-between align-items-center">
                    <h4 style="margin: 0; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">
                      <i class="fa fa-bus" style="margin-right: 10px;"></i>Bus Information
                    </h4>
                    <a href="emp-manage-bus.php" class="btn" style="background: linear-gradient(120deg, #3498db 0%, #2980b9 100%); color: white; padding: 8px 15px; border-radius: 20px; font-weight: 500; font-size: 14px; text-decoration: none;">
                      <i class="fa fa-arrow-left" style="margin-right: 5px;"></i> Back
                    </a>
                  </div>
                </div>
                
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table" style="background: rgba(255,255,255,0.8); border-radius: 10px;">
                      <thead>
                        <tr style="border-bottom: 2px solid rgba(44,62,80,0.1);">
                          <th style="padding: 15px; color: #34495E; font-weight: 600;">Bus Number</th>
                          <th style="padding: 15px; color: #34495E; font-weight: 600;">Bus Name</th>
                          <th style="padding: 15px; color: #34495E; font-weight: 600;">Route</th>
                          <th style="padding: 15px; color: #34495E; font-weight: 600;">Departure</th>
                          <th style="padding: 15px; color: #34495E; font-weight: 600;">Arrival</th>
                          <th style="padding: 15px; color: #34495E; font-weight: 600;">Departure Time</th>
                          <th style="padding: 15px; color: #34495E; font-weight: 600;">Total Seats</th>
                          <th style="padding: 15px; color: #34495E; font-weight: 600;">Available Seats</th>
                          <th style="padding: 15px; color: #34495E; font-weight: 600;">Fare</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr style="border-bottom: 1px solid rgba(44,62,80,0.1);">
                          <td style="padding: 15px;"><?php echo htmlspecialchars($row->number);?></td>
                          <td style="padding: 15px;"><?php echo htmlspecialchars($row->name);?></td>
                          <td style="padding: 15px;"><?php echo htmlspecialchars($row->route);?></td>
                          <td style="padding: 15px;"><?php echo htmlspecialchars($row->current);?></td>
                          <td style="padding: 15px;"><?php echo htmlspecialchars($row->destination);?></td>
                          <td style="padding: 15px;"><?php echo date('d M, Y \a\t h:i A', strtotime(htmlspecialchars($row->time))); ?></td>
                          <td style="padding: 15px;"><?php echo htmlspecialchars($row->passengers);?></td>
                          <td style="padding: 15px;"><?php echo htmlspecialchars($row->available_seats);?></td>
                          <td style="padding: 15px;">$<?php echo number_format($row->fare, 2);?></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php }?>
        
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