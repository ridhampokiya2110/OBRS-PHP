<?php
  session_start();
  include('assets/inc/config.php');
  include('assets/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['admin_id'];

  if(isset($_GET['delete_id'])) {
    $ticket_id = $_GET['delete_id'];
    
    // Delete the ticket
    $delete_query = "DELETE FROM obrs_booking_history WHERE booking_id=?";
    $stmt = $mysqli->prepare($delete_query);
    $stmt->bind_param('i', $ticket_id);
    $stmt->execute();

    if($stmt) {
      $success = "Ticket Deleted";
      header("refresh:1; url=emp-pending-tickets.php");
    }
    else {
      $err = "Try Again Later";
    }
  }
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
      <div class="page-head">
        <h2 class="page-head-title" style="color: black;">Delete Bus Ticket</h2>
        <nav aria-label="breadcrumb" role="navigation">
          <ol class="breadcrumb page-head-nav">
            <li class="breadcrumb-item"><a href="emp-dashboard.php" style="color: black;">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="emp-pending-tickets.php" style="color: black;">Bus Tickets</a></li>
            <li class="breadcrumb-item active" style="color: black; text-decoration: underline;">Delete</li>
          </ol>
        </nav>
      </div>

      <div class="main-content container-fluid">
        <div class="row">
          <div class="col-sm-12">
            <?php if(isset($success)) { ?>
              <div class="alert alert-success alert-dismissible" role="alert">
                <button class="close" data-dismiss="alert" type="button" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong>Success!</strong> <?php echo $success;?>
              </div>
            <?php } ?>
            <?php if(isset($err)) { ?>
              <div class="alert alert-danger alert-dismissible" role="alert">
                <button class="close" data-dismiss="alert" type="button" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong>Error!</strong> <?php echo $err;?>
              </div>
            <?php } ?>
          </div>
        </div>
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
    });
  </script>
</body>
</html>
