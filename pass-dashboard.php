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
      <div class="main-content container-fluid">
        <div class="row">
          <div class="col-12 col-lg-6 col-xl-4">
            <div class="widget widget-tile" style="background-color: #32CD32; color: #ffffff;">
              <div class="chart sparkline">
                <i class="fa fa-bus fa-2x"></i>
              </div>
              <div class="data-info">
                <?php
                $result = $mysqli->query("SELECT COUNT(*) as count FROM obrs_booking_history WHERE pass_id = '$aid' AND payment_status = 'Paid' AND status = 'Active'");
                $row = $result->fetch_assoc();
                ?>
                <div class="desc">Booked Tickets</div>
                <div class="value"><span><?php echo $row['count']; ?></span></div>
              </div>
            </div>
          </div>

          <div class="col-12 col-lg-6 col-xl-4">
            <div class="widget widget-tile" style="background-color: #FA4032; color: #ffffff;">
              <div class="chart sparkline">
                <i class="fa fa-ban fa-2x"></i>
              </div>
              <div class="data-info">
                <?php
                $result = $mysqli->query("SELECT COUNT(*) as count FROM obrs_booking_history WHERE pass_id = '$aid' AND status = 'Cancelled'");
                $row = $result->fetch_assoc();
                ?>
                <div class="desc">Cancelled Tickets</div>
                <div class="value"><span><?php echo $row['count']; ?></span></div>
              </div>
            </div>
          </div>

          <div class="col-12 col-lg-6 col-xl-4">
            <div class="widget widget-tile" style="background-color: #00BFFF; color: white;">
              <div class="chart sparkline">
                <i class="fa fa-ticket fa-2x"></i>
              </div>
              <div class="data-info">
                <?php
                $result = $mysqli->query("SELECT COUNT(*) as count FROM obrs_booking_history WHERE pass_id = '$aid' AND payment_status = 'Pending'");
                $row = $result->fetch_assoc();
                ?>
                <div class="desc">Pending Payment</div>
                <div class="value"><span><?php echo $row['count']; ?></span></div>
              </div>
            </div>
          </div>

          <div class="col-12 col-lg-6 col-xl-4">
            <div class="widget widget-tile" style="background-color: #FFD700; color: #000000;">
              <div class="chart sparkline">
                <i class="fa fa-money fa-2x"></i>
              </div>
              <div class="data-info">
                <?php
                $result = $mysqli->query("SELECT SUM(total_cost) as total FROM obrs_booking_history WHERE pass_id = '$aid' AND payment_status = 'Paid' AND status = 'Active'");
                $row = $result->fetch_assoc();
                ?>
                <div class="desc">Total Amount Spent</div>
                <div class="value"><span>₹<?php echo number_format($row['total'], 2); ?></span></div>
              </div>
            </div>
          </div>

          <div class="col-12 col-lg-6 col-xl-4">
            <div class="widget widget-tile" style="background-color: #00BFFF; color: white;">
              <div class="chart sparkline">
                <i class="fa fa-ticket fa-2x"></i>
              </div>
              <div class="data-info">
                <?php
                $result = $mysqli->query("SELECT COUNT(*) as count FROM obrs_booking_history WHERE pass_id = '$aid' AND payment_status = 'Pending' AND status = 'Expired'");
                $row = $result->fetch_assoc();
                ?>
                <div class="desc">Expired Tickets</div>
                <div class="value"><span><?php echo $row['count']; ?></span></div>
              </div>
            </div>
          </div>
        </div>



        <div class="row">
          <div class="col-sm-12">
            <div class="card card-table" style="background: linear-gradient(120deg, #E0EAFC 0%, #CFDEF3 100%); color: #2C3E50; box-shadow: 0 4px 20px rgba(0,0,0,0.1); border-radius: 15px;">
              <div class="card-header" style="border-bottom: 1px solid rgba(44,62,80,0.1); padding: 25px;">
                <div class="d-flex justify-content-between align-items-center">
                  <h4 style="margin: 0; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">
                    <i class="fa fa-ticket" style="margin-right: 10px;"></i>Latest Bookings
                  </h4>
                  <div>
                    <button onclick="window.location.reload();" class="btn btn-primary" style="background: linear-gradient(120deg, #3498db 0%, #2980b9 100%); border: none; padding: 8px 15px; border-radius: 20px;">
                      <i class="fa fa-refresh" style="margin-right: 5px;"></i>Refresh
                    </button>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <table class="table table-hover table-fw-widget datatable" style="margin: 0;">
                  <thead>
                    <tr style="border-bottom: 2px solid rgba(44,62,80,0.1);">
                      <th style="border: none; color: #34495E; font-weight: 600;">Booking ID</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Bus Details</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Journey Details</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Booking Details</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $ret = "SELECT b.*, t.name as bus_name 
                            FROM obrs_booking_history b
                            JOIN obrs_bus t ON b.bus_number = t.number
                            WHERE b.pass_id = '$aid'
                            ORDER BY b.id DESC LIMIT 5";
                    $stmt = $mysqli->prepare($ret);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    while ($row = $res->fetch_object()) {
                    ?>
                      <tr style="border-bottom: 1px solid rgba(44,62,80,0.1); transition: all 0.3s;">
                        <td style="border: none;">
                          <span class="badge bg-primary" style="font-size: 0.9em; padding: 8px 12px; color: white; border-radius: 8px;">
                            <?php echo htmlspecialchars($row->booking_id); ?>
                          </span>
                        </td>
                        <td style="border: none;">
                          <strong><?php echo htmlspecialchars($row->bus_name); ?></strong><br>
                          <small>Bus No: <?php echo htmlspecialchars($row->bus_number); ?></small>
                        </td>
                        <td style="border: none;">
                          <strong>From:</strong> <?php echo htmlspecialchars($row->dep_station); ?><br>
                          <strong>To:</strong> <?php echo htmlspecialchars($row->arr_station); ?><br>
                          <strong>Departure:</strong> <?php echo htmlspecialchars($row->dep_time) ?>
                        </td>
                        <td style="border: none;">
                          <strong>Seats:</strong> <?php echo htmlspecialchars($row->selected_seats); ?><br>
                          <strong>Fare:</strong> ₹<?php echo htmlspecialchars($row->bus_fare); ?><br>
                          <strong>Total:</strong> ₹<?php echo htmlspecialchars($row->total_cost); ?>
                        </td>
                        <td style="border: none;">
                          <?php if ($row->payment_status == 'Paid'): ?>
                            <span class="badge" style="background: linear-gradient(120deg, #50C878 0%, #228B22 100%); color: #FFFFFF; padding: 8px 15px; border-radius: 20px; font-weight: 500; font-size: 15px;">Paid</span>
                          <?php elseif ($row->status == 'Expired'): ?>
                            <span class="badge" style="background: linear-gradient(120deg, #FFD700 0%, #FFA500 100%); color: #000000; padding: 8px 15px; border-radius: 20px; font-weight: 500; font-size: 15px;">Expired</span>
                          <?php endif; ?>
                          <?php if ($row->status == 'Cancelled'): ?>
                            <span class="badge" style="background: linear-gradient(120deg, #FF6B6B 0%, #FF0000 100%); color: #FFFFFF; padding: 8px 15px; border-radius: 20px; font-weight: 500; font-size: 15px;">Cancelled</span>
                          <?php endif; ?>
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
      <!--footer-->
      <?php include('assets/inc/footer.php'); ?>
      <!--EndFooter-->
    </div>

  </div>

  <script src="assets/lib/jquery/jquery.min.js" type="text/javascript"></script>
  <script src="assets/lib/perfect-scrollbar/js/perfect-scrollbar.min.js" type="text/javascript"></script>
  <script src="assets/lib/bootstrap/dist/js/bootstrap.bundle.min.js" type="text/javascript"></script>
  <script src="assets/js/app.js" type="text/javascript"></script>
  <script src="assets/lib/jquery-flot/jquery.flot.js" type="text/javascript"></script>
  <script src="assets/lib/jquery-flot/jquery.flot.pie.js" type="text/javascript"></script>
  <script src="assets/lib/jquery-flot/jquery.flot.time.js" type="text/javascript"></script>
  <script src="assets/lib/jquery-flot/jquery.flot.resize.js" type="text/javascript"></script>
  <script src="assets/lib/jquery-flot/plugins/jquery.flot.orderBars.js" type="text/javascript"></script>
  <script src="assets/lib/jquery-flot/plugins/curvedLines.js" type="text/javascript"></script>
  <script src="assets/lib/jquery-flot/plugins/jquery.flot.tooltip.js" type="text/javascript"></script>
  <script src="assets/lib/jquery.sparkline/jquery.sparkline.min.js" type="text/javascript"></script>
  <script src="assets/lib/countup/countUp.min.js" type="text/javascript"></script>
  <script src="assets/lib/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
  <script src="assets/lib/jqvmap/jquery.vmap.min.js" type="text/javascript"></script>
  <script src="assets/lib/jqvmap/maps/jquery.vmap.world.js" type="text/javascript"></script>
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
  <script
    src="https://kit.fontawesome.com/f766ed9c4c.js"
    crossorigin="anonymous"></script>

  <script type="text/javascript">
    $(document).ready(function() {
      //-initialize the javascript
      App.init();
      App.dashboard();

      // Initialize DataTables with pagination
      $('.datatable').DataTable({
        "pageLength": 5,
        "lengthMenu": [
          [5, 10, 25, 50, -1],
          [5, 10, 25, 50, "All"]
        ]
      });
    });
  </script>
</body>

</html>