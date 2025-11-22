<div class="be-left-sidebar" style="background-color: #F0F0D7;">
  <div class="left-sidebar-wrapper"><a class="left-sidebar-toggle" href="#" style="color: black;">Dashboard</a>
    <div class="left-sidebar-spacer">
      <div class="left-sidebar-scroll">
        <div class="left-sidebar-content">
          <ul class="sidebar-elements" style="color: black;">
            <li class="divider" style="color: black;">Menu</li>
            <li class=""><a href="pass-dashboard.php" style="color: black;"><i class="fa-solid fa-gauge-high" style="width: 20px; margin-right: 8px;"></i><span>Dashboard</span></a>
            </li>
            <?php
            $aid = $_SESSION['pass_id']; //assaign session a varible [PASSENGER ID]
            $ret = "select * from obrs_passenger where pass_id=?";
            $stmt = $mysqli->prepare($ret);
            $stmt->bind_param('i', $aid);
            $stmt->execute(); //ok
            $res = $stmt->get_result();
            //$cnt=1;
            while ($row = $res->fetch_object()) {
            ?>
              <li class="parent"><a href="#" style="color: black;"><i class="fa-solid fa-user" style="width: 20px; margin-right: 8px;"></i><span>BusZy Traveler <?php echo $row->pass_uname; ?></span></a>
                <ul class="sub-menu">
                  <li><a href="pass-profile.php" style="color: black;">View</a>
                  </li>
                  <li><a href="pass-profile-update.php" style="color: black;">Update</a>
                  </li>

                  <li><a href="pass-profile-avatar.php" style="color: black;">Profile Avatar</a>
                  </li>
                  <li><a href="pass-profile-password.php" style="color: black;">Change Password</a>
                  </li>

                </ul>
              </li>
            <?php } ?>
            <li class="parent"><a href="#" style="color: black;"><i class="fa-solid fa-bus" style="width: 20px; margin-right: 8px;"></i><span>BusZy Express</span></a>

              <ul class="sub-menu">
                <li><a href="pass-all-available-bus.php" style="color: black;">All Available Buses</a>
              </ul>

            </li>
            <li class="parent"><a href="#" style="color: black;"><i class="fa-solid fa-calendar-check" style="width: 20px; margin-right: 8px;"></i><span>BusZy Booking</span></a>
              <ul class="sub-menu">
                <li><a href="pass-book-bus.php" style="color: black;">Reserve Your Seats</a>
                </li>
              </ul>
            </li>
            <li class="parent"><a href="#" style="color: black;"><i class="fa-solid fa-ticket" style="width: 20px; margin-right: 8px;"></i><span>BusZy Tickets</span></a>
              <ul class="sub-menu">
                <li><a href="pass-bus-checkout-ticket.php" style="color: black;">Review & Pay</a>
                </li>
                <li><a href="pass-show-confirm-ticket.php" style="color: black;">Download E-Ticket</a>
                </li>
                <li><a href="pass-cancel-bus.php" style="color: black;">Request Refund</a>
                </li>
              </ul>
            </li>
            <li><a href="pass-logout.php" style="color: black;"><i class="fa-solid fa-right-from-bracket" style="width: 20px; margin-right: 8px;"></i><span>BusZy Exit</span></a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<script
  src="https://kit.fontawesome.com/f766ed9c4c.js"
  crossorigin="anonymous"></script>