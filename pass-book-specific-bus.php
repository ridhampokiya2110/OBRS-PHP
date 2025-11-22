<?php
session_start();
include('assets/inc/config.php');
include('assets/inc/checklogin.php');
check_login();
$aid = $_SESSION['pass_id'];

// Check for expired unpaid bookings and restore seats
$check_expired = "SELECT * FROM obrs_booking_history WHERE status='Active' AND payment_status='Pending' 
                  AND booking_date < DATE_SUB(NOW(), INTERVAL 5 MINUTE)";
$expired_result = $mysqli->query($check_expired);
while ($expired = $expired_result->fetch_object()) {
  // Get Bus details
  $train_query = "SELECT id, available_seats, booked_seats FROM obrs_bus WHERE number=?";
  $stmt = $mysqli->prepare($train_query);
  $stmt->bind_param('s', $expired->bus_number);
  $stmt->execute();
  $train = $stmt->get_result()->fetch_object();

  // Restore seats
  $expired_seats = explode(",", $expired->selected_seats);
  $current_booked = explode(",", $train->booked_seats);
  $new_booked = array_diff($current_booked, $expired_seats);
  $new_booked_str = implode(",", $new_booked);
  $new_available = $train->available_seats + count($expired_seats);

  // Update Bus seats
  $update = "UPDATE obrs_bus SET available_seats=?, booked_seats=? WHERE id=?";
  $stmt = $mysqli->prepare($update);
  $stmt->bind_param('isi', $new_available, $new_booked_str, $train->id);
  $stmt->execute();

  // Update booking status
  $update_booking = "UPDATE obrs_booking_history SET status='Expired' WHERE booking_id=?";
  $stmt = $mysqli->prepare($update_booking);
  $stmt->bind_param('s', $expired->booking_id);
  $stmt->execute();

  // Clear passenger booking if it's their booking that expired
  if ($expired->pass_id == $aid) {
    // First check if passenger still has this booking
    $check_booking = "SELECT pass_bus_number FROM obrs_passenger 
                     WHERE pass_id=? AND pass_bus_number=?";
    $stmt = $mysqli->prepare($check_booking);
    $stmt->bind_param('is', $aid, $expired->bus_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $clear_passenger = "UPDATE obrs_passenger SET 
                         pass_bus_number=NULL, 
                         pass_bus_name=NULL,
                         pass_dep_station=NULL, 
                         pass_dep_time=NULL, 
                         pass_arr_station=NULL,
                         pass_bus_fare=NULL, 
                         seats=NULL, 
                         selected_seats=NULL, 
                         seat_type=NULL 
                         WHERE pass_id=? AND pass_bus_number=?";
      $stmt = $mysqli->prepare($clear_passenger);
      $stmt->bind_param('is', $aid, $expired->bus_number);
      $stmt->execute();

      $err = "Your booking has expired due to non-payment within 5 minutes. The seats have been released.";
    }
  }
}

