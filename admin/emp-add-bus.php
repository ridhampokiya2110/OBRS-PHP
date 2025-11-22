<?php
session_start();
include('assets/inc/config.php');
//date_default_timezone_set('Africa /Nairobi');
include('assets/inc/checklogin.php');
check_login();
$aid = $_SESSION['admin_id'];
if (isset($_POST['add_train'])) {

  $name = $_POST['name'];
  $route = $_POST['route'];
  $current = $_POST['current'];
  $destination = $_POST['destination'];
  $time = $_POST['time'];
  $number = $_POST['number'];
  $fare = $_POST['fare'];
  $passengers = 36; // Fixed total seats to 36
  $available_seats = 36; // Initially available seats equals total seats
  //sql querry to post the entered information
  $query = "insert into obrs_bus (name, route, current, destination, time, number, fare, passengers, available_seats) values(?,?,?,?,?,?,?,?,?)";
  $stmt = $mysqli->prepare($query);
  //bind this parameters
  $rc = $stmt->bind_param('sssssssss', $name, $route, $current, $destination, $time, $number, $fare, $passengers, $available_seats);
  $stmt->execute();
  if ($stmt) {
    $succ = "Bus Added";
  } else {
    $err = "Please Try Again Later";
  }
  #echo"<script>alert('Your Profile Has Been Updated Successfully');</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<!--Head-->
<?php include('assets/inc/head.php'); ?>
<style>
  .card {
    background: #D8E4F8;
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15), 0 4px 8px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(0, 0, 0, 0.05);
  }

  .card-header {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-bottom: 2px solid rgba(0, 0, 0, 0.05);
    padding: 25px;
    border-radius: 20px 20px 0 0;
  }

  .card-subtitle {
    color: #2C3E50;
    font-size: 1.4em;
    font-weight: 600;
    letter-spacing: 0.5px;
  }

  .form-control {
    border-radius: 12px;
    border: 2px solid #E8EEF4;
    padding: 12px;
    background: rgba(255, 255, 255, 0.9);
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
  }

  .form-control:focus {
    border-color: #4E73DF;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
  }

  .col-form-label {
    color: #34495E;
    font-weight: 600;
    font-size: 1.1em;
  }

  .btn {
    border-radius: 12px;
    padding: 12px 30px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
  }

  .btn-success {
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);
  }

  .btn-danger {
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2);
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
        <h2 class="page-head-title" style="color: black;">Add Bus</h2>
        <nav aria-label="breadcrumb" role="navigation">
          <ol class="breadcrumb page-head-nav">
            <li class="breadcrumb-item"><a href="pass-dashboard.php" style="color: black;">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#" style="color: black;">Buses</a></li>
            <li class="breadcrumb-item active" style="color: black; text-decoration: underline;">Add Bus</li>
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
        <!--Train Details forms-->
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <span class="card-subtitle">Add New Bus Details</span>
              </div>
              <div class="card-body" style="padding: 40px;">
                <form method="POST">
                  <div class="form-group row">
                    <label class="col-12 col-sm-3 col-form-label text-sm-right"> Bus Name</label>
                    <div class="col-12 col-sm-8 col-lg-6">
                      <input class="form-control" name="name" type="text" placeholder="Enter Bus Name">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-12 col-sm-3 col-form-label text-sm-right"> Bus Number</label>
                    <div class="col-12 col-sm-8 col-lg-6">
                      <input class="form-control" name="number" type="text" placeholder="Enter Bus Number">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-12 col-sm-3 col-form-label text-sm-right">Bus Route</label>
                    <div class="col-12 col-sm-8 col-lg-6">
                      <input class="form-control" name="route" type="text" placeholder="Format: [Source] - [Destination]">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-12 col-sm-3 col-form-label text-sm-right">Departure</label>
                    <div class="col-12 col-sm-8 col-lg-6">
                      <input class="form-control" name="current" type="text" placeholder="Enter Source/Departure">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-12 col-sm-3 col-form-label text-sm-right">Arrival</label>
                    <div class="col-12 col-sm-8 col-lg-6">
                      <input class="form-control" name="destination" type="text" placeholder="Enter Destination/Arrival">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-12 col-sm-3 col-form-label text-sm-right">Departure Date & Time</label>
                    <div class="col-12 col-sm-8 col-lg-6">
                      <input class="form-control" name="time" type="datetime-local" placeholder="Enter Bus's Departure Date & Time">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-12 col-sm-3 col-form-label text-sm-right">Total Number of Seats</label>
                    <div class="col-12 col-sm-8 col-lg-6">
                      <input class="form-control" value="36" readonly type="text">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label class="col-12 col-sm-3 col-form-label text-sm-right">Bus Fare</label>
                    <div class="col-12 col-sm-8 col-lg-6">
                      <input class="form-control" name="fare" type="number" placeholder="₹">
                    </div>
                  </div>
                  <div class="col-sm-12">
                    <p class="text-center" style="margin-top: 30px;">
                      <input class="btn btn-space btn-success" value="Add Bus" name="add_train" type="submit" style="padding: 12px 30px; font-weight: 600; letter-spacing: 0.5px; transition: all 0.3s ease;">
                      <a href="emp-manage-bus.php" class="btn btn-space btn-danger" style="padding: 12px 30px; font-weight: 600; letter-spacing: 0.5px; transition: all 0.3s ease;">Cancel</a>
                    </p>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <!--End Train Instance-->

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
        //-initialize the javascript
        App.init();
        App.formElements();
      });
    </script>
</body>

</html>