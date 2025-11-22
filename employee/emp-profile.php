<?php
session_start();
include('assets/inc/config.php');
include('assets/inc/checklogin.php');
check_login();
$aid = $_SESSION['emp_id'];
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
          <h2 class="page-head-title" style="color: black;">My Profile</h2>
          <nav aria-label="breadcrumb" role="navigation">
            <ol class="breadcrumb page-head-nav">
              <li class="breadcrumb-item"><a href="emp-dashboard.php" style="color: black;">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="#" style="color: black;">Employee</a></li>
              <li class="breadcrumb-item active" style="color: black; text-decoration: underline;">Profile</li>
            </ol>
          </nav>
      </div>

      <?php
      $aid = $_SESSION['emp_id'];
      $ret = "select * from obrs_employee where emp_id=?";
      $stmt = $mysqli->prepare($ret);
      $stmt->bind_param('i', $aid);
      $stmt->execute();
      $res = $stmt->get_result();
      while ($row = $res->fetch_object()) {
      ?>

        <div class="main-content container-fluid px-6 py-8">
          <div class="max-w-7xl mx-auto">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
              <div class="p-8" style="background-color: #D7E3F7;">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                  <!-- Profile Image & Basic Info -->
                  <div class="text-center">
                    <div class="relative inline-block">
                      <img
                        src="assets/img/profile/<?php echo $row->emp_dpic; ?>"
                        alt="Profile Picture"
                        class="w-48 h-48 rounded-full object-cover border-4 border-white shadow-lg mx-auto"
                        style="border-radius: 50%;">
                    </div>
                    <h3 class="mt-4 text-xl font-bold text-gray-800">
                      <?php echo $row->emp_fname; ?> <?php echo $row->emp_lname; ?>
                    </h3>
                    <p class="text-gray-600">@<?php echo $row->emp_uname; ?></p>
                  </div>

                  <!-- Profile Details -->
                  <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-xl p-6">
                      <h4 class="flex items-center text-lg font-semibold text-gray-800 mb-4">
                        <i class="fa fa-user-circle mr-2"></i>
                        About Me
                      </h4>

                      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                          <div class="flex items-center">
                            <div>
                              <strong class="font-bold">Phone:</strong>
                              <span class="ml-2 text-gray-600"><?php echo $row->emp_phone; ?></span>
                            </div>
                          </div>
                        </div>

                        <div class="space-y-4">
                          <div class="flex items-center">
                            <div>
                              <strong class="font-bold">Department:</strong>
                              <span class="ml-2 text-gray-600"><?php echo $row->emp_dept; ?></span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>

      <!--Footer-->
      <?php include('assets/inc/footer.php'); ?>
      <!--EndFooter-->
    </div>
  </div>

  <script src="assets/lib/jquery/jquery.min.js" type="text/javascript"></script>
  <script src="assets/lib/perfect-scrollbar/js/perfect-scrollbar.min.js" type="text/javascript"></script>
  <script src="assets/lib/bootstrap/dist/js/bootstrap.bundle.min.js" type="text/javascript"></script>
  <script src="assets/js/app.js" type="text/javascript"></script>
  <script src="https://kit.fontawesome.com/f766ed9c4c.js" crossorigin="anonymous"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      App.init();
    });
  </script>
</body>

</html>