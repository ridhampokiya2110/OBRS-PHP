<?php
  session_start();
  include('assets/inc/config.php');
  include('assets/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['admin_id'];

  // Query to get Total Earning and Total Tickets for active paid tickets only
  $query = "SELECT SUM(total_cost) AS total_earning, COUNT(*) AS total_tickets 
            FROM obrs_booking_history 
            WHERE payment_status = 'Paid' AND status NOT IN ('Expired','Cancelled')";
  $stmt = $mysqli->prepare($query);
  $stmt->execute();
  $stmt->bind_result($total_earning, $total_tickets);
  $stmt->fetch();
  $stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<!--Head-->
<?php include("assets/inc/head.php"); ?>
<!--End Head-->

<body style="background-color: #F0F0D7;">
  <div class="be-wrapper be-fixed-sidebar">
    <!--Navigation-->
    <?php include("assets/inc/navbar.php"); ?>
    <?php include('assets/inc/sidebar.php'); ?>
    <!--End Navigation-->

    <div class="be-content">
      <div class="page-head">
        <h2 class="page-head-title" style="color: black;">BusZy Booking</h2>
        <nav aria-label="breadcrumb" role="navigation">
          <ol class="breadcrumb page-head-nav">
            <li class="breadcrumb-item"><a href="emp-dashboard.php" style="color: black;">Dashboard</a></li>
            <li class="breadcrumb-item active" style="color: black; text-decoration: underline;">View Active Paid Tickets</li>
          </ol>
        </nav>
      </div>

      <div class="main-content container-fluid">
        <div class="row">
          <div class="col-sm-12">
            <div class="card card-table" style="background: linear-gradient(120deg, #E0EAFC 0%, #CFDEF3 100%); color: #2C3E50; box-shadow: 0 4px 20px rgba(0,0,0,0.1); border-radius: 15px;">
              <div class="card-header" style="border-bottom: 1px solid rgba(44,62,80,0.1); padding: 25px;">
                <h4 style="margin: 0; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">
                  <i class="fa fa-ticket" style="margin-right: 10px;"></i>Active Paid Tickets
                </h4>
              </div>

              <div class="card-body">
                <div class="row mb-4">
                  <div class="col-sm-6">
                    <div class="text-center">
                      <h5 style="font-weight: 600; color: #2C3E50;">Total Earning</h5>
                      <span style="font-size: 24px; font-weight: 700; color: #27ae60;">₹<?php echo number_format($total_earning, 2); ?></span>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="text-center">
                      <h5 style="font-weight: 600; color: #2C3E50;">Total Active Tickets</h5>
                      <span style="font-size: 24px; font-weight: 700; color: #3498db;"><?php echo $total_tickets; ?></span>
                    </div>
                  </div>
                </div>

                <table class="table table-hover table-striped table-bordered" id="ticketTable" style="width:100%; margin: 0;">
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
                      $ret="SELECT b.*, p.pass_fname, p.pass_lname, p.pass_email, p.pass_addr,
                            t.name as bus_name, t.number as train_no 
                            FROM obrs_booking_history b
                            JOIN obrs_passenger p ON b.pass_id = p.pass_id
                            JOIN obrs_bus t ON b.bus_number = t.number
                            WHERE b.payment_status = 'Paid' AND b.status NOT IN ('Expired','Cancelled')";
                      $stmt= $mysqli->prepare($ret);
                      $stmt->execute();
                      $res=$stmt->get_result();
                      while($row=$res->fetch_object())
                      {
                    ?>
                      <tr style="border-bottom: 1px solid rgba(44,62,80,0.1); transition: all 0.3s;">
                        <td style="border: none;">
                          <span class="badge bg-primary" style="font-size: 0.9em; padding: 8px 12px; color: white; border-radius: 8px;">
                            <?php echo htmlspecialchars($row->booking_id);?>
                          </span>
                        </td>
                        <td style="border: none;">
                          <strong><?php echo htmlspecialchars($row->bus_name);?></strong><br>
                          <small>Bus No: <?php echo htmlspecialchars($row->train_no);?></small>
                        </td>
                        <td style="border: none;">
                          <strong>From:</strong> <?php echo htmlspecialchars($row->dep_station);?><br>
                          <strong>To:</strong> <?php echo htmlspecialchars($row->arr_station);?><br>
                          <strong>Departure:</strong> <?php echo htmlspecialchars($row->dep_time)?>
                        </td>
                        <td style="border: none;">
                          <?php echo htmlspecialchars($row->pass_fname . ' ' . $row->pass_lname);?><br>
                          <small><?php echo htmlspecialchars($row->pass_email);?></small><br>
                          <small><?php echo htmlspecialchars($row->pass_addr);?></small>
                        </td>
                        <td style="border: none;">
                          <strong>Seats:</strong> <?php echo htmlspecialchars($row->seats);?><br>
                          <strong>Seat Numbers:</strong> <?php echo htmlspecialchars($row->selected_seats);?><br>
                          <strong>Total Fare:</strong> ₹<?php echo htmlspecialchars($row->bus_fare * $row->seats)?>
                        </td>
                        <td style="border: none;">
                          <a href="emp-print-ticket.php?id=<?php echo $row->id;?>" 
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
      <!--End Footer-->
    </div>
  </div>

  <!-- Core Scripts -->
  <script src="assets/lib/jquery/jquery.min.js" type="text/javascript"></script>
  <script src="assets/lib/perfect-scrollbar/js/perfect-scrollbar.min.js" type="text/javascript"></script>
  <script src="assets/lib/bootstrap/dist/js/bootstrap.bundle.min.js" type="text/javascript"></script>
  <script src="assets/js/app.js" type="text/javascript"></script>
  <script src="https://kit.fontawesome.com/f766ed9c4c.js" crossorigin="anonymous"></script>

  <!-- DataTables Scripts -->
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
    $(document).ready(function(){
      App.init();
    });
  </script>
</body>
</html>