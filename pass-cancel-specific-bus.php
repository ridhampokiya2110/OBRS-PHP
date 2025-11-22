<?php
session_start();
include('assets/inc/config.php');
include('assets/inc/checklogin.php');
check_login();
$aid = $_SESSION['pass_id'];

if (isset($_POST['Cancel_Seats']) && isset($_POST['booking_id']) && isset($_POST['seats_to_cancel'])) {
  $booking_id = $_POST['booking_id'];
  $seats_to_cancel = $_POST['seats_to_cancel'];

  // Get booking details
  $ret = "SELECT * FROM obrs_booking_history WHERE id=? AND pass_id=? AND status='Active'";
  $stmt = $mysqli->prepare($ret);
  $stmt->bind_param('ii', $booking_id, $aid);
  $stmt->execute();
  $res = $stmt->get_result();
  $booking = $res->fetch_object();

  if ($booking) {
    // Get train details
    $ret = "SELECT id, available_seats, booked_seats FROM obrs_bus WHERE number=?";
    $stmt = $mysqli->prepare($ret);
    $stmt->bind_param('s', $booking->bus_number);
    $stmt->execute();
    $res = $stmt->get_result();
    $train = $res->fetch_object();

    if ($train) {
      // Get current booking seats and types
      $current_seats = !empty($booking->selected_seats) ? explode(",", $booking->selected_seats) : array();
      $current_types = !empty($booking->seat_type) ? explode(",", $booking->seat_type) : array();
      $train_booked_seats = !empty($train->booked_seats) ? explode(",", $train->booked_seats) : array();

      // Create arrays to store seats to keep and cancel
      $seats_to_keep = array();
      $seats_to_remove = array();
      $double_pairs = array();

      // First pass - identify all double seat pairs
      for ($i = 0; $i < count($current_seats); $i++) {
        if ($current_types[$i] == 'Double') {
          // Look for matching double seat
          for ($j = $i + 1; $j < count($current_seats); $j++) {
            if ($current_types[$j] == 'Double' && abs($current_seats[$i] - $current_seats[$j]) == 1) {
              $double_pairs[$current_seats[$i]] = $current_seats[$j];
              $double_pairs[$current_seats[$j]] = $current_seats[$i];
              break;
            }
          }
        }
      }

      // Second pass - process cancellations
      for ($i = 0; $i < count($current_seats); $i++) {
        $current_seat = $current_seats[$i];

        if (in_array($current_seat, $seats_to_cancel)) {
          // This seat is marked for cancellation
          $seats_to_remove[] = $current_seat;

          // If it's a double seat, also remove its pair
          if ($current_types[$i] == 'Double' && isset($double_pairs[$current_seat])) {
            $seats_to_remove[] = $double_pairs[$current_seat];
          }
        } else if ($current_types[$i] == 'Double' && isset($double_pairs[$current_seat])) {
          // Check if this seat's pair is being cancelled
          if (in_array($double_pairs[$current_seat], $seats_to_cancel)) {
            $seats_to_remove[] = $current_seat;
          } else {
            $seats_to_keep[] = $current_seat;
          }
        } else {
          // Single seat not being cancelled
          $seats_to_keep[] = $current_seat;
        }
      }

      // Remove duplicates
      $seats_to_remove = array_unique($seats_to_remove);
      $seats_to_keep = array_unique($seats_to_keep);
      $seats_to_keep = array_diff($seats_to_keep, $seats_to_remove);

      // Prepare new seat types array
      $new_types = array();
      foreach ($seats_to_keep as $seat) {
        $index = array_search($seat, $current_seats);
        if ($index !== false) {
          $new_types[] = $current_types[$index];
        }
      }

      // Calculate new total cost
      $fare_per_seat = $booking->total_cost / count($current_seats);
      $new_total = $fare_per_seat * count($seats_to_keep);

      // Update train's booked seats
      $new_train_seats = array_values(array_diff($train_booked_seats, $seats_to_remove));
      $new_train_seats_str = !empty($new_train_seats) ? implode(",", $new_train_seats) : '';

      // Update available seats count
      $new_available = $train->available_seats + count($seats_to_remove);

      // Update train seats
      $update_train = "UPDATE obrs_bus SET available_seats=?, booked_seats=? WHERE id=?";
      $stmt = $mysqli->prepare($update_train);
      $stmt->bind_param('isi', $new_available, $new_train_seats_str, $train->id);
      $stmt->execute();

      if (count($seats_to_keep) > 0) {
        // Update booking with remaining seats
        $new_seats_str = implode(",", $seats_to_keep);
        $new_types_str = implode(",", $new_types);
        $remaining_seats = count($seats_to_keep);

        $update = "UPDATE obrs_booking_history SET selected_seats=?, seat_type=?, seats=?, total_cost=? WHERE id=?";
        $stmt = $mysqli->prepare($update);
        $stmt->bind_param('ssidi', $new_seats_str, $new_types_str, $remaining_seats, $new_total, $booking_id);
        $stmt->execute();
      } else {
        // Cancel entire booking if no seats remain
        $update = "UPDATE obrs_booking_history SET status='Cancelled', cancel_date=NOW() WHERE id=?";
        $stmt = $mysqli->prepare($update);
        $stmt->bind_param('i', $booking_id);
        $stmt->execute();
      }

      if ($stmt) {
        $succ = "Selected Seats Successfully Cancelled";
      } else {
        $err = "Please Try Again Later";
      }
    } else {
      $err = "Train not found";
    }
  } else {
    $err = "Invalid booking or already cancelled";
  }
}

