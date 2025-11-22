<?php
  session_start();
  include('assets/inc/config.php');
  include('assets/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['admin_id'];

  // Handle delete ticket
  if(isset($_GET['del'])) {
    $id = intval($_GET['del']);
    $stmt = $mysqli->prepare("DELETE FROM obrs_booking_history WHERE id=?");
    $stmt->bind_param('i', $id);
    $result = $stmt->execute();
    $stmt->close();	 

    if($result) {
      $_SESSION['succ'] = "Ticket Details Deleted Successfully";
      header("Location: emp-manage-tickets.php");
      exit();
    } else {
      $_SESSION['err'] = "Error Deleting Ticket Details. Please Try Again";
      header("Location: emp-manage-tickets.php");
      exit();
    }
  }

  // Get success/error messages from session and clear them
  if(isset($_SESSION['succ'])) {
    $succ = $_SESSION['succ'];
    unset($_SESSION['succ']);
  }
  if(isset($_SESSION['err'])) {
    $err = $_SESSION['err'];
    unset($_SESSION['err']); 
  }

  // Get ticket counts
  $active_count = $mysqli->query("SELECT COUNT(*) as count FROM obrs_booking_history WHERE payment_status='Paid' AND status NOT IN ('Expired','Cancelled')")->fetch_object()->count;
  $cancelled_count = $mysqli->query("SELECT COUNT(*) as count FROM obrs_booking_history WHERE status='Cancelled'")->fetch_object()->count;
  $expired_count = $mysqli->query("SELECT COUNT(*) as count FROM obrs_booking_history WHERE status='Expired'")->fetch_object()->count;
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
        <h2 class="page-head-title" style="color: black;">View Bus Tickets</h2>
        <nav aria-label="breadcrumb" role="navigation">
          <ol class="breadcrumb page-head-nav">
            <li class="breadcrumb-item"><a href="emp-dashboard.php" style="color: black;">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#" style="color: black;">Bus Tickets</a></li>
            <li class="breadcrumb-item active" style="color: black; text-decoration: underline;">View</li>
          </ol>
        </nav>
      </div>

      <div class="main-content container-fluid">
        <?php if(isset($succ)): ?>
          <div class="alert alert-success alert-dismissible" role="alert">
            <?php echo $succ; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        <?php endif; ?>

        <?php if(isset($err)): ?>
          <div class="alert alert-danger alert-dismissible" role="alert">
            <?php echo $err; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        <?php endif; ?>

        <div class="row">
          <div class="col-sm-12">
            <div class="card card-table" style="background: linear-gradient(120deg, #E0EAFC 0%, #CFDEF3 100%); color: #2C3E50; box-shadow: 0 4px 20px rgba(0,0,0,0.1); border-radius: 15px;">
              <div class="card-header" style="border-bottom: 1px solid rgba(44,62,80,0.1); padding: 25px;">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h4 style="margin: 0 0 15px 0; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">
                      <i class="fa fa-ticket" style="margin-right: 10px;"></i>All Bus Tickets
                    </h4>
                    <div style="display: flex; gap: 20px;">
                      <span style="background: linear-gradient(120deg, #27ae60 0%, #2ecc71 100%); padding: 5px 15px; border-radius: 20px; color: white;">
                        Active: <?php echo $active_count; ?>
                      </span>
                      <span style="background: linear-gradient(120deg, #e74c3c 0%, #c0392b 100%); padding: 5px 15px; border-radius: 20px; color: white;">
                        Cancelled: <?php echo $cancelled_count; ?>
                      </span>
                      <span style="background: linear-gradient(120deg, #d35400 0%, #c0392b 100%); padding: 5px 15px; border-radius: 20px; color: white;">
                        Expired: <?php echo $expired_count; ?>
                      </span>
                    </div>
                  </div>
                  <button onclick="window.location.href='emp-manage-tickets.php';" class="btn btn-primary" style="background: linear-gradient(120deg, #3498db 0%, #2980b9 100%); border: none; padding: 8px 15px; border-radius: 20px;">
                    <i class="fa fa-refresh" style="margin-right: 5px;"></i>Refresh
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div class="table-responsive" style="overflow-x: auto;">
                  <table class="table table-hover table-fw-widget table-striped" style="margin: 0; text-align: center; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                    <thead>
                      <tr style="background: linear-gradient(120deg, #2C3E50 0%, #34495E 100%);">
                        <th style="border: none; color: white; font-weight: 600; padding: 18px; vertical-align: middle; font-size: 14px;">Booking ID</th>
                        <th style="border: none; color: white; font-weight: 600; padding: 18px; vertical-align: middle; font-size: 14px;">Passenger</th>
                        <th style="border: none; color: white; font-weight: 600; padding: 18px; vertical-align: middle; font-size: 14px;">Phone</th>
                        <th style="border: none; color: white; font-weight: 600; padding: 18px; vertical-align: middle; font-size: 14px;">Bus Name</th>
                        <th style="border: none; color: white; font-weight: 600; padding: 18px; vertical-align: middle; font-size: 14px;">Bus Number</th>
                        <th style="border: none; color: white; font-weight: 600; padding: 18px; vertical-align: middle; font-size: 14px;">Departure</th>
                        <th style="border: none; color: white; font-weight: 600; padding: 18px; vertical-align: middle; font-size: 14px;">Arrival</th>
                        <th style="border: none; color: white; font-weight: 600; padding: 18px; vertical-align: middle; font-size: 14px;">Selected Seats</th>
                        <th style="border: none; color: white; font-weight: 600; padding: 18px; vertical-align: middle; font-size: 14px;">Amount</th>
                        <th style="border: none; color: white; font-weight: 600; padding: 18px; vertical-align: middle; font-size: 14px;">Status</th>
                        <th style="border: none; color: white; font-weight: 600; padding: 18px; vertical-align: middle; font-size: 14px;">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $ret="SELECT b.*, p.pass_fname, p.pass_lname, p.pass_addr, p.pass_phone,
                              t.name as bus_name, t.number as train_no 
                              FROM obrs_booking_history b
                              JOIN obrs_passenger p ON b.pass_id = p.pass_id
                              JOIN obrs_bus t ON b.bus_number = t.number";
                        $stmt= $mysqli->prepare($ret);
                        $stmt->execute();
                        $res=$stmt->get_result();
                        while($row=$res->fetch_object())
                        {
                      ?>
                        <tr style="border-bottom: 1px solid rgba(44,62,80,0.1); transition: all 0.2s;">
                          <td style="border: none; padding: 16px; vertical-align: middle; font-size: 14px;">
                            <span style="background: #f0f2f5; padding: 4px 8px; border-radius: 4px; color: #2C3E50;">
                              #<?php echo htmlspecialchars($row->booking_id);?>
                            </span>
                          </td>
                          <td style="border: none; padding: 16px; vertical-align: middle; font-size: 14px;">
                            <div style="font-weight: 600;"><?php echo htmlspecialchars($row->pass_fname . ' ' . $row->pass_lname);?></div>
                          </td>
                          <td style="border: none; padding: 16px; vertical-align: middle; font-size: 14px;">
                            <?php echo htmlspecialchars($row->pass_phone);?>
                          </td>
                          <td style="border: none; padding: 16px; vertical-align: middle; font-size: 14px; font-weight: 500;">
                            <?php echo htmlspecialchars($row->bus_name);?>
                          </td>
                          <td style="border: none; padding: 16px; vertical-align: middle; font-size: 14px;">
                            <span style="background: #f0f2f5; padding: 4px 8px; border-radius: 4px; color: #2C3E50;">
                              <?php echo htmlspecialchars($row->train_no);?>
                            </span>
                          </td>
                          <td style="border: none; padding: 16px; vertical-align: middle; font-size: 14px;">
                            <?php echo htmlspecialchars($row->dep_station);?>
                          </td>
                          <td style="border: none; padding: 16px; vertical-align: middle; font-size: 14px;">
                            <?php echo htmlspecialchars($row->arr_station);?>
                          </td>
                          <td style="border: none; padding: 16px; vertical-align: middle; font-size: 14px; font-weight: 500;">
                            <?php echo htmlspecialchars($row->selected_seats);?>
                          </td>
                          <td style="border: none; padding: 16px; vertical-align: middle; font-size: 14px;">
                            <span style="color: #27ae60; font-weight: 600;">$<?php echo htmlspecialchars($row->bus_fare);?></span>
                          </td>
                          <td style="border: none; padding: 16px; vertical-align: middle;">
                            <?php
                              $status = $row->status;
                              $badgeColor = '';
                              $displayStatus = '';
                              
                              if ($status === 'Expired') {
                                $badgeColor = 'linear-gradient(120deg, #d35400 0%, #c0392b 100%)';
                                $displayStatus = 'Expired';
                              } else if ($status === 'Cancelled') {
                                $badgeColor = 'linear-gradient(120deg, #e74c3c 0%, #c0392b 100%)';
                                $displayStatus = 'Cancelled';
                              } else if ($row->payment_status === 'Pending') {
                                $badgeColor = 'linear-gradient(120deg, #f39c12 0%, #d35400 100%)';
                                $displayStatus = 'Pending';
                              } else if ($row->payment_status === 'Paid') {
                                $badgeColor = 'linear-gradient(120deg, #27ae60 0%, #2ecc71 100%)';
                                $displayStatus = 'Paid';
                              } else {
                                $badgeColor = 'linear-gradient(120deg, #95a5a6 0%, #7f8c8d 100%)';
                                $displayStatus = 'Unknown';
                              }
                            ?>
                            <span class="badge" style="background: <?php echo $badgeColor; ?>; padding: 8px 15px; border-radius: 20px; color: white; font-weight: 500; font-size: 12px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                              <?php echo htmlspecialchars($displayStatus);?>
                            </span>
                          </td>
                          <td style="border: none; padding: 16px; vertical-align: middle;">
                            <?php if($status !== 'Expired' && $status !== 'Cancelled' && $row->payment_status === 'Paid'): ?>
                              <a href="emp-print-ticket.php?id=<?php echo $row->id; ?>" 
                                 class="badge print-btn" 
                                 style="background: linear-gradient(120deg, #27ae60 0%, #2ecc71 100%); color: white; margin: 2px; padding: 8px 15px; border-radius: 20px; font-weight: 500; font-size: 12px; text-decoration: none;">
                                <i class="fa fa-print" style="margin-right: 5px;"></i>Print
                              </a>
                            <?php else: ?>
                              <a href="emp-manage-tickets.php?del=<?php echo $row->id; ?>" 
                                 class="badge delete-btn" 
                                 style="background: linear-gradient(120deg, #e74c3c 0%, #c0392b 100%); color: white; margin: 2px; padding: 8px 15px; border-radius: 20px; font-weight: 500; font-size: 12px; text-decoration: none;"
                                 onclick="return confirm('Are you sure you want to delete this ticket?');">
                                <i class="fa fa-trash" style="margin-right: 5px;"></i>Delete
                              </a>
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
      </div>
      <!--Footer-->
      <?php include('assets/inc/footer.php');?>
      <!--End Footer-->
    </div>
  </div>

  <script src="assets/lib/jquery/jquery.min.js" type="text/javascript"></script>
  <script src="assets/lib/perfect-scrollbar/js/perfect-scrollbar.min.js" type="text/javascript"></script>
  <script src="assets/lib/bootstrap/dist/js/bootstrap.bundle.min.js" type="text/javascript"></script>
  <script src="assets/js/app.js" type="text/javascript"></script>
  <script src="https://kit.fontawesome.com/f766ed9c4c.js" crossorigin="anonymous"></script>
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
    $(document).ready(function(){
      App.init();
      App.dataTables();
    });
  </script>
</body>
</html>