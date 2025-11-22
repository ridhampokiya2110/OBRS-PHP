<?php
session_start();
include('assets/inc/config.php');
include('assets/inc/checklogin.php');
check_login();
$aid = $_SESSION['pass_id'];

// Check if payment method and booking ID are set in session
if (!isset($_SESSION['payment_method']) || !isset($_SESSION['booking_id'])) {
  header("Location: pass-bus-checkout-ticket.php");
  exit();
}

$payment_method = $_SESSION['payment_method'];
$booking_id = $_SESSION['booking_id'];

// Get booking details
$query = "SELECT * FROM obrs_booking_history WHERE id=? AND pass_id=? AND payment_status='Pending'";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('ii', $booking_id, $aid);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_object();

if (!$booking) {
  header("Location: pass-bus-checkout-ticket.php");
  exit();
}

// Process payment form submission
if (isset($_POST['confirm_payment'])) {
  $errors = [];
  
  if($payment_method == 'credit' || $payment_method == 'debit') {
    $card_number = $_POST['card_number'] ?? '';
    $card_name = $_POST['card_name'] ?? '';
    $expiry = $_POST['expiry'] ?? '';
    $cvv = $_POST['cvv'] ?? '';
    
    // Basic validation for card payments
    if (empty($card_number) || strlen($card_number) < 16) {
      $errors[] = "Invalid card number";
    }
    if (empty($card_name)) {
      $errors[] = "Card holder name is required";
    }
    if (empty($expiry) || !preg_match('/^\d{2}\/\d{2}$/', $expiry)) {
      $errors[] = "Invalid expiry date";
    }
    if (empty($cvv) || strlen($cvv) < 3) {
      $errors[] = "Invalid CVV";
    }
  }
  else if($payment_method == 'paypal') {
    $paypal_email = $_POST['paypal_email'] ?? '';
    $paypal_password = $_POST['paypal_password'] ?? '';
    
    // Basic validation for PayPal
    if(empty($paypal_email) || !filter_var($paypal_email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = "Invalid PayPal email";
    }
    if(empty($paypal_password)) {
      $errors[] = "PayPal password is required";
    }
  }

  if (empty($errors)) {
    // Update booking status to paid and add payment method
    $update = "UPDATE obrs_booking_history SET payment_status='Paid', payment_method=? WHERE id=?";
    $stmt = $mysqli->prepare($update);
    $stmt->bind_param('si', $payment_method, $booking_id);
    
    if ($stmt->execute()) {
      // Clear payment session data
      unset($_SESSION['payment_method']);
      unset($_SESSION['booking_id']);
      
      // Redirect to success page
      header("Location: pass-print-ticket.php?id=" . $booking_id);
      exit();
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<!--HeAD-->
<?php include('assets/inc/head.php');?>
<!--end HEAD-->

<body style="background-color: #F0F0D7;">
  <div class="be-wrapper be-fixed-sidebar">
    <!--Navigation bar-->
    <?php include('assets/inc/navbar.php');?>
    <!--Navigation-->

    <!--Sidebar-->
    <?php include('assets/inc/sidebar.php');?>
    <!--Sidebar-->

    <div class="be-content">
      <div class="page-head">
        <h2 class="page-head-title" style="color: black;">Payment Gateway</h2>
        <nav aria-label="breadcrumb" role="navigation">
          <ol class="breadcrumb page-head-nav">
            <li class="breadcrumb-item"><a href="#" style="color: black;">BusZy Tickets</a></li>
            <li class="breadcrumb-item"><a href="pass-confirm-ticket.php" style="color: black;">Review Booking</a></li>
            <li class="breadcrumb-item active" style="color: black; text-decoration: underline;">Payment Gateway</li>
          </ol>
        </nav>
      </div>

      <div class="main-content container-fluid">
        <div class="row">
          <div class="col-md-12">
            <div class="card" style="background: #D8E4F8; border-radius: 20px; box-shadow: 0 8px 32px rgba(0,0,0,0.15), 0 4px 8px rgba(0,0,0,0.1); border: 1px solid rgba(0,0,0,0.05);">
              <div class="card-header card-header-divider" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-bottom: 2px solid rgba(0,0,0,0.05); padding: 25px; border-radius: 20px 20px 0 0;">
                <span style="color: #2C3E50; font-size: 1.4em; font-weight: 600; letter-spacing: 0.5px;" class="card-subtitle">Enter Payment Details</span>
              </div>
              <div class="card-body" style="padding: 40px;">
                <?php if(!empty($errors)): ?>
                  <div class="alert alert-danger">
                    <ul class="mb-0">
                      <?php foreach($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                      <?php endforeach; ?>
                    </ul>
                  </div>
                <?php endif; ?>

                <div class="row mb-4">
                  <div class="col-md-12">
                    <div class="payment-info p-4 rounded" style="background: linear-gradient(135deg, rgba(255,255,255,0.98), rgba(255,255,255,0.92)); border-radius: 18px !important; box-shadow: 0 8px 20px rgba(0,0,0,0.08); border: 1px solid rgba(0,0,0,0.05);">
                      <h5 style="color: #2C3E50; font-weight: 700; margin-bottom: 1.2rem; font-size: 1.25rem;">Booking Summary</h5>
                      <div class="d-flex justify-content-between align-items-center mb-3">
                        <p class="mb-0" style="color: #4a5568; font-size: 1.1rem;">Amount to Pay:</p>
                        <span class="badge bg-success" style="font-size: 1.1rem; padding: 10px 16px; color: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(40, 167, 69, 0.2);">₹<?php echo number_format($booking->total_cost, 2); ?></span>
                      </div>
                      <div class="d-flex justify-content-between align-items-center">
                        <p class="mb-0" style="color: #4a5568; font-size: 1.1rem;">Payment Method:</p>
                        <span class="badge bg-primary" style="font-size: 1.1rem; padding: 10px 16px; color: white; border-radius: 10px; box-shadow: 0 2px 8px rgba(0, 123, 255, 0.2);"><?php echo ucfirst($payment_method); ?></span>
                      </div>
                    </div>
                  </div>
                </div>

                <form method="POST" id="payment-form">
                  <?php if($payment_method == 'credit' || $payment_method == 'debit'): ?>
                    <div class="row">
                      <div class="col-md-12 mb-3">
                        <label for="card_number" style="color: #2C3E50; font-weight: 600;">Card Number</label>
                        <input type="text" class="form-control" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" maxlength="16" required style="border-radius: 10px; padding: 12px; border: 2px solid rgba(0,0,0,0.1);">
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-12 mb-3">
                        <label for="card_name" style="color: #2C3E50; font-weight: 600;">Card Holder Name</label>
                        <input type="text" class="form-control" id="card_name" name="card_name" placeholder="John Doe" required style="border-radius: 10px; padding: 12px; border: 2px solid rgba(0,0,0,0.1);">
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="expiry" style="color: #2C3E50; font-weight: 600;">Expiry Date</label>
                        <input type="text" class="form-control" id="expiry" name="expiry" placeholder="MM/YY" maxlength="5" required style="border-radius: 10px; padding: 12px; border: 2px solid rgba(0,0,0,0.1);">
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="cvv" style="color: #2C3E50; font-weight: 600;">CVV</label>
                        <input type="password" class="form-control" id="cvv" name="cvv" placeholder="123" maxlength="3" required style="border-radius: 10px; padding: 12px; border: 2px solid rgba(0,0,0,0.1);">
                      </div>
                    </div>
                  <?php elseif($payment_method == 'paypal'): ?>
                    <div class="row">
                      <div class="col-md-12 mb-3">
                        <label for="paypal_email" style="color: #2C3E50; font-weight: 600;">PayPal Email</label>
                        <input type="email" class="form-control" id="paypal_email" name="paypal_email" placeholder="your@email.com" required style="border-radius: 10px; padding: 12px; border: 2px solid rgba(0,0,0,0.1);">
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12 mb-3">
                        <label for="paypal_password" style="color: #2C3E50; font-weight: 600;">PayPal Password</label>
                        <input type="password" class="form-control" id="paypal_password" name="paypal_password" required style="border-radius: 10px; padding: 12px; border: 2px solid rgba(0,0,0,0.1);">
                      </div>
                    </div>
                  <?php endif; ?>

                  <div class="text-center mt-5">
                    <button type="submit" name="confirm_payment" class="btn btn-success btn-lg me-5" style="border-radius: 50px; padding: 12px 40px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2); transition: all 0.3s ease;">
                      <i class="fas fa-credit-card me-2"></i>Confirm Payment
                    </button>
                    <a href="pass-confirm-ticket.php?id=<?php echo $booking_id; ?>" class="btn btn-danger btn-lg ms-5" style="border-radius: 50px; padding: 12px 40px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2); transition: all 0.3s ease;">
                      <i class="fas fa-times me-2"></i>Cancel
                    </a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--footer-->
      <?php include('assets/inc/footer.php');?>
      <!--EndFooter-->
    </div>
  </div>

  <script src="assets/lib/jquery/jquery.min.js" type="text/javascript"></script>
  <script src="assets/lib/perfect-scrollbar/js/perfect-scrollbar.min.js" type="text/javascript"></script>
  <script src="assets/lib/bootstrap/dist/js/bootstrap.bundle.min.js" type="text/javascript"></script>
  <script src="assets/js/app.js" type="text/javascript"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      App.init();

      <?php if($payment_method == 'credit' || $payment_method == 'debit'): ?>
        // Format card number input
        $('#card_number').on('input', function() {
          $(this).val($(this).val().replace(/\D/g, ''));
        });

        // Format expiry date input
        $('#expiry').on('input', function() {
          var val = $(this).val().replace(/\D/g, '');
          if (val.length > 2) {
            val = val.slice(0,2) + '/' + val.slice(2);
          }
          $(this).val(val);
        });

        // Format CVV input
        $('#cvv').on('input', function() {
          $(this).val($(this).val().replace(/\D/g, ''));
        });
      <?php endif; ?>
    });
  </script>
</body>
</html>
