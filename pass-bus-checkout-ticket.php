<?php
session_start();
include('assets/inc/config.php');
include('assets/inc/checklogin.php');
check_login();
$aid = $_SESSION['pass_id'];

// Check for expired unpaid bookings and restore seats
$check_expired = "SELECT * FROM obrs_booking_history WHERE pass_id=? AND status='Active' AND payment_status='Pending' 
                  AND booking_date < DATE_SUB(NOW(), INTERVAL 5 MINUTE)";
$stmt = $mysqli->prepare($check_expired);
$stmt->bind_param('i', $aid);
$stmt->execute();
$expired_result = $stmt->get_result();

if ($expired_result->num_rows > 0) {
  while ($expired = $expired_result->fetch_object()) {
    // Get Bus details
    $train_query = "SELECT id, available_seats, booked_seats FROM obrs_bus WHERE number=?";
    $stmt = $mysqli->prepare($train_query);
    $stmt->bind_param('s', $expired->bus_number);
    $stmt->execute();
    $train = $stmt->get_result()->fetch_object();

    // Restore seats
    $expired_seats = explode(",", $expired->selected_seats);
    $current_booked = $train->booked_seats ? explode(",", $train->booked_seats) : array();
    $new_booked = array_diff($current_booked, $expired_seats);
    $new_booked_str = implode(",", $new_booked);
    $new_available = $train->available_seats + count($expired_seats);

    // Update Bus seats
    $update = "UPDATE obrs_bus SET available_seats=?, booked_seats=? WHERE id=?";
    $stmt = $mysqli->prepare($update);
    $stmt->bind_param('isi', $new_available, $new_booked_str, $train->id);
    $stmt->execute();

    // Update booking status
    $update_booking = "UPDATE obrs_booking_history SET status='Expired' WHERE id=?";
    $stmt = $mysqli->prepare($update_booking);
    $stmt->bind_param('i', $expired->id);
    $stmt->execute();

    $error = "Your booking has expired due to non-payment within 5 minutes. The seats have been released.";
  }
}

// Handle checkout form submission
if (isset($_POST['checkout'])) {
  $booking_id = $_POST['booking_id'];

  // Update booking with payment details
  $query = "UPDATE obrs_booking_history SET payment_status='Pending' WHERE id=? AND pass_id=?";
  $stmt = $mysqli->prepare($query);
  $stmt->bind_param('ii', $booking_id, $aid);
  $stmt->execute();

  if ($stmt) {
    $_SESSION['success'] = "Payment initiated successfully.";
    header("Location: pass-confirm-ticket.php");
    exit();
  } else {
    $error = "Something went wrong";
  }
}

