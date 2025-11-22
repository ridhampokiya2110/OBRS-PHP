<?php
session_start();
include('assets/inc/config.php');
include('assets/inc/checklogin.php');
check_login();
$aid=$_SESSION['pass_id'];

// Get booking ID from URL
$booking_id = isset($_GET['id']) ? $_GET['id'] : null;

if(!$booking_id) {
  header("Location: pass-bus-checkout-ticket.php");
  exit();
}

// Get booking details from booking history
$query = "SELECT b.* FROM obrs_booking_history b 
          WHERE b.id=? AND b.pass_id=? AND b.payment_status='Pending'";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('ii', $booking_id, $aid);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_object();

if(!$booking) {
  header("Location: pass-bus-checkout-ticket.php");
  exit();
}

// Process payment if form submitted
if(isset($_POST['pay_now'])) {
  if(!isset($_POST['payment_method'])) {
    $error = "Please select a payment method";
  } else {
    $payment_method = $_POST['payment_method'];
    // Store payment method in session
    $_SESSION['payment_method'] = $payment_method;
    $_SESSION['booking_id'] = $booking_id;
    
    // Redirect to payment gateway
    header("Location: payment-gateway.php");
    exit();
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<!--HeAD-->
<?php include('assets/inc/head.php');?>
<!-- end HEAD-->
<body style="background-color: #F0F0D7;">
  <div class="be-wrapper be-fixed-sidebar">
    <!--Navigation Bar-->
    <?php include('assets/inc/navbar.php');?>
    <!--End Navigation Bar-->
    <!--Sidebar-->
    <?php include('assets/inc/sidebar.php');?>
    <!--End Sidebar-->

    <div class="be-content">
      <div class="page-head">
        <h2 class="page-head-title" style="color: black;">Take My Money! : BusZy Tickets</h2>
        <nav aria-label="breadcrumb" role="navigation">
          <ol class="breadcrumb page-head-nav">
            <li class="breadcrumb-item"><a href="#" style="color: black;">BusZy Tickets</a></li>
            <li class="breadcrumb-item"><a href="pass-bus-checkout-ticket.php" style="color: black;">Review Booking</a></li>
            <li class="breadcrumb-item active" style="color: black; text-decoration: underline;">Take My Money! : BusZy Tickets</li>
          </ol>
        </nav>
      </div>

      <div class="main-content container-fluid">
        <div class="row">
          <div class="col-md-12">
            <div class="card" style="background: #D8E4F8; border-radius: 20px; box-shadow: 0 8px 32px rgba(0,0,0,0.15), 0 4px 8px rgba(0,0,0,0.1); border: 1px solid rgba(0,0,0,0.05);">
              <div class="card-header card-header-divider" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-bottom: 2px solid rgba(0,0,0,0.05); padding: 25px; border-radius: 20px 20px 0 0;">
                <span style="color: #2C3E50; font-size: 1.4em; font-weight: 600; letter-spacing: 0.5px;" class="card-subtitle">Review Your Booking Details</span>
              </div>
              <div class="card-body" style="padding: 40px;">
                <?php if(isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 12px; border: none;">
                  <?php echo $error; ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <div class="row">
                  
                  <div class="col-md-6">
                    <table class="table" style="background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(255,255,255,0.85)); border-radius: 15px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                      <tr>
                        <th class="text-primary py-3" style="border-top: none; font-size: 1.1em; width: 40%; background: rgba(13,110,253,0.03);">
                          <i class="fas fa-bookmark me-2" style="color: #4e73df;"></i>
                          <span style="color: #2C3E50; font-weight: 600;">Booking ID:</span>
                        </th>
                        <td class="py-3" style="border-top: none; font-size: 1.1em; color: #2C3E50;">
                          <span class="badge bg-success" style="font-size: 0.9em; padding: 8px 12px; border-radius: 8px; color: white;">
                            <?php echo $booking->booking_id; ?>
                          </span>
                        </td>
                      </tr>
                      <tr>
                        <th class="text-primary py-3" style="font-size: 1.1em; background: rgba(13,110,253,0.03);">
                          <i class="fas fa-bus me-2" style="color: #4e73df;"></i>
                          <span style="color: #2C3E50; font-weight: 600;">Bus Number:</span>
                        </th>
                        <td class="py-3" style="font-size: 1.1em; color: #2C3E50;"><?php echo $booking->bus_number; ?></td>
                      </tr>
                      <tr>
                        <th class="text-primary py-3" style="font-size: 1.1em; background: rgba(13,110,253,0.03);">
                          <i class="fas fa-bus-alt me-2" style="color: #4e73df;"></i>
                          <span style="color: #2C3E50; font-weight: 600;">Bus Name:</span>
                        </th>
                        <td class="py-3" style="font-size: 1.1em; color: #2C3E50;"><?php echo $booking->bus_name; ?></td>
                      </tr>
                      <tr>
                        <th class="text-primary py-3" style="font-size: 1.1em; background: rgba(13,110,253,0.03);">
                          <i class="fas fa-map-marker-alt me-2" style="color: #4e73df;"></i>
                          <span style="color: #2C3E50; font-weight: 600;">From:</span>
                        </th>
                        <td class="py-3" style="font-size: 1.1em; color: #2C3E50;"><?php echo $booking->dep_station; ?></td>
                      </tr>
                      <tr>
                        <th class="text-primary py-3" style="font-size: 1.1em; background: rgba(13,110,253,0.03);">
                          <i class="fas fa-map-marker me-2" style="color: #4e73df;"></i>
                          <span style="color: #2C3E50; font-weight: 600;">To:</span>
                        </th>
                        <td class="py-3" style="font-size: 1.1em; color: #2C3E50;"><?php echo $booking->arr_station; ?></td>
                      </tr>
                    </table>
                  </div>

                  <div class="col-md-6">
                    <table class="table" style="background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(255,255,255,0.85)); border-radius: 15px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                      <tr>
                        <th class="text-primary py-3" style="border-top: none; font-size: 1.1em; width: 40%; background: rgba(13,110,253,0.03);">
                          <i class="fas fa-clock me-2" style="color: #4e73df;"></i>
                          <span style="color: #2C3E50; font-weight: 600;">Departure:</span>
                        </th>
                        <td class="py-3" style="border-top: none; font-size: 1.1em; color: #2C3E50;">
                          <?php echo $booking->dep_time; ?>
                        </td>
                      </tr>
                      <tr>
                        <th class="text-primary py-3" style="font-size: 1.1em; background: rgba(13,110,253,0.03);">
                          <i class="fas fa-chair me-2" style="color: #4e73df;"></i>
                          <span style="color: #2C3E50; font-weight: 600;">Seats:</span>
                        </th>
                        <td class="py-3" style="font-size: 1.1em; color: #2C3E50;">
                          <?php echo $booking->selected_seats; ?>
                        </td>
                      </tr>
                      <tr>
                        <th class="text-primary py-3" style="font-size: 1.1em; background: rgba(13,110,253,0.03);">
                          <i class="fas fa-couch me-2" style="color: #4e73df;"></i>
                          <span style="color: #2C3E50; font-weight: 600;">Type:</span>
                        </th>
                        <td class="py-3" style="font-size: 1.1em; color: #2C3E50;">
                          <?php echo $booking->seat_type; ?>
                        </td>
                      </tr>
                      <tr>
                        <th class="text-primary py-3" style="font-size: 1.1em; background: rgba(13,110,253,0.03);">
                          <i class="fas fa-dollar-sign me-2" style="color: #4e73df;"></i>
                          <span style="color: #2C3E50; font-weight: 600;">Total:</span>
                        </th>
                        <td class="py-3" style="font-size: 1.1em;">
                          <span class="badge bg-success" style="font-size: 1em; padding: 8px 12px; border-radius: 8px; color: white;">
                          ₹<?php echo $booking->total_cost; ?>
                          </span>
                        </td>
                      </tr>
                    </table>
                  </div>
                </div>

                <div class="row mt-4">
                  <div class="col-md-12">
                    <h4 class="mb-4 text-center" style="color: #2C3E50; font-weight: 600;">Select Payment Method</h4>
                    
                    <form method="POST" action="" id="payment-form">
                      <div class="row justify-content-center">
                        <div class="col-md-3">
                          <div class="card payment-card mb-3" style="background: rgba(255,255,255,0.95); border-radius: 15px; transition: all 0.3s ease; border: 2px solid transparent;">
                            <div class="card-body text-center py-4">
                              <input type="radio" name="payment_method" value="credit" id="credit" class="d-none" required>
                              <label for="credit" class="d-block cursor-pointer mb-0">
                                <i class="fas fa-credit-card fa-3x mb-3 text-primary"></i>
                                <h5 class="mb-0 font-weight-bold">Credit Card</h5>
                              </label>
                            </div>
                          </div>
                        </div>
                        
                        <div class="col-md-3">
                          <div class="card payment-card mb-3" style="background: rgba(255,255,255,0.95); border-radius: 15px; transition: all 0.3s ease; border: 2px solid transparent;">
                            <div class="card-body text-center py-4">
                              <input type="radio" name="payment_method" value="debit" id="debit" class="d-none">
                              <label for="debit" class="d-block cursor-pointer mb-0">
                                <i class="fas fa-money-check fa-3x mb-3 text-success"></i>
                                <h5 class="mb-0 font-weight-bold">Debit Card</h5>
                              </label>
                            </div>
                          </div>
                        </div>
                        
                        <div class="col-md-3">
                          <div class="card payment-card mb-3" style="background: rgba(255,255,255,0.95); border-radius: 15px; transition: all 0.3s ease; border: 2px solid transparent;">
                            <div class="card-body text-center py-4">
                              <input type="radio" name="payment_method" value="paypal" id="paypal" class="d-none">
                              <label for="paypal" class="d-block cursor-pointer mb-0">
                                <i class="fab fa-paypal fa-3x mb-3 text-info"></i>
                                <h5 class="mb-0 font-weight-bold">PayPal</h5>
                              </label>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="text-center mt-5">
                        <button type="submit" name="pay_now" class="btn btn-success btn-lg me-5" style="border-radius: 50px; padding: 12px 40px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2); transition: all 0.3s ease;">
                          <i class="fas fa-credit-card me-2"></i>Pay Now
                        </button>
                        
                        <a href="pass-bus-checkout-ticket.php" class="btn btn-danger btn-lg ms-5" style="border-radius: 50px; padding: 12px 40px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2); transition: all 0.3s ease;">
                          <i class="fas fa-times me-2"></i>Cancel
                        </a>
                      </div>
                    </form>

                  </div>
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
  </div>

  <script src="assets/lib/jquery/jquery.min.js" type="text/javascript"></script>
  <script src="assets/lib/perfect-scrollbar/js/perfect-scrollbar.min.js" type="text/javascript"></script>
  <script src="assets/lib/bootstrap/dist/js/bootstrap.bundle.min.js" type="text/javascript"></script>
  <script src="assets/js/app.js" type="text/javascript"></script>
  <script type="text/javascript">
    $(document).ready(function(){
      App.init();

      // Add hover and selection effects to payment method cards
      $('.payment-card').hover(
        function() {
          if (!$(this).find('input[type="radio"]').prop('checked')) {
            $(this).css('transform', 'translateY(-5px)').css('box-shadow', '0 8px 15px rgba(0,0,0,0.1)');
          }
        }, function() {
          if (!$(this).find('input[type="radio"]').prop('checked')) {
            $(this).css('transform', 'translateY(0)').css('box-shadow', 'none');
          }
        }
      );

      // Handle payment method selection
      $('input[name="payment_method"]').change(function() {
        // Remove highlight from all cards
        $('.payment-card').css({
          'transform': 'translateY(0)',
          'box-shadow': 'none',
          'border-color': 'transparent',
          'background': 'rgba(255,255,255,0.95)'
        });

        // Add highlight to selected card
        $(this).closest('.payment-card').css({
          'transform': 'translateY(-5px)',
          'box-shadow': '0 8px 20px rgba(0,0,0,0.15)',
          'border-color': '#4e73df',
          'background': 'rgba(78,115,223,0.1)'
        });
      });

      // Form validation
      $('#payment-form').submit(function(e) {
        if (!$('input[name="payment_method"]:checked').val()) {
          e.preventDefault();
          alert('Please select a payment method');
          return false;
        }
      });
    });
  </script>
</body>
</html>