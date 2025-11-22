<?php

/**
 *Server side code to get details of single passenger using id 
 */
$aid = $_SESSION['pass_id'];
$ret = "select * from obrs_passenger where pass_id=?"; //fetch details of pasenger
$stmt = $mysqli->prepare($ret);
$stmt->bind_param('i', $aid);
$stmt->execute(); //ok
$res = $stmt->get_result();
//$cnt=1;
while ($row = $res->fetch_object()) {
?>
  <nav class="navbar navbar-expand fixed-top be-top-header" style="background-color: #F0F0D7;">
    <div class="container-fluid">
      <div class="flex items-center">
        <a href="pass-dashboard.php" class="flex items-center space-x-2">
          <img src="assets/img/buszy-logo-Copy.png" alt="Bus Logo" class="h-[3vw] w-[6vw] ml-[4vw] mr-[5vw]">
        </a>
      </div>
      <div class="page-title"><span style="color: black;">

          <?php
          date_default_timezone_set('Asia/Kolkata');
          $welcome_string = "Hello";
          $numeric_date = date("G");
          if ($numeric_date >= 0 && $numeric_date <= 11)
            $welcome_string = "Good Morning!";
          else if ($numeric_date >= 12 && $numeric_date <= 16)
            $welcome_string = "Good Afternoon!";
          else if ($numeric_date >= 16 && $numeric_date <= 20)
            $welcome_string = "Good Evening!";
          else if ($numeric_date >= 20 && $numeric_date <= 23)
            $welcome_string = "Good Night!";
          echo "$welcome_string";
          ?>

          <?php echo $row->pass_uname; ?></span></div>
      <div class="be-right-navbar">
        <ul class="nav navbar-nav float-right be-user-nav">
          <li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" role="button" aria-expanded="false" style="color: black;"><img src="assets/img/profile/<?php echo $row->pass_dpic; ?>" alt="Avatar"><span class="user-name" style="color: black;">Túpac Amaru</span></a>
            <div class="dropdown-menu" role="menu">
              <a class="dropdown-item" href="pass-profile.php" style="color: black;"><span class="icon mdi mdi-face"></span>Account</a><a class="dropdown-item" href="pass-logout.php" style="color: black;"><span class="icon mdi mdi-power"></span>Logout</a>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </nav>
<?php } ?>