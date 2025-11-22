<?php
   /**
    * Server side code to get details of single admin using id 
    */
    $aid = $_SESSION['admin_id'];
    $ret = "select * from obrs_admin where admin_id=?"; // fetch details of admin
    $stmt = $mysqli->prepare($ret);
    $stmt->bind_param('i', $aid);
    $stmt->execute(); //ok
    $res = $stmt->get_result();
    while($row = $res->fetch_object()) {
?>
    <nav class="navbar navbar-expand fixed-top be-top-header" style="background-color: #F0F0D7;">
        <div class="container-fluid">
          <div class="flex items-center">
            <a href="emp-dashboard.php" class="flex items-center space-x-2">
              <div class="flex items-center">
                <h3 class="text-2xl ml-7 mr-5" style="font-family: 'Poppins', sans-serif; color: black; font-weight:700;">Admin Portal</h3>
              </div>
            </a>
          </div>
          <div class="page-title"><span style="color: black;">
          
          <?php 
          date_default_timezone_set('Asia/Kolkata');
          $welcome_string = "Hello"; 
          $numeric_date = date("G");
          if($numeric_date >= 0 && $numeric_date <= 11) {
              $welcome_string = "Good Morning!";
          } else if($numeric_date >= 12 && $numeric_date <= 16) {
              $welcome_string = "Good Afternoon!";
          } else if($numeric_date >= 16 && $numeric_date <= 20) {
              $welcome_string = "Good Evening!";
          } else if($numeric_date >= 20 && $numeric_date <= 23) {
              $welcome_string = "Good Night!";
          }
          echo $welcome_string;
          ?>
          
          <?php echo $row->admin_uname; ?></span></div>
          <div class="be-right-navbar">
            <ul class="nav navbar-nav float-right be-user-nav">
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" role="button" aria-expanded="false" style="color: black;">
                  <img src="assets/img/profile/<?php echo $row->admin_dpic; ?>" alt="Avatar">
                  <span class="user-name" style="color: black;"><?php echo $row->admin_uname; ?></span>
                </a>
                <div class="dropdown-menu" role="menu">     
                  <a class="dropdown-item" href="emp-profile.php" style="color: black;">
                    <span class="icon mdi mdi-face"></span>Account
                  </a>
                  <a class="dropdown-item" href="emp-logout.php" style="color: black;">
                    <span class="icon mdi mdi-power"></span>Logout
                  </a>
                </div>
              </li>
            </ul>
          </div>
        </div>
    </nav>
<?php } ?>