<?php
session_start();
include('assets/inc/config.php');
include('assets/inc/checklogin.php');
check_login();
$aid = $_SESSION['emp_id'];

// Get booking ID from URL
$booking_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$booking_id) {
  header("Location: emp-dashboard.php");
  exit();
}

// Get booking details from booking history
$query = "SELECT b.*, p.pass_fname, p.pass_lname, p.pass_email, p.pass_addr, p.pass_phone,
          t.name as bus_name, t.number as train_no,
          b.dep_station, b.arr_station, b.dep_time, b.selected_seats
          FROM obrs_booking_history b
          JOIN obrs_passenger p ON b.pass_id = p.pass_id
          JOIN obrs_bus t ON b.bus_number = t.number 
          WHERE b.id=?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_object();

if (!$booking) {
  header("Location: emp-dashboard.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<!--Head-->
<?php include('assets/inc/head.php'); ?>
<!--End Head-->

<body style="background-color: #F0F0D7;">
  <div class="be-wrapper be-fixed-sidebar">
    <!--Navigation bar-->
    <?php include("assets/inc/navbar.php"); ?>
    <!--Navigation-->

    <!--Sidebar-->
    <?php include("assets/inc/sidebar.php"); ?>
    <!--Sidebar-->
    <div class="be-content">
      <div class="page-head">
        <h2 class="page-head-title" style="color: black;">Print Ticket</h2>
        <nav aria-label="breadcrumb" role="navigation">
          <ol class="breadcrumb page-head-nav">
            <li class="breadcrumb-item"><a href="emp-dashboard.php" style="color: black;">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="emp-paid-tickets.php" style="color: black;">Tickets</a></li>
            <li class="breadcrumb-item active" style="color: black; text-decoration: underline;">Print Ticket</li>
          </ol>
        </nav>
      </div>

      <div class="main-content container-fluid">
        <div class="row">
          <div class="col-lg-12">

            <div id='printReceipt'>
              <div class="ticket-container" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 25px; padding: 40px; margin: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.15); position: relative; overflow: hidden; border: 2px solid #dee2e6;">
                <!-- Decorative Elements -->
                <div style="position: absolute; top: -80px; right: -80px; width: 200px; height: 200px; background: linear-gradient(45deg, rgba(52,152,219,0.1) 0%, rgba(52,152,219,0.05) 100%); border-radius: 50%; transform: rotate(-15deg);"></div>
                <div style="position: absolute; bottom: -60px; left: -60px; width: 150px; height: 150px; background: linear-gradient(45deg, rgba(46,204,113,0.1) 0%, rgba(46,204,113,0.05) 100%); border-radius: 50%; transform: rotate(30deg);"></div>

                <!-- Header -->
                <div class="ticket-header" style="text-align: center; margin-bottom: 35px; border-bottom: 3px dashed #dee2e6; padding-bottom: 25px;">
                  <h1 style="color: #3498db; font-size: 3.5em; margin: 0; text-transform: uppercase; letter-spacing: 6px; font-weight: 800; text-shadow: 2px 2px 4px rgba(0,0,0,0.1);">BusZy Pass</h1>
                  <div style="color: #34495e; font-size: 1.3em; margin-top: 15px; font-weight: 600;">Booking ID: <span style="color: #3498db; text-shadow: 1px 1px 2px rgba(52,152,219,0.2);"><?php echo htmlspecialchars($booking->booking_id); ?></span></div>
                </div>

                <!-- Passenger Info with QR Code -->
                <div style="display: flex; gap: 30px; margin-bottom: 30px;">
                  <div class="passenger-info" style="flex: 2; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); padding: 30px; border-radius: 20px; box-shadow: 0 8px 20px rgba(0,0,0,0.08); border: 1px solid #e9ecef;">
                    <h3 style="color: #2c3e50; margin-bottom: 25px; font-size: 1.5em; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; border-bottom: 2px solid #e9ecef; padding-bottom: 15px;">
                      <i class="fa fa-user-circle" style="margin-right: 12px; color: #3498db;"></i>Passenger Details
                    </h3>
                    <div style="display: grid; grid-template-columns: auto 1fr; gap: 20px; color: #2c3e50; font-size: 1.15em;">
                      <strong style="color: #34495e;">Name:</strong>
                      <span style="color: #2c3e50; font-weight: 500;"><?php echo htmlspecialchars($booking->pass_fname . ' ' . $booking->pass_lname); ?></span>

                      <strong style="color: #34495e;">Email:</strong>
                      <span style="color: #2c3e50; font-weight: 500;"><?php echo htmlspecialchars($booking->pass_email); ?></span>

                      <strong style="color: #34495e;">Mobile:</strong>
                      <span style="color: #2c3e50; font-weight: 500;"><?php echo htmlspecialchars($booking->pass_phone); ?></span>

                      <strong style="color: #34495e;">Address:</strong>
                      <span style="color: #2c3e50; font-weight: 500;"><?php echo htmlspecialchars($booking->pass_addr); ?></span>
                    </div>
                  </div>
                  
                  <!-- QR Code Section -->
                  <div style="flex: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); padding: 30px; border-radius: 20px; box-shadow: 0 8px 20px rgba(0,0,0,0.08); border: 1px solid #e9ecef;">
                    <div style="background: white; padding: 15px; border-radius: 15px; box-shadow: 0 8px 20px rgba(0,0,0,0.1);">
                      <?php
                        $qr_data = array(
                          'Booking ID' => $booking->booking_id,
                          'Name' => $booking->pass_fname . ' ' . $booking->pass_lname,
                          'Email' => $booking->pass_email,
                          'Phone' => $booking->pass_phone,
                          'Address' => $booking->pass_addr,
                          'Bus' => $booking->bus_name,
                          'Bus Number' => $booking->train_no,
                          'From' => $booking->dep_station,
                          'To' => $booking->arr_station,
                          'Departure' => $booking->dep_time,
                          'Seats' => $booking->selected_seats
                        );
                        $qr_string = json_encode($qr_data);
                      ?>
                      <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo urlencode($qr_string); ?>"
                        alt="QR Code" style="width: 200px; height: 200px;">
                    </div>
                    <p style="margin-top: 15px; color: #7f8c8d; font-size: 0.9em; text-align: center;">Scan to verify ticket</p>
                  </div>
                </div>

                <!-- Journey Details -->
                <div class="journey-details" style="display: flex; justify-content: space-between; margin: 25px 0; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); padding: 35px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); border: 1px solid #e9ecef;">
                  <div class="journey-from" style="text-align: center; flex: 1; padding: 20px; background: rgba(52,152,219,0.05); border-radius: 15px; transition: transform 0.3s;">
                    <h4 style="color: #2c3e50; margin-bottom: 15px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
                      <i class="fa fa-map-marker-alt" style="margin-right: 10px; color: #3498db;"></i>From
                    </h4>
                    <p style="font-size: 1.4em; color: #2c3e50; font-weight: 600;">
                      <?php echo htmlspecialchars($booking->dep_station); ?>
                    </p>
                  </div>
                  <div class="journey-arrow" style="display: flex; align-items: center; padding: 0 40px;">
                    <div style="position: relative;">
                      <div style="width: 80px; height: 3px; background: linear-gradient(90deg, #3498db 0%, #2ecc71 100%);"></div>
                      <i class="fa fa-bus" style="color: #3498db; font-size: 2em; position: absolute; top: -25px; left: 50%; transform: translateX(-50%);"></i>
                    </div>
                  </div>
                  <div class="journey-to" style="text-align: center; flex: 1; padding: 20px; background: rgba(46,204,113,0.05); border-radius: 15px; transition: transform 0.3s;">
                    <h4 style="color: #2c3e50; margin-bottom: 15px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
                      <i class="fa fa-map-marker-alt" style="margin-right: 10px; color: #2ecc71;"></i>To
                    </h4>
                    <p style="font-size: 1.4em; color: #2c3e50; font-weight: 600;">
                      <?php echo htmlspecialchars($booking->arr_station); ?>
                    </p>
                  </div>
                </div>

                <!-- Bus Details -->
                <div class="bus-details" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); padding: 35px; border-radius: 20px; margin-bottom: 35px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); border: 1px solid #e9ecef;">
                  <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 35px;">
                    <div style="flex: 1; min-width: 250px; background: rgba(52,152,219,0.05); padding: 30px; border-radius: 15px; transition: all 0.3s; box-shadow: 0 8px 20px rgba(0,0,0,0.05);">
                      <h4 style="color: #2c3e50; margin-bottom: 20px; font-size: 1.4em; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; border-bottom: 2px solid rgba(52,152,219,0.2); padding-bottom: 15px;">
                        <i class="fa fa-bus" style="margin-right: 12px; color: #3498db;"></i>Bus Information
                      </h4>
                      <p style="color: #2c3e50; line-height: 2.2; font-size: 1.15em;">
                        <strong style="color: #34495e; display: inline-block; width: 150px;">Bus Name:</strong>
                        <span style="color: #2c3e50; font-weight: 500;"><?php echo htmlspecialchars($booking->bus_name); ?></span><br>
                        <strong style="color: #34495e; display: inline-block; width: 150px;">Bus Number:</strong>
                        <span style="color: #2c3e50; font-weight: 500;"><?php echo htmlspecialchars($booking->train_no); ?></span><br>
                        <strong style="color: #34495e; display: inline-block; width: 150px;">Departure Time:</strong>
                        <span style="color: #2c3e50; font-weight: 500;"><?php echo htmlspecialchars($booking->dep_time); ?></span>
                      </p>
                    </div>
                    <div style="flex: 1; min-width: 250px; background: rgba(46,204,113,0.05); padding: 30px; border-radius: 15px; transition: all 0.3s; box-shadow: 0 8px 20px rgba(0,0,0,0.05);">
                      <h4 style="color: #2c3e50; margin-bottom: 20px; font-size: 1.4em; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; border-bottom: 2px solid rgba(46,204,113,0.2); padding-bottom: 15px;">
                        <i class="fa fa-chair" style="margin-right: 12px; color: #2ecc71;"></i>Seat Information
                      </h4>
                      <p style="color: #2c3e50; line-height: 2.2; font-size: 1.15em;">
                        <strong style="color: #34495e; display: inline-block; width: 150px;">Number of Seats:</strong>
                        <span style="color: #2c3e50; font-weight: 500;"><?php echo htmlspecialchars($booking->seats); ?></span><br>
                        <strong style="color: #34495e; display: inline-block; width: 150px;">Seat Numbers:</strong>
                        <span style="color: #2c3e50; font-weight: 500;"><?php echo htmlspecialchars($booking->selected_seats); ?></span><br>
                        <strong style="color: #34495e; display: inline-block; width: 150px;">Total Fare:</strong>
                        <span style="color: #2c3e50; font-weight: 500;">₹<?php echo htmlspecialchars($booking->bus_fare * $booking->seats); ?></span>
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Important Notes -->
                <div class="important-notes" style="background: linear-gradient(120deg, rgba(231, 76, 60, 0.05) 0%, rgba(231, 76, 60, 0.1) 100%); padding: 25px; border-radius: 20px; margin-top: 30px; border: 1px solid rgba(231, 76, 60, 0.2); box-shadow: 0 4px 15px rgba(231, 76, 60, 0.05);">
                  <h4 style="color: #c0392b; margin-bottom: 15px; font-size: 1.3em; font-weight: 600; display: flex; align-items: center; gap: 10px;">
                    <i class="fa fa-exclamation-triangle" style="font-size: 1.2em; color: #e74c3c;"></i>
                    Important Notes
                  </h4>
                  <ul style="color: #34495e; margin: 0; list-style: none; padding: 0;">
                    <li style="padding: 8px 0; margin: 5px 0; display: flex; align-items: center; gap: 10px;">
                      <i class="fa fa-check-circle" style="color: #e74c3c;"></i>
                      <span>This ticket is non-transferable</span>
                    </li>
                    <li style="padding: 8px 0; margin: 5px 0; display: flex; align-items: center; gap: 10px;">
                      <i class="fa fa-id-card" style="color: #e74c3c;"></i>
                      <span>Please carry a valid photo ID for verification</span>
                    </li>
                    <li style="padding: 8px 0; margin: 5px 0; display: flex; align-items: center; gap: 10px;">
                      <i class="fa fa-clock" style="color: #e74c3c;"></i>
                      <span>Arrive at least 30 minutes before departure time</span>
                    </li>
                    <li style="padding: 8px 0; margin: 5px 0; display: flex; align-items: center; gap: 10px;">
                      <i class="fa fa-ticket-alt" style="color: #e74c3c;"></i>
                      <span>Keep this ticket handy - you'll need to show it when boarding</span>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <div class="text-center" style="margin: 20px;">
              <button id="print" onclick="printContent('printReceipt');" class="btn"
                style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); 
                             color: white; padding: 12px 30px; border-radius: 25px; 
                             border: none; font-size: 1.1em; cursor: pointer;
                             transition: transform 0.2s;">
                <i class="fa fa-print" style="margin-right: 8px;"></i> Print Ticket
              </button>
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
  <script type="text/javascript">
    $(document).ready(function() {
      App.init();
    });
  </script>
  <script>
    function printContent(el) {
      var restorepage = $('body').html();
      var printcontent = $('#' + el).clone();
      $('body').empty().html(printcontent);
      window.print();
      $('body').html(restorepage);
    }
  </script>
</body>

</html>
