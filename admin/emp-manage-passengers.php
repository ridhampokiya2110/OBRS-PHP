<!--Start Server side code to give us and hold session-->
<?php
  session_start();
  include('assets/inc/config.php');
  include('assets/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['admin_id'];
  //delete or remove library user  php code
if(isset($_GET['del']))
{
      $id=intval($_GET['del']);
      $adn="delete from obrs_passenger where pass_id=?";
      $stmt= $mysqli->prepare($adn);
      $stmt->bind_param('i',$id);
      $stmt->execute();
      $stmt->close();	 

        if($stmt)
        {
          $succ = "Passenger Details Removed";
        }
          else
          {
            $err = "Try Again Later";
          }
}
?>
<!--End Server side scriptiing-->
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
        <h2 class="page-head-title" style="color: black;">Manage Passengers</h2>
        <nav aria-label="breadcrumb" role="navigation">
          <ol class="breadcrumb page-head-nav">
            <li class="breadcrumb-item"><a href="emp-dashboard.php" style="color: black;">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#" style="color: black;">Passengers</a></li>
            <li class="breadcrumb-item active" style="color: black; text-decoration: underline;">Manage</li>
          </ol>
        </nav>
      </div>

      <?php if(isset($succ)) {?>
        <script>
          setTimeout(function() { 
            swal("Success!","<?php echo $succ;?>!","success").then(() => {
              window.location.href = 'emp-manage-passengers.php';
            });
          }, 100);
        </script>
      <?php } ?>

      <?php if(isset($err)) {?>
        <script>
          setTimeout(function() { 
            swal("Failed!","<?php echo $err;?>!","error").then(() => {
              window.location.href = 'emp-manage-passengers.php';
            });
          }, 100);
        </script>
      <?php } ?>

      <div class="main-content container-fluid">
        <div class="row">
          <div class="col-sm-12">
            <div class="card card-table" style="background: linear-gradient(120deg, #E0EAFC 0%, #CFDEF3 100%); color: #2C3E50; box-shadow: 0 4px 20px rgba(0,0,0,0.1); border-radius: 15px;">
              <div class="card-header" style="border-bottom: 1px solid rgba(44,62,80,0.1); padding: 25px;">
                <div class="d-flex justify-content-between align-items-center">
                  <h4 style="margin: 0; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">
                    <i class="fa fa-users" style="margin-right: 10px;"></i>Manage Passengers
                  </h4>
                  <button onclick="window.location.reload();" class="btn btn-primary" style="background: linear-gradient(120deg, #3498db 0%, #2980b9 100%); border: none; padding: 8px 15px; border-radius: 20px;">
                    <i class="fa fa-refresh" style="margin-right: 5px;"></i>Refresh
                  </button>
                </div>
              </div>
              <div class="card-body">
                <table class="table table-hover table-fw-widget table-striped table-bordered" style="margin: 0; text-align: center;">
                  <thead>
                    <tr style="border-bottom: 2px solid rgba(44,62,80,0.1); background-color: #f8f9fa;">
                      <th style="border: none; color: #34495E; font-weight: 600; padding: 15px; vertical-align: middle; width: 20%;">Name</th>
                      <th style="border: none; color: #34495E; font-weight: 600; padding: 15px; vertical-align: middle; width: 15%;">Contact</th>
                      <th style="border: none; color: #34495E; font-weight: 600; padding: 15px; vertical-align: middle; width: 20%;">Address</th>
                      <th style="border: none; color: #34495E; font-weight: 600; padding: 15px; vertical-align: middle; width: 20%;">Email</th>
                      <th style="border: none; color: #34495E; font-weight: 600; padding: 15px; vertical-align: middle; width: 25%;">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $ret="SELECT * FROM obrs_passenger";
                      $stmt= $mysqli->prepare($ret);
                      $stmt->execute();
                      $res=$stmt->get_result();
                      while($row=$res->fetch_object())
                      {
                    ?>
                      <tr style="border-bottom: 1px solid rgba(44,62,80,0.1); transition: all 0.3s;">
                        <td style="border: none; padding: 15px; vertical-align: middle;"><?php echo $row->pass_fname;?> <?php echo $row->pass_lname;?></td>
                        <td style="border: none; padding: 15px; vertical-align: middle;"><?php echo $row->pass_phone;?></td>
                        <td style="border: none; padding: 15px; vertical-align: middle;"><?php echo $row->pass_addr;?></td>
                        <td style="border: none; padding: 15px; vertical-align: middle;"><?php echo $row->pass_email;?></td>
                        <td style="border: none; padding: 15px; vertical-align: middle;">
                          <div class="d-flex justify-content-center gap-2">
                            <a href="emp-update-passenger.php?pass_id=<?php echo $row->pass_id;?>" class="badge" style="background: linear-gradient(120deg, #50C878 0%, #228B22 100%); color: #FFFFFF; padding: 8px 15px; border-radius: 20px; font-weight: 500; font-size: 12px; text-decoration: none; margin: 0 3px;">
                              <i class="fa fa-edit" style="margin-right: 5px;"></i>Update
                            </a>
                            <a href="emp-manage-passengers.php?del=<?php echo $row->pass_id;?>" class="badge" style="background: linear-gradient(120deg, #FF6B6B 0%, #dc3545 100%); color: #FFFFFF; padding: 8px 15px; border-radius: 20px; font-weight: 500; font-size: 12px; text-decoration: none; margin: 0 3px;">
                              <i class="fa fa-trash" style="margin-right: 5px;"></i>Delete
                            </a>
                            <a href="emp-view-pass.php?pass_id=<?php echo $row->pass_id;?>" class="badge" style="background: linear-gradient(120deg, #3498db 0%, #2980b9 100%); color: #FFFFFF; padding: 8px 15px; border-radius: 20px; font-weight: 500; font-size: 12px; text-decoration: none; margin: 0 3px;">
                              <i class="fa fa-eye" style="margin-right: 5px;"></i>View
                            </a>
                          </div>
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
      
      // Auto refresh every 5 minutes
      setInterval(function() {
        window.location.reload();
      }, 300000);
    });
  </script>
</body>

</html>