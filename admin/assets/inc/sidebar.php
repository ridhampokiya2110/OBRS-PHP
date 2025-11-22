<div class="be-left-sidebar" style="background-color: #F0F0D7;">
  <div class="left-sidebar-wrapper"><a class="left-sidebar-toggle" href="#" style="color: black;">Dashboard</a>
    <div class="left-sidebar-spacer">
      <div class="left-sidebar-scroll">
        <div class="left-sidebar-content">
          <ul class="sidebar-elements" style="color: black;">
            <li class="divider" style="color: black;">Menu</li>
            <li class=""><a href="emp-dashboard.php" style="color: black;"><i class="fa-solid fa-gauge-high" style="width: 20px; margin-right: 8px;"></i><span>Dashboard</span></a>
            </li>
            <?php
            $aid = $_SESSION['admin_id'];
            $ret = "select * from obrs_admin where admin_id=?";
            $stmt = $mysqli->prepare($ret);
            $stmt->bind_param('i', $aid);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_object()) {
            ?>
              <li class="parent"><a href="#" style="color: black;"><i class="fa-solid fa-user" style="width: 20px; margin-right: 8px;"></i><span>BusZy Admin <?php echo $row->admin_uname; ?></span></a>
                <ul class="sub-menu">
                  <li><a href="emp-profile.php" style="color: black;">View</a>
                  </li>
                  <li><a href="emp-profile-update.php" style="color: black;">Update</a>
                  </li>
                  <li><a href="emp-profile-avatar.php" style="color: black;">Profile Avatar</a>
                  </li>
                  <li><a href="emp-profile-password.php" style="color: black;">Change Password</a>
                  </li>
                </ul>
              </li>
            <?php } ?>
            <li class="parent"><a href="#" style="color: black;"><i class="fa-solid fa-bus" style="width: 20px; margin-right: 8px;"></i><span>BusZy Buses</span></a>
              <ul class="sub-menu">
                <li><a href="emp-add-bus.php" style="color: black;">Add Bus</a>
                <li><a href="emp-manage-bus.php" style="color: black;">Manage Buses</a>
                </li>
              </ul>
            </li>
            <li class="parent"><a href="#" style="color: black;"><i class="fa-solid fa-users" style="width: 20px; margin-right: 8px;"></i><span>BusZy Travelers</span></a>
              <ul class="sub-menu">
                <li><a href="emp-add-passenger.php" style="color: black;">Add Traveler</a>
                </li>
                <li><a href="emp-manage-passengers.php" style="color: black;">Manage Travelers</a>
                </li>
              </ul>
            </li>
            <li class="parent"><a href="#" style="color: black;"><i class="fa-solid fa-user-tie" style="width: 20px; margin-right: 8px;"></i><span>BusZy Employees</span></a>
              <ul class="sub-menu">
                <li><a href="admin-add-employee.php" style="color: black;">Add Employee</a>
                </li>
                <li><a href="admin-manage-employee.php" style="color: black;">Manage Employees</a>
                </li>
              </ul>
            </li>
            <li class="parent"><a href="#" style="color: black;"><i class="fa-solid fa-ticket" style="width: 20px; margin-right: 8px;"></i><span>BusZy Tickets</span></a>
              <ul class="sub-menu">
                <li><a href="emp-pending-tickets.php" style="color: black;"><span class="badge badge-info float-right">All</span>View Tickets</a> </li>
                <li><a href="emp-paid-tickets.php" style="color: black;"><span class="badge badge-success float-right">Paid</span>View Tickets</a> </li>
                <li><a href="emp-manage-tickets.php" style="color: black;">Manage</a>
                </li>
              </ul>
            </li>
            <li class="parent"><a href="#" style="color: black;"><i class="fa-solid fa-money-bill" style="width: 20px; margin-right: 8px;"></i><span>BusZy Accounting</span></a>
              <ul class="sub-menu">
                <li><a href="emp-view-accounting.php" style="color: black;"><span class="badge badge-success float-right">Ticket Payments</span>View</a>
                </li>
              </ul>
            </li>
            <li><a href="emp-logout.php" style="color: black;"><i class="fa-solid fa-right-from-bracket" style="width: 20px; margin-right: 8px;"></i><span>BusZy Exit</span></a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>