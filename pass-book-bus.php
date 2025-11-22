<?php
session_start();
include('assets/inc/config.php');
include('assets/inc/checklogin.php');
check_login();
$aid = $_SESSION['pass_id'];

// Check if user has cancelled ticket
if(isset($_POST['cancel_ticket'])) {
  // Get passenger's currently booked seats and train ID
  $ret = "SELECT seats, pass_bus_number FROM obrs_passenger WHERE pass_id=?";
  $stmt = $mysqli->prepare($ret);
  $stmt->bind_param('i', $aid);
  $stmt->execute();
  $res = $stmt->get_result();
  $passenger = $res->fetch_object();

  if($passenger && $passenger->pass_bus_number) {
    // Get train details using train number
    $ret = "SELECT id, available_seats, booked_seats FROM obrs_bus WHERE number=?";
    $stmt = $mysqli->prepare($ret);
    $stmt->bind_param('s', $passenger->pass_bus_number);
    $stmt->execute();
    $res = $stmt->get_result();
    $train = $res->fetch_object();

    if($train) {
      // Get passenger's booked seats
      $passenger_seats = explode(",", $passenger->seats);
      $train_booked_seats = $train->booked_seats ? explode(",", $train->booked_seats) : array();

      // Remove passenger's seats from booked seats
      $new_booked_seats = array_diff($train_booked_seats, $passenger_seats);
      $new_booked_seats_str = implode(",", $new_booked_seats);

      // Update available seats count
      $new_available = $train->available_seats + count($passenger_seats);

      // Update train seats
      $update_train = "UPDATE obrs_bus SET available_seats=?, booked_seats=? WHERE id=?";
      $stmt = $mysqli->prepare($update_train);
      $stmt->bind_param('isi', $new_available, $new_booked_seats_str, $train->id);
      $stmt->execute();

      // Reset passenger's booking details
      $query="UPDATE obrs_passenger SET pass_bus_number=NULL, pass_bus_name=NULL, pass_dep_station=NULL, pass_dep_time=NULL, pass_arr_station=NULL, pass_bus_fare=NULL, seats=NULL WHERE pass_id=?";
      $stmt = $mysqli->prepare($query);
      $rc=$stmt->bind_param('i', $aid);
      $stmt->execute();

      if($stmt) {
        $success = "Ticket cancelled successfully";
      } else {
        $err = "Please try again later";
      }
    }
  }
}
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
        <h2 class="page-head-title" style="color: black;">BusZy Booking</h2>
        <nav aria-label="breadcrumb" role="navigation">
          <ol class="breadcrumb page-head-nav">
            <li class="breadcrumb-item"><a href="#" style="color: black;">BusZy Booking </a></li>
            <li class="breadcrumb-item active" style="color: black; text-decoration: underline;">Reserve Your Seats</li>
          </ol>
        </nav>
      </div>

      <div class="main-content container-fluid">
        <div class="row">
          <div class="col-sm-12">
            <div class="card card-table" style="background: linear-gradient(120deg, #E0EAFC 0%, #CFDEF3 100%); color: #2C3E50; box-shadow: 0 4px 20px rgba(0,0,0,0.1); border-radius: 15px;">
              <div class="card-header" style="border-bottom: 1px solid rgba(44,62,80,0.1); padding: 25px;">
                <h4 style="margin: 0; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;"><i class="fa fa-chair" style="margin-right: 10px;"></i>Reserve Your Seats</h4>
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
                      <th style="border: none; color: #34495E; font-weight: 600;">Bus Number</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Bus Name</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Route</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Departure</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Arrival</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Dep.Time</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Fare</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Total Seats</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Available Seats</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Action</th>
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
                    while($row = $res->fetch_object()) {
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
                        <td style="border: none;"><?php echo $row->number; ?></td>
                        <td style="border: none;"><?php echo $row->name; ?></td>
                        <td style="border: none;"><?php echo $row->route; ?></td>
                        <td style="border: none;"><?php echo $row->current; ?></td>
                        <td style="border: none;"><?php echo $row->destination; ?></td>
                        <td style="border: none;"><?php echo date('d M, Y \a\t h:i A', strtotime(htmlspecialchars($row->time))); ?></td>
                        <td style="border: none;">₹<?php echo $row->fare; ?></td>
                        <td style="border: none;"><?php echo $row->passengers; ?></td>
                        <td style="border: none; <?php echo $seatStyle; ?>"><?php echo $row->available_seats; ?><br><?php echo $seatMsg; ?></td>
                        <td style="border: none;">
                          <?php if($row->available_seats > 0) { ?>
                            <a href="pass-book-specific-bus.php?id=<?php echo $row->id; ?>" class="badge" style="background: linear-gradient(120deg, #50C878 0%, #228B22 100%); color: #FFFFFF; padding: 8px 15px; border-radius: 20px; font-weight: 500; font-size: 12px; text-decoration: none;">
                              <i class="fa fa-ticket" style="margin-right: 5px;"></i> Book
                            </a>
                          <?php } else { ?>
                            <span class="badge" style="background: #dc3545; color: #FFFFFF; padding: 8px 15px; border-radius: 20px; font-weight: 500; font-size: 12px;">
                              <i class="fa fa-ban" style="margin-right: 5px;"></i> Sold Out
                            </span>
                          <?php } ?>
                        </td>
                      </tr>
                    <?php } ?>
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