// Handle ticket cancellation
if (isset($_POST['Cancel_Ticket'])) {
  // Get passenger's current booking details
  $ret = "SELECT pass_bus_number, seats FROM obrs_passenger WHERE pass_id=?";
  $stmt = $mysqli->prepare($ret);
  $stmt->bind_param('i', $aid);
  $stmt->execute();
  $res = $stmt->get_result();
  $row = $res->fetch_object();
  $bus_number = $row->pass_bus_number;
  $booked_seats = $row->seats;

  // Get Bus details
  $ret = "SELECT id, available_seats, booked_seats FROM obrs_bus WHERE number=?";
  $stmt = $mysqli->prepare($ret);
  $stmt->bind_param('s', $bus_number);
  $stmt->execute();
  $res = $stmt->get_result();
  $row = $res->fetch_object();
  $train_id = $row->id;
  $available_seats = $row->available_seats;
  $current_booked = explode(",", $row->booked_seats);

  // Remove cancelled seats from booked seats
  $cancelled_seats = explode(",", $booked_seats);
  $new_booked = array_diff($current_booked, $cancelled_seats);
  $new_booked_str = implode(",", $new_booked);

  // Update available seats count
  $new_available = $available_seats + count($cancelled_seats);

  // Update Bus seats
  $update = "UPDATE obrs_bus SET available_seats=?, booked_seats=? WHERE id=?";
  $stmt = $mysqli->prepare($update);
  $stmt->bind_param('isi', $new_available, $new_booked_str, $train_id);
  $stmt->execute();

  // Save cancelled booking to history table
  $save_history = "INSERT INTO obrs_booking_history (pass_id, bus_number, seats, status, cancel_date) 
                  SELECT pass_id, pass_bus_number, seats, 'Cancelled', NOW() 
                  FROM obrs_passenger WHERE pass_id=?";
  $stmt = $mysqli->prepare($save_history);
  $stmt->bind_param('i', $aid);
  $stmt->execute();

  // Clear passenger booking details
  $update = "UPDATE obrs_passenger SET pass_bus_number=NULL, pass_bus_name=NULL, pass_dep_station=NULL, 
            pass_dep_time=NULL, pass_arr_station=NULL, pass_bus_fare=NULL, seats=NULL, selected_seats=NULL, seat_type=NULL WHERE pass_id=?";
  $stmt = $mysqli->prepare($update);
  $stmt->bind_param('i', $aid);
  $stmt->execute();

  if ($stmt) {
    $succ = "Ticket cancelled successfully";
  } else {
    $err = "Please try again later";
  }
}

