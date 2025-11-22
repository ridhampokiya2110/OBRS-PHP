<?php
session_start();
include('assets/inc/config.php');
include('assets/inc/checklogin.php');
check_login();
$aid = $_SESSION['pass_id'];
?>
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
        <h2 class="page-head-title" style="color: black;">BusZy Express</h2>
        <nav aria-label="breadcrumb" role="navigation">
          <ol class="breadcrumb page-head-nav">
            <li class="breadcrumb-item"><a href="#" style="color: black;">BusZy Express </a></li>
            <li class="breadcrumb-item active" style="color: black; text-decoration: underline;">All Available Buses</li>
          </ol>
        </nav>
        <hr style="border-top: 2px solid rgba(0,0,0,0.1); margin: 15px 0;">
      </div>

      <div class="main-content container-fluid">
        <div class="row">
          <div class="col-sm-12">
            <div class="card card-table" style="background: linear-gradient(120deg, #E0EAFC 0%, #CFDEF3 100%); color: #2C3E50; box-shadow: 0 4px 20px rgba(0,0,0,0.1); border-radius: 15px;">
              <div class="card-header" style="border-bottom: 1px solid rgba(44,62,80,0.1); padding: 25px;">
                <h4 style="margin: 0; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;"><i class="fa fa-bus" style="margin-right: 10px;"></i>All Available Buses</h4>
              </div>
              <div class="card-body">
                <!-- Search Form -->
                <div class="row mb-4">
                  <div class="col-md-8 ml-auto">
                    <form method="GET" class="form-inline justify-content-end">
                      <div class="input-group" style="margin-right: 1vw;">
                        <input type="text" name="search" class="form-control" placeholder="Search by Bus Number, Name, Route, Stations..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" style="border-radius: 4px 0 0 4px;">
                        <div class="input-group-append">
                          <button type="submit" class="btn" style="background: linear-gradient(120deg, #3498db 0%, #2980b9 100%); color: white; border: none;">
                            <i class="fa fa-search"></i> Search
                          </button>
                          <?php if(isset($_GET['search'])) { ?>
                            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn" style="background: linear-gradient(120deg, #e74c3c 0%, #c0392b 100%); color: white; border: none; margin-left: 5px;">
                              <i class="fa fa-times"></i> Reset
                            </a>
                          <?php } ?>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>

                <table class="table table-hover table-fw-widget table-striped table-bordered table-hover table-fw-widget" style="margin: 0;">
                  <thead>
                    <tr style="border-bottom: 2px solid rgba(44,62,80,0.1);">
                      <th style="border: none; color: #34495E; font-weight: 600;">No.</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Bus Number</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Bus Name</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Route</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Departure</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Arrival</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Dep.Time</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Fare</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Total Seats</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Available Seats</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $search = isset($_GET['search']) ? $_GET['search'] : '';
                    
                    if($search) {
                      $search = "%$search%";
                      $ret = "SELECT * FROM obrs_bus WHERE 
                             number LIKE ? OR
                             name LIKE ? OR 
                             route LIKE ? OR
                             current LIKE ? OR
                             destination LIKE ?";
                      $stmt = $mysqli->prepare($ret);
                      $stmt->bind_param('sssss', $search, $search, $search, $search, $search);
                    } else {
                      $ret = "SELECT * FROM obrs_bus";
                      $stmt = $mysqli->prepare($ret);
                    }
                    
                    $stmt->execute();
                    $res = $stmt->get_result();
                    $cnt = 1;
                    while ($row = $res->fetch_object()) {
                      $seatStyle = '';
                      $seatMsg = '';
                      if($row->available_seats < 7) {
                        $seatStyle = 'color: #F26B0F; font-weight: bold;';
                        $seatMsg = ' (Hurry! Only ' . $row->available_seats . ' seats left!)';
                      } else if($row->available_seats < 14) {
                        $seatStyle = 'color: #ba9e00; font-weight: bold;';
                        $seatMsg = ' (Going fast - Book now!)';
                      } else if($row->available_seats < 20) {
                        $seatStyle = 'color: #2E8B57; font-weight: bold;';
                        $seatMsg = ' (Good availability)';
                      } else {
                        $seatStyle = '';
                        $seatMsg = ' (Plenty of seats)';
                      }
                    ?>
                      <tr style="border-bottom: 1px solid rgba(44,62,80,0.1); transition: all 0.3s;">
                        <td style="border: none;"><?php echo $cnt; ?></td>
                        <td style="border: none;"><?php echo $row->number; ?></td>
                        <td style="border: none;"><?php echo $row->name; ?></td>
                        <td style="border: none;"><?php echo $row->route; ?></td>
                        <td style="border: none;"><?php echo $row->current; ?></td>
                        <td style="border: none;"><?php echo $row->destination; ?></td>
                        <td style="border: none;"><?php echo date('d M, Y \a\t h:i A', strtotime(htmlspecialchars($row->time))); ?></td>
                        <td style="border: none;">₹<?php echo $row->fare; ?></td>
                        <td style="border: none;"><?php echo $row->passengers; ?></td>
                        <td style="border: none; <?php echo $seatStyle; ?>"><?php echo $row->available_seats; ?><br><?php echo $seatMsg; ?></td>
                      </tr>
                    <?php $cnt = $cnt + 1;
                    } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--Footer-->
      <?php include('assets/inc/footer.php'); ?>
      <!--EndFooter-->
    </div>
  </div>

  <script src="assets/lib/jquery/jquery.min.js" type="text/javascript"></script>
  <script src="assets/lib/perfect-scrollbar/js/perfect-scrollbar.min.js" type="text/javascript"></script>
  <script src="assets/lib/bootstrap/dist/js/bootstrap.bundle.min.js" type="text/javascript"></script>
  <script src="assets/js/app.js" type="text/javascript"></script>
  <script src="https://kit.fontawesome.com/f766ed9c4c.js" crossorigin="anonymous"></script>
  <script src="assets/lib/datatables/datatables.net/js/jquery.dataTables.js" type="text/javascript"></script>
  <script src="assets/lib/datatables/datatables.net-bs4/js/dataTables.bootstrap4.js" type="text/javascript"></script>
  <script src="assets/lib/datatables/datatables.net-buttons/js/dataTables.buttons.min.js" type="text/javascript"></script>
  <script src="assets/lib/datatables/datatables.net-buttons/js/buttons.flash.min.js" type="text/javascript"></script>
  <script src="assets/lib/datatables/jszip/jszip.min.js" type="text/javascript"></script>
  <script src="assets/lib/datatables/pdfmake/pdfmake.min.js" type="text/javascript"></script>
  <script src="assets/lib/datatables/pdfmake/vfs_fonts.js" type="text/javascript"></script>
  <script src="assets/lib/datatables/datatables.net-buttons/js/buttons.colVis.min.js" type="text/javascript"></script>
  <script src="assets/lib/datatables/datatables.net-buttons/js/buttons.print.min.js" type="text/javascript"></script>
  <script src="assets/lib/datatables/datatables.net-buttons/js/buttons.html5.min.js" type="text/javascript"></script>
  <script src="assets/lib/datatables/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js" type="text/javascript"></script>
  <script src="assets/lib/datatables/datatables.net-responsive/js/dataTables.responsive.min.js" type="text/javascript"></script>
  <script src="assets/lib/datatables/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js" type="text/javascript"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      App.init();
      App.dataTables();
    });
  </script>
</body>

</html>