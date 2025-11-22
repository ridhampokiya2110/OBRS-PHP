<!--Start Server side code to give us and hold session-->
<?php
  session_start();
  include('assets/inc/config.php');
  include('assets/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['admin_id'];

  // Delete bus record
  if(isset($_GET['del'])) {
    $id = intval($_GET['del']);
    $stmt = $mysqli->prepare("DELETE FROM obrs_bus WHERE id=?");
    $stmt->bind_param('i', $id);
    $result = $stmt->execute();
    $stmt->close();	 

    if($result) {
      $succ = "Bus Details Deleted Successfully";
    } else {
      $err = "Error Deleting Bus Details. Please Try Again";
    }
  }
?>
<!--End Server side scripting-->
<!DOCTYPE html>
<html lang="en">
<!--Head-->
<?php include("assets/inc/head.php"); ?>
<!--End Head-->

<body style="background-color: #F0F0D7;">
  <div class="be-wrapper be-fixed-sidebar">
    <!--Navigation-->
    <?php include("assets/inc/navbar.php"); ?>
    <?php include('assets/inc/sidebar.php'); ?>
    <!--End Navigation-->

    <div class="be-content">
      <div class="page-head">
        <h2 class="page-head-title" style="color: black;">BusZy Booking</h2>
        <nav aria-label="breadcrumb" role="navigation">
          <ol class="breadcrumb page-head-nav">
            <li class="breadcrumb-item"><a href="emp-dashboard.php" style="color: black;">Dashboard</a></li>
            <li class="breadcrumb-item active" style="color: black; text-decoration: underline;">Manage Buses</li>
          </ol>
        </nav>
      </div>

      <?php if(isset($succ)): ?>
        <script>
          setTimeout(function() { 
            swal("Success!", "<?php echo $succ; ?>", "success");
          }, 100);
        </script>
      <?php endif; ?>

      <?php if(isset($err)): ?>
        <script>
          setTimeout(function() { 
            swal("Error!", "<?php echo $err; ?>", "error");
          }, 100);
        </script>
      <?php endif; ?>

      <div class="main-content container-fluid">
        <div class="row">
          <div class="col-sm-12">
            <div class="card card-table" style="background: linear-gradient(120deg, #E0EAFC 0%, #CFDEF3 100%); color: #2C3E50; box-shadow: 0 4px 20px rgba(0,0,0,0.1); border-radius: 15px;">
              <div class="card-header" style="border-bottom: 1px solid rgba(44,62,80,0.1); padding: 25px;">
                <h4 style="margin: 0; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">
                  <i class="fa fa-bus" style="margin-right: 10px;"></i>Manage Buses
                </h4>
              </div>

              <div class="card-body">
                <table class="table table-hover table-striped table-bordered" id="busTable" style="width:100%; margin: 0;">
                  <thead>
                    <tr style="border-bottom: 2px solid rgba(44,62,80,0.1);">
                      <th style="border: none; color: #34495E; font-weight: 600;">Bus Number</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Bus Name</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Route</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Departure</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Arrival</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Dep.Time</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Fare</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Total Seats</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Available Seats</th>
                      <th style="border: none; color: #34495E; font-weight: 600;">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $query = "SELECT * FROM obrs_bus ORDER BY number";
                      $stmt = $mysqli->prepare($query);
                      $stmt->execute();
                      $result = $stmt->get_result();
                      
                      while($row = $result->fetch_object()):
                        $seatStyle = '';
                        $seatMsg = '';
                        if($row->available_seats < 7) {
                          $seatStyle = 'color: #F26B0F; font-weight: bold;';
                          $seatMsg = ' (Hurry! Only ' . $row->available_seats . ' seats left!)';
                        } else if($row->available_seats < 14) {
                          $seatStyle = 'color: #ba9e00; font-weight: bold;';
                          $seatMsg = ' (Going fast - Book now!)';
                        } else if($row->available_seats < 20) {
                          $seatStyle = 'color: #2E8B57; font-weight: bold;';
                          $seatMsg = ' (Good availability)';
                        } else {
                          $seatStyle = '';
                          $seatMsg = ' (Plenty of seats)';
                        }
                    ?>
                      <tr style="border-bottom: 1px solid rgba(44,62,80,0.1); transition: all 0.3s;">
                        <td style="border: none;"><?php echo htmlspecialchars($row->number); ?></td>
                        <td style="border: none;"><?php echo htmlspecialchars($row->name); ?></td>
                        <td style="border: none;"><?php echo htmlspecialchars($row->route); ?></td>
                        <td style="border: none;"><?php echo htmlspecialchars($row->current); ?></td>
                        <td style="border: none;"><?php echo htmlspecialchars($row->destination); ?></td>
                        <td style="border: none;"><?php echo date('d M, Y \a\t h:i A', strtotime(htmlspecialchars($row->time))); ?></td>
                        <td style="border: none;">₹<?php echo number_format($row->fare, 2); ?></td>
                        <td style="border: none;"><?php echo $row->passengers; ?></td>
                        <td style="border: none; <?php echo $seatStyle; ?>"><?php echo $row->available_seats; ?><br><?php echo $seatMsg; ?></td>
                        <td style="border: none;">
                          <div class="btn-group" role="group">
                            <a href="emp-update-bus.php?id=<?php echo $row->id; ?>" class="badge" style="background: linear-gradient(120deg, #3498db 0%, #2980b9 100%); color: white; margin: 2px; padding: 8px 15px; border-radius: 20px; font-weight: 500; font-size: 12px; text-decoration: none;">
                              <i class="fa fa-edit" style="margin-right: 5px;"></i> Update
                            </a>
                            <a href="emp-manage-bus.php?del=<?php echo $row->id; ?>" class="badge delete-btn" style="background: linear-gradient(120deg, #e74c3c 0%, #c0392b 100%); color: white; margin: 2px; padding: 8px 15px; border-radius: 20px; font-weight: 500; font-size: 12px; text-decoration: none;" onclick="return confirm('Are you sure you want to delete this bus?');">
                              <i class="fa fa-trash" style="margin-right: 5px;"></i> Delete
                            </a>
                            <a href="emp-view-bus.php?id=<?php echo $row->id; ?>" class="badge" style="background: linear-gradient(120deg, #27ae60 0%, #2ecc71 100%); color: white; margin: 2px; padding: 8px 15px; border-radius: 20px; font-weight: 500; font-size: 12px; text-decoration: none;">
                              <i class="fa fa-eye" style="margin-right: 5px;"></i> View
                            </a>
                          </div>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
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

  <!-- Core Scripts -->
  <script src="assets/lib/jquery/jquery.min.js" type="text/javascript"></script>
  <script src="assets/lib/perfect-scrollbar/js/perfect-scrollbar.min.js" type="text/javascript"></script>
  <script src="assets/lib/bootstrap/dist/js/bootstrap.bundle.min.js" type="text/javascript"></script>
  <script src="assets/js/app.js" type="text/javascript"></script>
  <script src="https://kit.fontawesome.com/f766ed9c4c.js" crossorigin="anonymous"></script>

  <!-- DataTables Scripts -->
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
      
      $('#busTable').DataTable({
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
          'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        pageLength: 10,
        order: [[0, 'asc']]
      });
    });
  </script>
</body>
</html>