if (isset($_POST['Book_Train'])) {
  $pass_bus_number = $_POST['pass_bus_number'];
  $pass_bus_name = $_POST['pass_bus_name'];
  $pass_dep_station = $_POST['pass_dep_station'];
  $pass_dep_time = $_POST['pass_dep_time'];
  $pass_arr_station = $_POST['pass_arr_station'];
  $pass_bus_fare = $_POST['pass_bus_fare'];

  $selected_seats_arr = $_POST['selected_seats'];

  // Check if more than 6 seats are selected
  if (count($selected_seats_arr) > 6) {
    $err = "You cannot select more than 6 seats";
  } else {
    $seat_types = array(); // Array to store seat types

    foreach ($selected_seats_arr as $seat) {
      if (($seat >= 7 && $seat <= 18) || ($seat >= 25 && $seat <= 36)) {
        $seat_types[] = "Single";
      } else {
        $seat_types[] = "Single";
      }
    }

    $pass_seats = count($selected_seats_arr);
    $selected_seats = implode(",", $selected_seats_arr);
    $seat_type = implode(",", $seat_types); // Convert seat types array to string

    // Calculate total cost
    $total_cost = $pass_bus_fare * $pass_seats;

    // Get Bus ID and available seats
    $train_id = $_GET['id'];
    $ret = "SELECT available_seats, booked_seats FROM obrs_bus WHERE id=?";
    $stmt = $mysqli->prepare($ret);
    $stmt->bind_param('i', $train_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_object();
    $available_seats = $row->available_seats;
    $booked_seats = $row->booked_seats ? explode(",", $row->booked_seats) : array();

    // Check if seats are already booked
    $seats_available = true;
    foreach ($selected_seats_arr as $seat) {
      if (in_array($seat, $booked_seats)) {
        $seats_available = false;
        break;
      }
    }

    if ($seats_available) {
      // Generate unique booking ID
      $booking_id = 'TKT' . strtoupper(substr($pass_bus_number, 0, 2)) .
        date('ymd') . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4);

      // Save booking to history table first with pending payment status
      $save_booking = "INSERT INTO obrs_booking_history (booking_id, pass_id, bus_number, bus_name, dep_station, 
                                dep_time, arr_station, bus_fare, seats, selected_seats, seat_type, total_cost, status, payment_status, booking_date)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Active', 'Pending', NOW())";
      $stmt = $mysqli->prepare($save_booking);
      $stmt->bind_param(
        'sisssssssssd',
        $booking_id,
        $aid,
        $pass_bus_number,
        $pass_bus_name,
        $pass_dep_station,
        $pass_dep_time,
        $pass_arr_station,
        $pass_bus_fare,
        $pass_seats,
        $selected_seats,
        $seat_type,
        $total_cost
      );
      $stmt->execute();

      // Update passenger booking with selected_seats and seat_type
      $query = "update obrs_passenger set pass_bus_number=?, pass_bus_name=?, pass_dep_station=?, pass_dep_time=?, pass_arr_station=?, pass_bus_fare=?, seats=?, selected_seats=?, seat_type=? where pass_id=?";
      $stmt = $mysqli->prepare($query);
      $rc = $stmt->bind_param('sssssssssi', $pass_bus_number, $pass_bus_name, $pass_dep_station, $pass_dep_time, $pass_arr_station, $pass_bus_fare, $selected_seats, $selected_seats, $seat_type, $aid);
      $stmt->execute();

      // Update available seats and booked seats
      $new_available = $available_seats - $pass_seats;
      $new_booked = $booked_seats ? implode(",", array_merge($booked_seats, $selected_seats_arr)) : $selected_seats;
      $update_seats = "UPDATE obrs_bus SET available_seats=?, booked_seats=? WHERE id=?";
      $stmt = $mysqli->prepare($update_seats);
      $stmt->bind_param('isi', $new_available, $new_booked, $train_id);
      $stmt->execute();

      if ($stmt) {
        header("Location: pass-bus-checkout-ticket.php");
        exit();
      } else {
        $err = "Please Try Again Later";
      }
    } else {
      $err = "Selected seats are no longer available";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<!--Head-->
<?php include('assets/inc/head.php'); ?>
<style>
  .seat-layout {
    display: flex;
    flex-direction: column;
    gap: 20px;
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    background: #f5f5f5;
    border-radius: 8px;
  }

  .birth-row {
    display: flex;
    justify-content: space-between;
    gap: 40px;
  }

  .birth-section {
    flex: 1;
  }

  .birth-title {
    text-align: center;
    font-weight: bold;
    margin-bottom: 10px;
    color: #333;
  }

  .seat-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
  }

  .double-seats {
    display: flex;
    gap: 5px;
  }

  .single-seat {
    width: 60px;
  }

  .seat {
    padding: 10px;
    text-align: center;
    border: 1px solid #ccc;
    cursor: pointer;
    border-radius: 4px;
    background: white;
    width: 60px;
    font-size: 12px;
  }

  .seat.selected {
    background-color: #28a745;
    color: white;
  }

  .seat.booked {
    background-color: #dc3545;
    color: white;
    cursor: not-allowed;
  }

  .seat:hover:not(.booked) {
    background-color: #e9ecef;
  }

  .legend {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-top: 20px;
  }

  .legend-item {
    display: flex;
    align-items: center;
    gap: 5px;
  }

  .legend-box {
    width: 20px;
    height: 20px;
    border: 1px solid #ccc;
  }

  .legend-box.available {
    background: white;
  }

  .legend-box.selected {
    background: #28a745;
  }

  .legend-box.booked {
    background: #dc3545;
  }
</style>
<!--End Head-->

<body style="background-color: #F0F0D7;">
  <div class="be-wrapper be-fixed-sidebar ">
    <!--Navigation Bar-->
    <?php include('assets/inc/navbar.php'); ?>
    <!--End Navigation Bar-->

    <!--Sidebar-->
    <?php include('assets/inc/sidebar.php'); ?>
    <!--End Sidebar-->
    <div class="be-content">
      <div class="page-head">
        <h2 class="page-head-title" style="color: black;">The Wheel Deal: BusZy Booking</h2>
        <nav aria-label="breadcrumb" role="navigation">
          <ol class="breadcrumb page-head-nav">
            <li class="breadcrumb-item"><a href="#" style="color: black;">BusZy Booking </a></li>
            <li class="breadcrumb-item"><a href="pass-book-bus.php" style="color: black;">Reserve Your Seats</a></li>
            <li class="breadcrumb-item active" style="color: black; text-decoration: underline;">The Wheel Deal: BusZy Booking</li>
          </ol>
        </nav>
      </div>
      <?php if (isset($succ)) { ?>
        <!--This code for injecting an alert-->
        <script>
          setTimeout(function() {
              swal("Success!", "<?php echo $succ; ?>!", "success");
            },
            100);
        </script>

      <?php } ?>
      <?php if (isset($err)) { ?>
        <!--This code for injecting an alert-->
        <script>
          setTimeout(function() {
              swal("Failed!", "<?php echo $err; ?>!", "Failed");
            },
            100);
        </script>

      <?php } ?>
      <div class="main-content container-fluid">
        <?php
        $aid = $_SESSION['pass_id'];
        $ret = "select * from obrs_passenger where pass_id=?";
        $stmt = $mysqli->prepare($ret);
        $stmt->bind_param('i', $aid);
        $stmt->execute(); //ok
        $res = $stmt->get_result();
        //$cnt=1;
        while ($row = $res->fetch_object()) {
        ?>
          <div class="row">
            <div class="col-md-12">
              <div class="card" style="background: #D8E4F8; border-radius: 20px; box-shadow: 0 8px 32px rgba(0,0,0,0.15), 0 4px 8px rgba(0,0,0,0.1); border: 1px solid rgba(0,0,0,0.05);">
                <div class="card-header card-header-divider" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-bottom: 2px solid rgba(0,0,0,0.05); padding: 25px; border-radius: 20px 20px 0 0;">
                  <span style="color: #2C3E50; font-size: 1.4em; font-weight: 600; letter-spacing: 0.5px;" class="card-subtitle">Fill All Details (Don't Be Like Captain Procrastinator!)</span>
                </div>
                <div class="card-body" style="padding: 40px;">
                  <form method="POST">
                    <div class="form-group row" style="margin-bottom: 25px;">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;"> First Name</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" readonly name="pass_fname" value="<?php echo $row->pass_fname; ?>" id="inputText3" type="text" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                      </div>
                    </div>
                    <div class="form-group row" style="margin-bottom: 25px;">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;"> Last Name</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" readonly name="pass_lname" value="<?php echo $row->pass_lname; ?>" id="inputText3" type="text" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                      </div>
                    </div>
                    <div class="form-group row" style="margin-bottom: 25px;">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;"> Phone Number</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" readonly name="pass_phone" value="<?php echo substr($row->pass_phone, 0, 10); ?>" id="inputText3" type="text" maxlength="10" pattern="[0-9]{10}" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                      </div>
                    </div>
                    <div class="form-group row" style="margin-bottom: 25px;">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;"> Address</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" readonly name="pass_addr" value="<?php echo $row->pass_addr; ?>" id="inputText3" type="text" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                      </div>
                    </div>

                    <!--Lets get the details of one single Bus using its Bus Id 
                    and pass it to this user instance-->
                    <?php
                    $id = $_GET['id'];
                    $ret = "select * from obrs_bus where id=?";
                    $stmt = $mysqli->prepare($ret);
                    $stmt->bind_param('i', $id);
                    $stmt->execute(); //ok
                    $res = $stmt->get_result();
                    //$cnt=1;
                    while ($row = $res->fetch_object()) {
                      $booked_seats = $row->booked_seats ? explode(",", $row->booked_seats) : array();
                    ?>
                      <div class="form-group row" style="margin-bottom: 25px;">
                        <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;">Bus Number</label>
                        <div class="col-12 col-sm-8 col-lg-6">
                          <input class="form-control" readonly name="pass_bus_number" value="<?php echo $row->number; ?>" id="inputText3" type="text" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                        </div>
                      </div>
                      <div class="form-group row" style="margin-bottom: 25px;">
                        <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;">Bus Name</label>
                        <div class="col-12 col-sm-8 col-lg-6">
                          <input class="form-control" readonly name="pass_bus_name" value="<?php echo $row->name; ?>" id="inputText3" type="text" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                        </div>
                      </div>
                      <div class="form-group row" style="margin-bottom: 25px;">
                        <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;">Departure</label>
                        <div class="col-12 col-sm-8 col-lg-6">
                          <input class="form-control" readonly name="pass_dep_station" value="<?php echo $row->current; ?>" id="inputText3" type="text" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                        </div>
                      </div>

                      <div class="form-group row" style="margin-bottom: 25px;">
                        <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;"> Arrival </label>
                        <div class="col-12 col-sm-8 col-lg-6">
                          <input class="form-control" readonly name="pass_arr_station" value="<?php echo $row->destination; ?>" id="inputText3" type="text" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                        </div>
                      </div>
                      <div class="form-group row" style="margin-bottom: 25px;">
                        <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;"> Departure Time</label>
                        <div class="col-12 col-sm-8 col-lg-6">
                          <input class="form-control" readonly name="pass_dep_time" value="<?php echo date('d M, Y \a\t h:i A', strtotime(htmlspecialchars($row->time))); ?>" id="inputText3" type="text" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                        </div>
                      </div>
                      <div class="form-group row" style="margin-bottom: 25px;">
                        <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;">Bus Fare</label>
                        <div class="col-12 col-sm-8 col-lg-6">
                          <input class="form-control" readonly name="pass_bus_fare" value="<?php echo $row->fare; ?>" id="inputText3" type="text" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                        </div>
                      </div>

                      <!-- Seat Selection -->
                      <div class="form-group row">
                        <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;"> Select Seats</label>
                        <div class="col-12 col-sm-8 col-lg-6">
                          <div class="seat-layout">
                            <!-- Driver Section -->
                            <div class="birth-section" style="display: flex; flex-direction: column; align-items: flex-end;">
                              <div class="birth-title" style="text-align: right; width: 100%;">Driver Section</div>
                              <div class="seat-row">
                                <div class="driver-seat" style="margin-left: auto;">
                                  <div class="seat booked">
                                    <i class="fas fa-steering-wheel"></i>
                                    Driver
                                  </div>
                                </div>
                              </div>
                            </div>

                            <!-- Lower Birth -->
                            <div class="birth-section">
                              <div class="birth-title">Lower Birth</div>
                              <?php for ($row = 1; $row <= 6; $row++) { ?>
                                <div class="seat-row">
                                  <!-- Single seat on left -->
                                  <?php
                                  $seat_num = $row; // Single seats: 1,2,3,4,5,6
                                  $booked = in_array($seat_num, $booked_seats) ? 'booked' : '';
                                  echo "<div class='seat single-seat $booked' data-seat='$seat_num'>
                                        L$seat_num
                                        <input type='checkbox' name='selected_seats[]' value='$seat_num' style='display:none;' " . ($booked ? 'disabled' : '') . ">
                                     </div>";
                                  ?>
                                  <!-- Double seats on right -->
                                  <div class="double-seats">
                                    <?php
                                    $double_seat_start = ($row - 1) * 2 + 7; // Double seats: 7-8, 9-10, 11-12, etc
                                    for ($i = 0; $i < 2; $i++) {
                                      $seat_num = $double_seat_start + $i;
                                      $booked = in_array($seat_num, $booked_seats) ? 'booked' : '';
                                      echo "<div class='seat $booked' data-seat='$seat_num'>
                                            L$seat_num
                                            <input type='checkbox' name='selected_seats[]' value='$seat_num' style='display:none;' " . ($booked ? 'disabled' : '') . ">
                                         </div>";
                                    }
                                    ?>
                                  </div>
                                </div>
                              <?php } ?>
                            </div>

                            <!-- Upper Birth -->
                            <div class="birth-section">
                              <div class="birth-title">Upper Birth</div>
                              <?php for ($row = 1; $row <= 6; $row++) { ?>
                                <div class="seat-row">
                                  <!-- Single seat on left -->
                                  <?php
                                  $seat_num = $row + 18; // Single seats: 19,20,21,22,23,24
                                  $booked = in_array($seat_num, $booked_seats) ? 'booked' : '';
                                  echo "<div class='seat single-seat $booked' data-seat='$seat_num'>
                                        U$seat_num
                                        <input type='checkbox' name='selected_seats[]' value='$seat_num' style='display:none;' " . ($booked ? 'disabled' : '') . ">
                                     </div>";
                                  ?>
                                  <!-- Double seats on right -->
                                  <div class="double-seats">
                                    <?php
                                    $double_seat_start = ($row - 1) * 2 + 25; // Double seats: 25-26, 27-28, 29-30, etc
                                    for ($i = 0; $i < 2; $i++) {
                                      $seat_num = $double_seat_start + $i;
                                      $booked = in_array($seat_num, $booked_seats) ? 'booked' : '';
                                      echo "<div class='seat $booked' data-seat='$seat_num'>
                                            U$seat_num
                                            <input type='checkbox' name='selected_seats[]' value='$seat_num' style='display:none;' " . ($booked ? 'disabled' : '') . ">
                                         </div>";
                                    }
                                    ?>
                                  </div>
                                </div>
                              <?php } ?>
                            </div>

                            <!-- Legend -->
                            <div class="legend">
                              <div class="legend-item">
                                <div class="legend-box available"></div>
                                <span>Available</span>
                              </div>
                              <div class="legend-item">
                                <div class="legend-box selected"></div>
                                <span>Selected</span>
                              </div>
                              <div class="legend-item">
                                <div class="legend-box booked"></div>
                                <span>Booked</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!--End Bus  isntance-->
                    <?php } ?>

                    <div class="col-sm-12">
                      <p class="text-center" style="margin-top: 30px;">
                        <input class="btn btn-space btn-success" value="Book Bus" name="Book_Train" type="submit" style="border-radius: 12px; padding: 12px 30px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2); transition: all 0.3s ease; margin-right: 20px;">
                        <a href="pass-book-bus.php" class="btn btn-space btn-danger" style="border-radius: 12px; padding: 12px 30px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2); transition: all 0.3s ease;">Cancel Ticket</a>
                      </p>
                    </div>
                  </form>
                </div>
              </div>
            </div>

          <?php } ?>

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
    <script src="assets/lib/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
    <script src="assets/lib/jquery.nestable/jquery.nestable.js" type="text/javascript"></script>
    <script src="assets/lib/moment.js/min/moment.min.js" type="text/javascript"></script>
    <script src="assets/lib/datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="assets/lib/select2/js/select2.min.js" type="text/javascript"></script>
    <script src="assets/lib/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script src="assets/lib/bootstrap-slider/bootstrap-slider.min.js" type="text/javascript"></script>
    <script src="assets/lib/bs-custom-file-input/bs-custom-file-input.js" type="text/javascript"></script>
    <script type="text/javascript">
      $(document).ready(function() {
        // Initialize the javascript
        App.init();
        App.formElements();

        // Seat selection logic and total cost calculation
        var fare = parseFloat($('input[name="pass_bus_fare"]').val());
        var totalCost = 0;

        $('.seat:not(.booked)').click(function() {
          var selectedSeats = $('.seat.selected').length;

          // Check if deselecting
          if ($(this).hasClass('selected')) {
            $(this).toggleClass('selected');
            var checkbox = $(this).find('input[type="checkbox"]');
            checkbox.prop('checked', !checkbox.prop('checked'));
          }
          // Check if selecting new seat
          else if (selectedSeats < 6) {
            $(this).toggleClass('selected');
            var checkbox = $(this).find('input[type="checkbox"]');
            checkbox.prop('checked', !checkbox.prop('checked'));
          } else {
            alert('You cannot select more than 6 seats');
            return;
          }

          // Update total cost
          totalCost = $('.seat.selected').length * fare;
          $('.total-cost').remove(); // Remove existing total cost display
          if (totalCost > 0) {
            $('.legend').append('<div class="legend-item total-cost"><span>Total Cost: ₹' + totalCost.toFixed(2) + '</span></div>');
          }
        });
      });
    </script>
</body>

</html>