// Check for success message
if (isset($_SESSION['success'])) {
  $success = $_SESSION['success'];
  unset($_SESSION['success']);
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
        <h2 class="page-head-title" style="color: black;">Review & Pay</h2>
        <nav aria-label="breadcrumb" role="navigation">
          <ol class="breadcrumb page-head-nav">
            <li class="breadcrumb-item"><a href="#" style="color: black;">BusZy Tickets </a></li>
            <li class="breadcrumb-item active" style="color: black; text-decoration: underline;">Review & Pay</li>
          </ol>
        </nav>
      </div>

      <div class="main-content container-fluid">
        <?php if (isset($error)) { ?>
          <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>

        <?php if (isset($success)) { ?>
          <div class="alert alert-success"><?php echo $success; ?></div>
        <?php } ?>

        <div class="row">
          <div class="col-sm-12">
            <div class="card card-table" style="background: linear-gradient(120deg, #E0EAFC 0%, #CFDEF3 100%); color: #2C3E50; box-shadow: 0 4px 20px rgba(0,0,0,0.1); border-radius: 15px;">
              <?php
              // Get passenger details        
              $ret = "SELECT * FROM obrs_passenger WHERE pass_id=?";
              $stmt = $mysqli->prepare($ret);
              $stmt->bind_param('i', $aid);
              $stmt->execute();
              $res = $stmt->get_result();
              while ($row = $res->fetch_object()) {
              ?>
                <div class="card-header" style="border-bottom: 1px solid rgba(44,62,80,0.1); padding: 25px;">
                  <h4 style="margin: 0; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">
                    <i class="fa fa-ticket" style="margin-right: 10px;"></i>
                    <?php echo $row->pass_fname; ?> <?php echo $row->pass_lname; ?>, please review and checkout your tickets
                  </h4>
                </div>
              <?php } ?>

              <div class="card-body">
                <!-- Search Form -->
                <div class="row mb-4">
                  <div class="col-md-8 ml-auto">
                    <form method="GET" class="form-inline justify-content-end">
                      <div class="input-group" style="margin-right: 1vw;">
                        <input type="text" name="search" class="form-control" placeholder="Search by Ticket ID, Bus Number, Name, Stations..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" style="border-radius: 4px 0 0 4px;">
                        <div class="input-group-append">
                          <button type="submit" class="btn" style="background: linear-gradient(120deg, #3498db 0%, #2980b9 100%); color: white; border: none;">
                            <i class="fa fa-search"></i> Search
                          </button>
                          <?php if (isset($_GET['search'])) { ?>
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
                      <th style="border: none; color: #34495E; font-weight: 600;">Ticket ID</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Bus Number</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Bus Name</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Departure</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Arrival</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Dep.Time</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Selected Seats</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Seat Types</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Total Fare</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    // Get all bookings for this passenger
                    $search = isset($_GET['search']) ? $_GET['search'] : '';

                    if ($search) {
                      $search = "%$search%";
                      $ret = "SELECT *, TIMESTAMPDIFF(MINUTE, booking_date, NOW()) as minutes_elapsed 
                            FROM obrs_booking_history 
                            WHERE pass_id=?
                            AND (booking_id LIKE ? 
                                OR bus_number LIKE ? 
                                OR bus_name LIKE ?
                                OR dep_station LIKE ?
                                OR arr_station LIKE ?
                                OR selected_seats LIKE ?
                                OR seat_type LIKE ?)
                            ORDER BY booking_date DESC";
                      $stmt = $mysqli->prepare($ret);
                      $stmt->bind_param('isssssss', $aid, $search, $search, $search, $search, $search, $search, $search);
                    } else {
                      $ret = "SELECT *, TIMESTAMPDIFF(MINUTE, booking_date, NOW()) as minutes_elapsed 
                            FROM obrs_booking_history 
                            WHERE pass_id=?
                            ORDER BY booking_date DESC";
                      $stmt = $mysqli->prepare($ret);
                      $stmt->bind_param('i', $aid);
                    }

                    $stmt->execute();
                    $res = $stmt->get_result();

                    if ($res->num_rows > 0) {
                      $bookings = array();
                      while ($row = $res->fetch_object()) {
                        $bookings[] = $row;
                      }

                      // Sort bookings by date descending (newest first)
                      usort($bookings, function ($a, $b) {
                        return strtotime($b->booking_date) - strtotime($a->booking_date);
                      });

                      foreach ($bookings as $row) {
                    ?>
                        <tr style="border-bottom: 1px solid rgba(44,62,80,0.1); transition: all 0.3s;">
                          <td style="border: none;"><?php echo $row->booking_id; ?></td>
                          <td style="border: none;"><?php echo $row->bus_number; ?></td>
                          <td style="border: none;"><?php echo $row->bus_name; ?></td>
                          <td style="border: none;"><?php echo $row->dep_station; ?></td>
                          <td style="border: none;"><?php echo $row->arr_station; ?></td>
                          <td style="border: none;"><?php echo $row->dep_time; ?></td>
                          <td style="border: none;"><?php echo $row->selected_seats; ?></td>
                          <td style="border: none;"><?php echo $row->seat_type; ?></td>
                          <td style="border: none;">₹<?php echo $row->total_cost; ?></td>
                          <td style="padding: 15px;">
                            <?php if ($row->payment_status == 'Paid') { ?>
                              <span class="badge" style="background: #2ecc71; color: white; padding: 8px 12px; border-radius: 4px;">
                                Paid
                              </span>
                            <?php } else if ($row->minutes_elapsed < 5) { ?>
                              <a href="pass-confirm-ticket.php?id=<?php echo $row->id; ?>" class="btn btn-sm" style="background: linear-gradient(120deg, #2ecc71 0%, #27ae60 100%); color: white; border: none; padding: 8px 15px; border-radius: 4px; text-decoration: none; transition: all 0.3s;">
                                Pay Now
                              </a>
                            <?php } else { ?>
                              <span class="badge" style="background: #e74c3c; color: white; padding: 8px 12px; border-radius: 4px;">
                                Expired
                              </span>
                            <?php } ?>
                          </td>
                        </tr>
                      <?php
                      }
                    } else {
                      ?>
                      <tr>
                        <td colspan="11" class="text-center">No bookings found</td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!--Footer-->
        <?php include('assets/inc/footer.php'); ?>
        <!--EndFooter-->
      </div>
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