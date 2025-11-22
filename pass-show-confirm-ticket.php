<?php
session_start();
include('assets/inc/config.php');
include('assets/inc/checklogin.php');
check_login();
$aid=$_SESSION['pass_id'];

// Get confirmed bookings for this passenger
$query = "SELECT b.*, p.pass_fname, p.pass_lname, p.pass_email, p.pass_addr,
          t.name as bus_name, t.number as train_no,
          b.dep_station, b.arr_station, b.dep_time, b.selected_seats
          FROM obrs_booking_history b
          JOIN obrs_passenger p ON b.pass_id = p.pass_id
          JOIN obrs_bus t ON b.bus_number = t.number 
          WHERE b.pass_id=? AND b.payment_status='Paid' AND b.status='Active'
          ORDER BY b.id DESC";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $aid);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<!--Head-->
<?php include('assets/inc/head.php');?>
<!--End Head-->
<body style="background-color: #F0F0D7;">
  <div class="be-wrapper be-fixed-sidebar">
    <!--Navigation bar-->
    <?php include("assets/inc/navbar.php");?>
    <!--Navigation-->

    <!--Sidebar-->
    <?php include("assets/inc/sidebar.php");?>
    <!--Sidebar-->
    <div class="be-content">
      <div class="page-head">
        <h2 class="page-head-title" style="color: black;">Grab Your Golden Ticket!</h2>
        <nav aria-label="breadcrumb" role="navigation">
          <ol class="breadcrumb page-head-nav">
            <li class="breadcrumb-item"><a href="#" style="color: black;">BusZy Tickets</a></li>
            <li class="breadcrumb-item active" style="color: black; text-decoration: underline;">Grab Your Golden Ticket!</li>
          </ol>
        </nav>
      </div>

      <div class="main-content container-fluid">
        <div class="row">
          <div class="col-sm-12">
            <div class="card card-table" style="background: linear-gradient(120deg, #E0EAFC 0%, #CFDEF3 100%); color: #2C3E50; box-shadow: 0 4px 20px rgba(0,0,0,0.1); border-radius: 15px;">
              <div class="card-header" style="border-bottom: 1px solid rgba(44,62,80,0.1); padding: 25px;">
                <h4 style="margin: 0; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">
                  <i class="fa fa-ticket" style="margin-right: 10px;"></i>Ticket-y McTicketface: Your Magical Travel Pass Awaits!
                </h4>
                <?php if(isset($_SESSION['success_msg'])): ?>
                  <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-top: 15px; border-radius: 8px;">
                    <?php 
                      echo $_SESSION['success_msg'];
                      unset($_SESSION['success_msg']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                <?php endif; ?>
              </div>

              <div class="card-body">
                <!-- Search Form -->
                <div class="row mb-4">
                  <div class="col-md-8 ml-auto">
                    <form method="GET" class="form-inline justify-content-end">
                      <div class="input-group" style="margin-right: 1vw;">
                        <input type="text" name="search" class="form-control" placeholder="Search by Bus Number, Name, Stations..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" style="border-radius: 4px 0 0 4px;">
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
                      <th style="border: none; color: #34495E; font-weight: 600;">Booking ID</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Bus Details</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Journey Details</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Passenger Details</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Booking Details</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    while($booking = $result->fetch_object()) {
                      ?>
                      <tr style="border-bottom: 1px solid rgba(44,62,80,0.1); transition: all 0.3s;">
                        <td style="border: none;">
                          <span class="badge bg-primary" style="font-size: 0.9em; padding: 8px 12px; color: white; border-radius: 8px;">
                            <?php echo htmlspecialchars($booking->booking_id);?>
                          </span>
                        </td>
                        <td style="border: none;">
                          <strong><?php echo htmlspecialchars($booking->bus_name);?></strong><br>
                          <small>Bus No: <?php echo htmlspecialchars($booking->train_no);?></small>
                        </td>
                        <td style="border: none;">
                          <strong>From:</strong> <?php echo htmlspecialchars($booking->dep_station);?><br>
                          <strong>To:</strong> <?php echo htmlspecialchars($booking->arr_station);?><br>
                          <strong>Departure:</strong> <?php echo htmlspecialchars($booking->dep_time);?>
                        </td>
                        <td style="border: none;">
                          <?php echo htmlspecialchars($booking->pass_fname . ' ' . $booking->pass_lname);?><br>
                          <small><?php echo htmlspecialchars($booking->pass_email);?></small><br>
                          <small><?php echo htmlspecialchars($booking->pass_addr);?></small>
                        </td>
                        <td style="border: none;">
                          <strong>Seats:</strong> <?php echo htmlspecialchars($booking->seats);?><br>
                          <strong>Seat Numbers:</strong> <?php echo htmlspecialchars($booking->selected_seats);?><br>
                          <strong>Total Fare:</strong> ₹<?php echo htmlspecialchars($booking->bus_fare * $booking->seats);?>
                        </td>
                        <td style="border: none;">
                          <a href="pass-print-ticket.php?id=<?php echo $booking->id;?>" 
                             target="_blank"
                             class="badge" style="background: linear-gradient(120deg, #50C878 0%, #228B22 100%); color: #FFFFFF; padding: 8px 15px; border-radius: 20px; font-weight: 500; font-size: 12px; text-decoration: none;">
                            <i class="fa fa-print" style="margin-right: 5px;"></i> Print Ticket
                          </a>
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
      <?php include('assets/inc/footer.php');?>
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
  <script type="text/javascript">
    $(document).ready(function(){
      App.init();
      
      // Initialize DataTable for better table features
      $('#table1').DataTable({
        "order": [[ 0, "desc" ]],
        "pageLength": 10,
        "language": {
          "lengthMenu": "Show _MENU_ tickets per page",
          "zeroRecords": "No tickets found", 
          "info": "Showing page _PAGE_ of _PAGES_",
          "infoEmpty": "No tickets available",
          "infoFiltered": "(filtered from _MAX_ total tickets)"
        }
      });
    });
  </script>
</body>
</html>
