<?php
session_start();
include('assets/inc/config.php');
include('assets/inc/checklogin.php');
check_login();
$aid = $_SESSION['admin_id'];
?>
<!DOCTYPE html>
<html lang="en">
<!--Head-->
<?php include("assets/inc/head.php"); ?>
<!--End Head-->

<body>
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
          <div class="col-12 col-lg-6 col-xl-6">
            <div class="widget widget-tile">
              <div class="chart sparkline"><i class="material-icons">attach_money</i></div>
              <div class="data-info">
                <?php
                // Get total earnings from paid bookings
                $result = "SELECT SUM(total_cost) FROM obrs_booking_history WHERE payment_status = 'Paid' AND status = 'Active'";
                $stmt = $mysqli->prepare($result);
                $stmt->execute();
                $stmt->bind_result($earnings);
                $stmt->fetch();
                $stmt->close();

                // Set earnings to 0 if null
                $earnings = $earnings ?: 0;
                ?>
                <div class="desc">Total Earnings</div>
                <div class="value">
                  <span>₹<?php echo number_format($earnings, 2); ?></span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-lg-6 col-xl-6">
            <div class="widget widget-tile">
              <div class="chart sparkline"><i class="material-icons">assignment_late</i></div>
              <div class="data-info">
                <?php
                // Get count of pending payments
                $result = "SELECT COUNT(*) FROM obrs_booking_history WHERE payment_status = 'Pending' AND status = 'Active'";
                $stmt = $mysqli->prepare($result);
                $stmt->execute();
                $stmt->bind_result($pending_count);
                $stmt->fetch();
                $stmt->close();

                // Set pending count to 0 if null
                $pending_count = $pending_count ?: 0;
                ?>
                <div class="desc">Pending Checkouts</div>
                <div class="value">
                  <span class="indicator indicator-warning mdi mdi-chevron-right"></span>
                  <span><?php echo $pending_count; ?></span>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 col-lg-6 col-xl-6">
            <div class="widget widget-tile">
              <div class="chart sparkline"><i class="material-icons">loyalty</i></div>
              <div class="data-info">
                <?php
                // Get count of cancelled paid tickets
                $result = "SELECT COUNT(*) FROM obrs_booking_history WHERE payment_status = 'Paid' AND status = 'Cancelled'";
                $stmt = $mysqli->prepare($result);
                $stmt->execute();
                $stmt->bind_result($cancelled_tickets);
                $stmt->fetch();
                $stmt->close();

                // Set cancelled tickets to 0 if null
                $cancelled_tickets = $cancelled_tickets ?: 0;
                ?>
                <div class="desc">Cancelled Paid Tickets</div>
                <div class="value">
                  <span class="indicator indicator-positive mdi mdi-chevron-right" style="color: red;"></span>
                  <span><?php echo $cancelled_tickets; ?></span>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 col-lg-6 col-xl-6">
            <div class="widget widget-tile">
              <div class="chart sparkline"><i class="material-icons">rowing</i></div>
              <div class="data-info">
                <?php
                //code for summing up number of bus tickets
                $result = "SELECT count(*) FROM obrs_booking_history WHERE payment_status = 'Paid' AND status = 'Active'";
                $stmt = $mysqli->prepare($result);
                $stmt->execute();
                $stmt->bind_result($ticket);
                $stmt->fetch();
                $stmt->close();
                ?>
                <div class="desc">Active Paid Reservations</div>
                <div class="value">
                  <span class="indicator indicator-positive mdi mdi-chevron-right"></span>
                  <span><?php echo $ticket; ?></span>
                </div>
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
                    <i class="fa fa-chart-pie" style="margin-right: 10px;"></i>Tickets Payment Status
                  </h4>
                  <div>
                    <button onclick="window.location.reload();" class="btn btn-primary" style="background: linear-gradient(120deg, #3498db 0%, #2980b9 100%); border: none; padding: 8px 15px; border-radius: 20px;">
                      <i class="fa fa-refresh" style="margin-right: 5px;"></i>Refresh
                    </button>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div id="PieChart" style="height: 400px; width: 100%;"></div>
                <script type="text/javascript">
                  window.onload = function() {
                    var options = {
                      exportEnabled: true,
                      animationEnabled: true,
                      backgroundColor: "transparent",
                      title: {
                        fontFamily: "Poppins",
                        fontSize: 24,
                        fontWeight: "normal",
                        padding: 20
                      },
                      legend: {
                        cursor: "pointer",
                        itemclick: explodePie,
                        fontFamily: "Poppins",
                        fontSize: 14
                      },
                      data: [{
                        type: "pie",
                        startAngle: 45,
                        showInLegend: true,
                        legendText: "{label}",
                        indexLabel: "{label}: {y}",
                        indexLabelFontFamily: "Poppins",
                        indexLabelFontSize: 12,
                        yValueFormatString: "#,##0",
                        dataPoints: [
                          <?php
                          // Get count of paid tickets
                          $result = "SELECT COUNT(*) FROM obrs_booking_history WHERE payment_status = 'Paid' AND status = 'Active'";
                          $stmt = $mysqli->prepare($result);
                          $stmt->execute();
                          $stmt->bind_result($paid);
                          $stmt->fetch();
                          $stmt->close();

                          // Get count of pending tickets
                          $result = "SELECT COUNT(*) FROM obrs_booking_history WHERE payment_status = 'Pending' AND status = 'Active'";
                          $stmt = $mysqli->prepare($result);
                          $stmt->execute();
                          $stmt->bind_result($pending);
                          $stmt->fetch();
                          $stmt->close();

                          // Get count of cancelled tickets
                          $result = "SELECT COUNT(*) FROM obrs_booking_history WHERE payment_status = 'Paid' AND status = 'Cancelled'";
                          $stmt = $mysqli->prepare($result);
                          $stmt->execute();
                          $stmt->bind_result($cancelled);
                          $stmt->fetch();
                          $stmt->close();
                          ?> {
                            label: "Paid Reservation",
                            y: <?php echo $paid; ?>,
                            color: "#27ae60"
                          },
                          {
                            label: "Pending Payments",
                            y: <?php echo $pending; ?>,
                            color: "#f39c12"
                          },
                          {
                            label: "Cancelled Tickets",
                            y: <?php echo $cancelled; ?>,
                            color: "#e74c3c"
                          }
                        ]
                      }]
                    };
                    $("#PieChart").CanvasJSChart(options);
                  }

                  function explodePie(e) {
                    if (typeof(e.dataSeries.dataPoints[e.dataPointIndex].exploded) === "undefined" || !e.dataSeries.dataPoints[e.dataPointIndex].exploded) {
                      e.dataSeries.dataPoints[e.dataPointIndex].exploded = true;
                    } else {
                      e.dataSeries.dataPoints[e.dataPointIndex].exploded = false;
                    }
                    e.chart.render();
                  }
                </script>
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
  <script src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>
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

  <script type="text/javascript">
    $(document).ready(function() {
      App.init();
      App.dashboard();
    });
  </script>
</body>

</html>