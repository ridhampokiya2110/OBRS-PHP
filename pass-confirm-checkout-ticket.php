<?php
    session_start();
    include('assets/inc/config.php');
    include('assets/inc/checklogin.php');
    check_login();
    $aid=$_SESSION['pass_id'];

    // Get booking details from booking history
    if(!isset($_GET['booking_id'])) {
        header("Location: pass-dashboard.php"); 
        exit();
    }
    $booking_id = $_GET['booking_id'];

    // Get booking and passenger details from booking history database
    $query = "SELECT b.*, p.pass_fname, p.pass_lname, p.pass_email, p.pass_addr,
              b.dep_station, b.arr_station,
              p.pass_fare_payment_code, p.pass_bus_number, p.seats,
              t.name as bus_name, t.number as train_no
              FROM obrs_booking_history b
              JOIN obrs_passenger p ON b.pass_id = p.pass_id 
              JOIN obrs_bus t ON b.bus_number = t.number
              WHERE b.id=? AND b.pass_id=? AND b.payment_status='Pending'";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ii', $booking_id, $aid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows == 0) {
        header("Location: pass-dashboard.php");
        exit();
    }
    
    $booking = $result->fetch_object();

    // Calculate total cost
    $bus_fare = $booking->bus_fare;
    $num_seats = $booking->seats;
    $total_cost = $bus_fare * $num_seats;

    if(isset($_POST['bus_fare_confirm_checkout']))
    {
            // Get data from booking history
            $pass_name = $booking->pass_fname . ' ' . $booking->pass_lname;
            $pass_addr = $booking->pass_addr;
            $pass_email = $booking->pass_email;        
            $bus_name = $booking->bus_name;
            $train_no = $booking->train_no;
            $train_dep_stat = $booking->dep_station;
            $train_arr_stat = $booking->arr_station;
            $bus_fare = $total_cost; // Use total cost instead of per-seat fare
            $fare_payment_code = $booking->pass_fare_payment_code;
            $payment_method = $_POST['payment_method'];
            $payment_status = 'Confirmed';
            
            $payment_success = true;
            
            if($payment_method == 'credit_card' || $payment_method == 'debit_card') {
                // Card validation
                $card_number = preg_replace('/\s+/', '', $_POST['card_number']);
                $card_expiry = $_POST['card_expiry'];
                $card_cvv = $_POST['card_cvv'];
                
                if(strlen($card_number) != 16 || !is_numeric($card_number)) {
                    $err = "Invalid card number";
                    $payment_success = false;
                }
                
                if(!preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $card_expiry)) {
                    $err = "Invalid expiry date format (MM/YY)";
                    $payment_success = false;
                } else {
                    list($exp_month, $exp_year) = explode('/', $card_expiry);
                    $exp_date = \DateTime::createFromFormat('y-m-d', $exp_year . '-' . $exp_month . '-01');
                    $now = new \DateTime();
                    
                    if($exp_date < $now) {
                        $err = "Card has expired";
                        $payment_success = false;
                    }
                }
                
                if(strlen($card_cvv) != 3 || !is_numeric($card_cvv)) {
                    $err = "Invalid CVV";
                    $payment_success = false;
                }
            }
            else if($payment_method == 'paypal') {
                // Generate QR code data from booking details
                $qr_data = array(
                    'amount' => $bus_fare,
                    'payment_code' => $fare_payment_code,
                    'merchant' => 'Railway Booking System',
                    'description' => "Train ticket from $train_dep_stat to $train_arr_stat"
                );
                
                $qr_json = json_encode($qr_data);
                
                // Store QR data in session for display
                $_SESSION['paypal_qr_data'] = $qr_json;
                
                // Don't process payment yet - wait for QR scan
                $payment_success = false;
                $info = "Please scan QR code to complete PayPal payment";
            }
            
            if($payment_success) {
                try {
                    $mysqli->begin_transaction();

                    // Update payment status in booking history database
                    $query1="UPDATE obrs_booking_history SET payment_status=? WHERE id=?";
                    $stmt1 = $mysqli->prepare($query1);
                    $stmt1->bind_param('si', $payment_status, $booking_id);
                    $stmt1->execute();

                    // Insert ticket record into database
                    $query2="INSERT INTO obrs_bus_tickets (pass_name, pass_addr, pass_email, bus_name, train_no, train_dep_stat, train_arr_stat, bus_fare, fare_payment_code, confirmation_code, payment_method, booking_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
                    $stmt2 = $mysqli->prepare($query2);
                    $confirmation = substr(md5(uniqid(rand(), true)), 0, 10);
                    $stmt2->bind_param('sssssssssssi', $pass_name, $pass_addr, $pass_email, $bus_name, $train_no, $train_dep_stat, $train_arr_stat, $bus_fare, $fare_payment_code, $confirmation, $payment_method, $booking_id);
                    $stmt2->execute();

                    $mysqli->commit();
                    $succ = "Payment Confirmed and Ticket Generated. Your confirmation code is: $confirmation";
                    
                    // Clear PayPal QR session data if exists
                    if(isset($_SESSION['paypal_qr_data'])) {
                        unset($_SESSION['paypal_qr_data']);
                    }

                    // Redirect to ticket view after 3 seconds
                    header("refresh:3;url=pass-print-ticket.php?ticket=$confirmation");

                } catch (Exception $e) {
                    $mysqli->rollback();
                    $err = "Transaction failed. Please try again later.";
                }
            }
    }