if (isset($_POST['Cancel_Booking']) && isset($_POST['booking_id'])) {
  $booking_id = $_POST['booking_id'];

  // Get booking details
  $ret = "SELECT * FROM obrs_booking_history WHERE id=? AND pass_id=? AND status='Active'";
  $stmt = $mysqli->prepare($ret);
  $stmt->bind_param('ii', $booking_id, $aid);
  $stmt->execute();
  $res = $stmt->get_result();
  $booking = $res->fetch_object();

  if ($booking) {
    // Get train details
    $ret = "SELECT id, available_seats, booked_seats FROM obrs_bus WHERE number=?";
    $stmt = $mysqli->prepare($ret);
    $stmt->bind_param('s', $booking->bus_number);
    $stmt->execute();
    $res = $stmt->get_result();
    $train = $res->fetch_object();

    if ($train) {
      // Get seats to cancel
      $cancelled_seats = !empty($booking->selected_seats) ? explode(",", $booking->selected_seats) : array();
      $train_booked_seats = !empty($train->booked_seats) ? explode(",", $train->booked_seats) : array();

      // Remove cancelled seats from train's booked seats
      $new_booked_seats = array_values(array_diff($train_booked_seats, $cancelled_seats));
      $new_booked_seats_str = !empty($new_booked_seats) ? implode(",", $new_booked_seats) : '';

      // Update available seats count
      $new_available = $train->available_seats + count($cancelled_seats);

      // Update train seats
      $update_train = "UPDATE obrs_bus SET available_seats=?, booked_seats=? WHERE id=?";
      $stmt = $mysqli->prepare($update_train);
      $stmt->bind_param('isi', $new_available, $new_booked_seats_str, $train->id);
      $stmt->execute();

      // Update booking status and cancel date
      $update = "UPDATE obrs_booking_history SET status='Cancelled', cancel_date=NOW() WHERE id=?";
      $stmt = $mysqli->prepare($update);
      $stmt->bind_param('i', $booking_id);
      $stmt->execute();

      if ($stmt) {
        $succ = "Booking Successfully Cancelled";
      } else {
        $err = "Please Try Again Later";
      }
    } else {
      $err = "Train not found";
    }
  } else {
    $err = "Invalid booking or already cancelled";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<!--HeAD-->
<?php include('assets/inc/head.php'); ?>
<style>
  .booking-card {
    border-radius: 15px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15); 
    margin-bottom: 25px;
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    background: linear-gradient(to right, #ffffff, #f8f9fa);
    border: 1px solid rgba(0,0,0,0.1);
  }

  .booking-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.25);
  }

  .seat-selector {
    padding: 12px 15px;
    border-radius: 8px;
    border: 2px solid #e0e0e0;
    margin-bottom: 15px;
    font-size: 14px;
    background-color: #fff;
    box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
  }

  .seat-selector:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
  }

  .cancel-btn {
    border-radius: 25px;
    padding: 10px 25px;
    transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    font-size: 13px;
  }

  .cancel-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
  }

  .cancel-btn.btn-warning {
    background: linear-gradient(45deg, #ffc107, #ff9800);
    border: none;
    color: #fff;
  }

  .cancel-btn.btn-danger {
    background: linear-gradient(45deg, #dc3545, #c82333);
    border: none;
  }

  .status-badge {
    padding: 8px 15px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 12px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  }

  .status-active {
    background: linear-gradient(45deg, #28a745, #20c997);
    color: white;
  }

  .status-cancelled {
    background: linear-gradient(45deg, #dc3545, #c82333);
    color: white;
  }

  .seat-info {
    background: linear-gradient(to right, #f8f9fa, #e9ecef);
    padding: 15px;
    border-radius: 10px;
    margin: 10px 0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
  }

  .seat-info p {
    margin-bottom: 8px;
    color: #495057;
    font-size: 14px;
  }

  .seat-info i {
    margin-right: 8px;
    width: 20px;
    text-align: center;
  }

  .badge-info {
    background: linear-gradient(45deg, #17a2b8, #138496);
    padding: 8px 12px;
    border-radius: 15px;
    font-weight: 500;
    font-size: 12px;
    margin: 3px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }

  .card-title {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 20px;
    font-size: 1.25rem;
  }

  .card-title i {
    color: #3498db;
    margin-right: 10px;
  }

  .breadcrumb {
    background: transparent;
    padding: 0;
  }

  .breadcrumb-item a {
    color: #3498db;
    text-decoration: none;
    transition: color 0.3s ease;
  }

  .breadcrumb-item a:hover {
    color: #2980b9;
  }

  .page-head-title {
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 20px;
  }
</style>
<!-- end HEAD-->

<body style="background-color: #F0F0D7;">
  <div class="be-wrapper be-fixed-sidebar">
    <!--navbar-->
    <?php include('assets/inc/navbar.php'); ?>
    <!--End navbar-->
    <!--Sidebar-->
    <?php include('assets/inc/sidebar.php'); ?>
    <!--End Sidebar-->

    <div class="be-content">
      <div class="page-head">
        <h2 class="page-head-title" style="color: black;">The "Thanks But No Thanks" Department</h2>
        <nav aria-label="breadcrumb" role="navigation">
          <ol class="breadcrumb page-head-nav">
            <li class="breadcrumb-item"><a href="#" style="color: black;">BusZy Tickets</a></li>
            <li class="breadcrumb-item active" style="color: black; ">Grab Your Golden Ticket!</li>
            <li class="breadcrumb-item active" style="color: black; text-decoration: underline;">The "Thanks But No Thanks" Department!</li>
          </ol>
        </nav>
      </div>

      <?php if (isset($succ)) { ?>
        <script>
          setTimeout(function() {
              swal({
                title: "Success!",
                text: "<?php echo $succ; ?>!",
                icon: "success",
                button: "Continue",
                timer: 3000
              });
            },
            100);
        </script>
      <?php } ?>

      <?php if (isset($err)) { ?>
        <script>
          setTimeout(function() {
              swal({
                title: "Failed!",
                text: "<?php echo $err; ?>!",
                icon: "error",
                button: "Try Again",
                timer: 3000
              });
            },
            100);
        </script>
      <?php } ?>

      <div class="main-content container-fluid" >
        <div class="row">
          <div class="col-sm-12">
            <?php
            $aid = $_SESSION['pass_id'];
            $ret = "SELECT b.*, p.pass_fname, p.pass_lname, p.pass_email, p.pass_phone, p.pass_addr 
                    FROM obrs_booking_history b
                    JOIN obrs_passenger p ON b.pass_id = p.pass_id 
                    WHERE b.pass_id=? 
                    ORDER BY b.booking_date DESC";
            $stmt = $mysqli->prepare($ret);
            $stmt->bind_param('i', $aid);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_object()) {
              $selected_seats = !empty($row->selected_seats) ? explode(',', $row->selected_seats) : array();
              $seat_types = !empty($row->seat_type) ? explode(',', $row->seat_type) : array();
            ?>
              <div class="booking-card card">
                <div class="card-body p-4">
                  <div class="row">
                    
                    <div class="col-md-4">
                      <h5 class="card-title mb-4" style="font-size: 1.3rem; font-weight: 600; color: #2c3e50;">
                        <i class="fas fa-bus me-2" style="color: #3498db;"></i> 
                        <?php echo $row->bus_name; ?> 
                        <span class="text-muted" style="font-size: 0.9rem;">(<?php echo $row->bus_number; ?>)</span>
                      </h5>
                      <h5 class="card-title mb-4" style="font-size: 1.3rem; font-weight: 600; color: #2c3e50;">
                        <i class="fas fa-ticket-alt me-2" style="color: #3498db;"></i> 
                        <?php echo $row->booking_id; ?> 
                      </h5>
                      <div class="seat-info">
                        <p class="mb-3" style="font-size: 1rem;">
                          <i class="fas fa-map-marker-alt text-danger me-2"></i> 
                          <span style="color: #34495e;">From:</span> 
                          <strong><?php echo $row->dep_station; ?></strong>
                        </p>
                        <p class="mb-3" style="font-size: 1rem;">
                          <i class="fas fa-map-marker-alt text-success me-2"></i>
                          <span style="color: #34495e;">To:</span>
                          <strong><?php echo $row->arr_station; ?></strong>
                        </p>
                        <p class="mb-3" style="font-size: 1rem;">
                          <i class="far fa-clock me-2" style="color: #9b59b6;"></i>
                          <span style="color: #34495e;">Departure:</span>
                          <strong><?php echo date('M d, Y h:i A', strtotime($row->dep_time)); ?></strong>
                        </p>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <h5 class="card-title mb-4" style="font-size: 1.2rem; font-weight: 600; color: #2c3e50;">
                        <i class="fas fa-chair me-2" style="color: #e67e22;"></i> Seat & Passenger Details
                      </h5>
                      <div class="passenger-info mb-3">
                        <p class="mb-2" style="font-size: 0.9rem;">
                          <i class="fas fa-user me-2" style="color: #3498db;"></i>
                          <?php echo $row->pass_fname . ' ' . $row->pass_lname; ?>
                        </p>
                        <p class="mb-2" style="font-size: 0.9rem;">
                          <i class="fas fa-envelope me-2" style="color: #9b59b6;"></i>
                          <?php echo $row->pass_email; ?>
                        </p>
                        <p class="mb-3" style="font-size: 0.9rem;">
                          <i class="fas fa-phone me-2" style="color: #27ae60;"></i>
                          <?php echo $row->pass_phone; ?>
                        </p>
                      </div>
                      <div class="seat-info" style="margin-bottom: 1rem;">
                        <?php
                        for ($i = 0; $i < count($selected_seats); $i++) {
                          if (isset($selected_seats[$i]) && isset($seat_types[$i])) {
                            echo "<span class='badge rounded-pill me-2 mb-2' style='background: linear-gradient(135deg, #3498db, #2980b9); padding: 8px 12px; font-size: 0.9rem;'>
                                    <i class='fas fa-ticket-alt me-1'></i>
                                    Seat {$selected_seats[$i]} ({$seat_types[$i]})
                                  </span>";
                          }
                        }
                        ?>
                      </div>
                      <p class="mt-3" style="font-size: 1.1rem;">
                        <strong style="color: #2c3e50;">Fare:</strong> 
                        <span class="ms-2" style="color: #7f8c8d;">₹<?php echo number_format($row->bus_fare, 2); ?></span>
                      </p>
                      <p class="mt-2" style="font-size: 1.1rem;">
                        <strong style="color: #2c3e50;">Total Cost:</strong> 
                        <span class="ms-2" style="color: #27ae60; font-weight: 600;">₹<?php echo number_format($row->total_cost, 2); ?></span>
                      </p>
                    </div>
                    
                    <div class="col-md-4">
                      <div class="text-end mb-4">
                        <span class="status-badge <?php echo $row->status == 'Active' ? 'status-active' : 'status-cancelled'; ?>"
                              style="padding: 8px 15px; border-radius: 20px; font-weight: 600; <?php echo $row->status == 'Active' ? 'background: linear-gradient(135deg, #2ecc71, #27ae60); color: white;' : 'background: linear-gradient(135deg, #e74c3c, #c0392b); color: white;'; ?>">
                          <i class="fas <?php echo $row->status == 'Active' ? 'fa-check-circle' : 'fa-times-circle'; ?> me-1"></i>
                          <?php echo $row->status; ?>
                        </span>
                      </div>
                      <?php if ($row->status == 'Active'): ?>
                        <form method="POST" class="mt-4">
                          <input type="hidden" name="booking_id" value="<?php echo $row->id; ?>">
                          <div class="form-group mb-3">
                            <select name="seats_to_cancel[]" class="form-control seat-selector" multiple data-placeholder="Select seats to cancel" style="border-radius: 10px;">
                              <?php foreach ($selected_seats as $i => $seat): ?>
                                <?php if (isset($seat_types[$i])): ?>
                                  <option value="<?php echo $seat; ?>">Seat <?php echo $seat; ?> (<?php echo $seat_types[$i]; ?>)</option>
                                <?php endif; ?>
                              <?php endforeach; ?>
                            </select>
                          </div>
                          <button type="submit" name="Cancel_Seats" class="btn btn-warning cancel-btn mb-3 w-100" style="background: linear-gradient(135deg, #f39c12, #e67e22); border: none;">
                            <i class="fas fa-times-circle me-2"></i> Cancel Selected Seats
                          </button>
                          <button type="submit" name="Cancel_Booking" class="btn btn-danger cancel-btn w-100" 
                                  style="background: linear-gradient(135deg, #e74c3c, #c0392b); border: none;"
                                  onclick="return confirm('Are you sure you want to cancel the entire booking? This action cannot be undone.')">
                            <i class="fas fa-ban me-2"></i> Cancel Entire Booking
                          </button>
                        </form>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            <?php } ?>
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
  <script src="https://kit.fontawesome.com/f766ed9c4c.js" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      App.init();

      // Enhanced Select2 initialization with custom styling
      $('.seat-selector').select2({
        placeholder: 'Select seats to cancel',
        allowClear: true,
        theme: 'classic',
        width: '100%',
        containerCssClass: 'select2-custom',
        dropdownCssClass: 'select2-custom',
      });

      // Add smooth scrolling
      $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({
          scrollTop: $($(this).attr('href')).offset().top
        }, 500, 'linear');
      });

      // Add hover effects
      $('.booking-card').hover(
        function() {
          $(this).find('.cancel-btn').addClass('pulse');
        },
        function() {
          $(this).find('.cancel-btn').removeClass('pulse');
        }
      );
    });
  </script>
</body>

</html>