?>
<!DOCTYPE html>
<html lang="en">
<!--Head-->
<?php include('assets/inc/head.php');?>
<!--End Head-->
  <body>
    <div class="be-wrapper be-fixed-sidebar">
    <!--Navigation Bar-->
      <?php include('assets/inc/navbar.php');?>
      <!--End Navigation Bar-->

      <!--Sidebar-->
      <?php include('assets/inc/sidebar.php');?>
      <!--End Sidebar-->
      <div class="be-content">
        <div class="page-head">
          <h2 class="page-head-title">Train Ticket Checkout</h2>
          <nav aria-label="breadcrumb" role="navigation">
            <ol class="breadcrumb page-head-nav">
              <li class="breadcrumb-item"><a href="pass-dashboard.php">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="#">Tickets</a></li>
              <li class="breadcrumb-item active">Checkout</li>
            </ol>
          </nav>
        </div>
            <?php if(isset($succ)) {?>
                <script>
                    setTimeout(function () 
                    { 
                        swal("Success!","<?php echo $succ;?>","success");
                    },
                        100);
                </script>
            <?php } ?>
            <?php if(isset($err)) {?>
                <script>
                    setTimeout(function () 
                    { 
                        swal("Failed!","<?php echo $err;?>","error");
                    },
                        100);
                </script>
            <?php } ?>
            <?php if(isset($info)) {?>
                <script>
                    setTimeout(function () 
                    { 
                        swal("Info","<?php echo $info;?>","info");
                    },
                        100);
                </script>
            <?php } ?>
        <div class="main-content container-fluid">
          <div class="row">
            <div class="col-md-12">
              <div class="card card-border-color card-border-color-success">
                <div class="card-header card-header-divider">
                    <h3 class="card-title">Payment Details</h3>
                    <span class="card-subtitle">Please review your booking details and complete payment</span>
                </div>
                <div class="card-body">
                  <form method="POST" class="form-horizontal">
                    <div class="form-group row">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3">Name</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" readonly name="pass_name" value="<?php echo htmlspecialchars($booking->pass_fname . ' ' . $booking->pass_lname);?>" id="inputText3" type="text">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3">Email</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" readonly name="pass_email" value="<?php echo htmlspecialchars($booking->pass_email);?>" id="inputText3" type="text">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3">Address</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" readonly name="pass_addr" value="<?php echo htmlspecialchars($booking->pass_addr);?>" id="inputText3" type="text">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3">Train Number</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" readonly name="train_no" value="<?php echo htmlspecialchars($booking->train_no);?>" id="inputText3" type="text">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3">Train Name</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" readonly name="bus_name" value="<?php echo htmlspecialchars($booking->bus_name);?>" id="inputText3" type="text">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3">Departure</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" readonly name="train_dep_stat" value="<?php echo htmlspecialchars($booking->dep_station);?>" id="inputText3" type="text">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3">Arrival</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" readonly name="train_arr_stat" value="<?php echo htmlspecialchars($booking->arr_station);?>" id="inputText3" type="text">
                      </div>
                    </div>                   
                    <div class="form-group row">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3">Fare Per Seat</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" readonly name="bus_fare" value="<?php echo htmlspecialchars($booking->bus_fare);?>" id="inputText3" type="text">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3">Number of Seats</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" readonly name="num_seats" value="<?php echo htmlspecialchars($booking->seats);?>" id="inputText3" type="text">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3">Total Cost</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" readonly name="total_cost" value="<?php echo htmlspecialchars($total_cost);?>" id="inputText3" type="text">
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3">Payment Code</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" readonly name="fare_payment_code" value="<?php echo htmlspecialchars($booking->pass_fare_payment_code);?>" id="inputText3" type="text">
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right">Payment Method</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <select class="form-control" name="payment_method" id="payment_method">
                          <option value="credit_card">Credit Card</option>
                          <option value="debit_card">Debit Card</option>
                          <option value="paypal">PayPal</option>
                        </select>
                      </div>
                    </div>

                    <div id="credit_card_fields">
                      <div class="form-group row">
                        <label class="col-12 col-sm-3 col-form-label text-sm-right">Card Number</label>
                        <div class="col-12 col-sm-8 col-lg-6">
                          <input class="form-control" name="card_number" type="text" placeholder="1234 5678 9012 3456" maxlength="19" pattern="\d{4}\s?\d{4}\s?\d{4}\s?\d{4}">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label class="col-12 col-sm-3 col-form-label text-sm-right">Expiry Date</label>
                        <div class="col-12 col-sm-8 col-lg-6">
                          <input class="form-control" name="card_expiry" type="text" placeholder="MM/YY" maxlength="5" pattern="(0[1-9]|1[0-2])\/([0-9]{2})">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label class="col-12 col-sm-3 col-form-label text-sm-right">CVV</label>
                        <div class="col-12 col-sm-8 col-lg-6">
                          <input class="form-control" name="card_cvv" type="password" placeholder="123" maxlength="3" pattern="\d{3}">
                        </div>
                      </div>
                    </div>

                    <div id="paypal_fields" style="display:none;">
                      <div class="form-group row">
                        <label class="col-12 col-sm-3 col-form-label text-sm-right">PayPal QR Code</label>
                        <div class="col-12 col-sm-8 col-lg-6">
                          <?php if(isset($_SESSION['paypal_qr_data'])): ?>
                            <div id="qrcode"></div>
                            <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
                            <script>
                              var qrcode = new QRCode(document.getElementById("qrcode"), {
                                text: <?php echo json_encode($_SESSION['paypal_qr_data']); ?>,
                                width: 200,
                                height: 200
                              });
                            </script>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>

                    <div class="row pt-3">
                      <div class="col-sm-12 text-right">
                        <a href="pass-dashboard.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-space btn-success" name="bus_fare_confirm_checkout">
                          <i class="icon icon-check"></i> Confirm Payment
                        </button>
                      </div>
                    </div>
                  </form>
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
    <script src="assets/lib/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
    <script src="assets/lib/jquery.nestable/jquery.nestable.js" type="text/javascript"></script>
    <script src="assets/lib/moment.js/min/moment.min.js" type="text/javascript"></script>
    <script src="assets/lib/datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="assets/lib/select2/js/select2.min.js" type="text/javascript"></script>
    <script src="assets/lib/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script src="assets/lib/bootstrap-slider/bootstrap-slider.min.js" type="text/javascript"></script>
    <script src="assets/lib/bs-custom-file-input/bs-custom-file-input.js" type="text/javascript"></script>
    <script type="text/javascript">
      $(document).ready(function(){
        App.init();
        App.formElements();

        // Format card number input with spaces
        $('input[name="card_number"]').on('input', function() {
          $(this).val($(this).val().replace(/[^\d]/g, '').replace(/(.{4})/g, '$1 ').trim());
        });

        // Format expiry date input
        $('input[name="card_expiry"]').on('input', function() {
          let val = $(this).val().replace(/\D/g, '');
          if (val.length >= 2) {
            val = val.substring(0,2) + '/' + val.substring(2);
          }
          $(this).val(val);
        });

        // Show/hide payment fields based on selected method
        $('#payment_method').change(function() {
          if($(this).val() == 'credit_card' || $(this).val() == 'debit_card') {
            $('#credit_card_fields').show();
            $('#paypal_fields').hide();
          } else if($(this).val() == 'paypal') {
            $('#credit_card_fields').hide();
            $('#paypal_fields').show();
          }
        });
      });
    </script>
  </body>
</html>