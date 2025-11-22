<?php
session_start();
include('assets/inc/config.php');
include('assets/inc/checklogin.php');
check_login();

if(isset($_POST['Create_Profile'])) {
    $pass_id = $_GET['pass_id'];            
    $pass_fname = $_POST['pass_fname'];
    $pass_lname = $_POST['pass_lname'];
    $pass_phone = $_POST['pass_phone'];
    $pass_addr = $_POST['pass_addr'];
    $pass_uname = $_POST['pass_uname'];
    $pass_email = $_POST['pass_email'];

    // Validate inputs
    if(empty($pass_fname) || empty($pass_lname) || empty($pass_phone) || 
       empty($pass_addr) || empty($pass_uname) || empty($pass_email)) {
        $err = "All fields are required";
    }
    else if(!filter_var($pass_email, FILTER_VALIDATE_EMAIL)) {
        $err = "Invalid email format";
    }
    else if(!preg_match("/^[0-9]{10}$/", $pass_phone)) {
        $err = "Phone number must be 10 digits";
    }
    else {
        // Update passenger details
        $query = "UPDATE obrs_passenger SET 
                 pass_fname=?, 
                 pass_lname=?, 
                 pass_phone=?, 
                 pass_addr=?, 
                 pass_uname=?, 
                 pass_email=? 
                 WHERE pass_id=?";
                 
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('ssssssi', $pass_fname, $pass_lname, $pass_phone, 
                         $pass_addr, $pass_uname, $pass_email, $pass_id);
        
        if($stmt->execute()) {
            $success = "Passenger Account Updated Successfully";
        }
        else {
            $err = "An error occurred. Please try again later";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<!--Head-->
<?php include('assets/inc/head.php');?>
<!--End Head-->
<body style="background-color: #F0F0D7;">
    <div class="be-wrapper be-fixed-sidebar">
        <!--Navigation Bar-->
        <?php include('assets/inc/navbar.php');?>
        <!--End Navigation Bar-->

        <!--Sidebar-->
        <?php include('assets/inc/sidebar.php');?>
        <!--End Sidebar-->
        <div class="be-content">
            <div class="page-head">
                <h2 class="page-head-title" style="color: black;">Update Passenger</h2>
                <nav aria-label="breadcrumb" role="navigation">
                    <ol class="breadcrumb page-head-nav">
                        <li class="breadcrumb-item"><a href="emp-dashboard.php" style="color: black;">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="emp-manage-passengers.php" style="color: black;">Passengers</a></li>
                        <li class="breadcrumb-item active" style="color: black; text-decoration: underline;">Update Passenger</li>
                    </ol>
                </nav>
            </div>

            <?php if(isset($success)) { ?>
                <script>
                    setTimeout(function() { 
                        swal("Success!", "<?php echo $success;?>", "success");
                    }, 100);
                </script>
            <?php } ?>

            <?php if(isset($err)) { ?>
                <script>
                    setTimeout(function() { 
                        swal("Error!", "<?php echo $err;?>", "error");
                    }, 100);
                </script>
            <?php } ?>

            <div class="main-content container-fluid">
                <?php
                $aid = $_GET['pass_id'];
                $ret = "SELECT * FROM obrs_passenger WHERE pass_id=?";
                $stmt = $mysqli->prepare($ret);
                $stmt->bind_param('i', $aid);
                $stmt->execute();
                $res = $stmt->get_result();
                while($row = $res->fetch_object()) {
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card" style="background: #D8E4F8; border-radius: 20px; box-shadow: 0 8px 32px rgba(0,0,0,0.15), 0 4px 8px rgba(0,0,0,0.1); border: 1px solid rgba(0,0,0,0.05);">
                            <div class="card-header card-header-divider" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-bottom: 2px solid rgba(0,0,0,0.05); padding: 25px; border-radius: 20px 20px 0 0;">
                                Update Passenger Profile
                                <span class="card-subtitle" style="color: #2C3E50; font-size: 1.4em; font-weight: 600; letter-spacing: 0.5px;">Fill All Details</span>
                            </div>
                            <div class="card-body" style="padding: 40px;">
                                <form method="POST">
                                    <div class="form-group row" style="margin-bottom: 25px;">
                                        <label class="col-12 col-sm-3 col-form-label text-sm-right" style="color: #34495E; font-weight: 600; font-size: 1.1em;">First Name</label>
                                        <div class="col-12 col-sm-8 col-lg-6">
                                            <input class="form-control" name="pass_fname" value="<?php echo htmlspecialchars($row->pass_fname);?>" type="text" required style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                                        </div>
                                    </div>
                                    <div class="form-group row" style="margin-bottom: 25px;">
                                        <label class="col-12 col-sm-3 col-form-label text-sm-right" style="color: #34495E; font-weight: 600; font-size: 1.1em;">Last Name</label>
                                        <div class="col-12 col-sm-8 col-lg-6">
                                            <input class="form-control" name="pass_lname" value="<?php echo htmlspecialchars($row->pass_lname);?>" type="text" required style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                                        </div>
                                    </div>
                                    <div class="form-group row" style="margin-bottom: 25px;">
                                        <label class="col-12 col-sm-3 col-form-label text-sm-right" style="color: #34495E; font-weight: 600; font-size: 1.1em;">Phone Number</label>
                                        <div class="col-12 col-sm-8 col-lg-6">
                                            <input class="form-control" name="pass_phone" value="<?php echo htmlspecialchars($row->pass_phone);?>" type="tel" pattern="[0-9]{10}" maxlength="10" required oninput="this.value=this.value.replace(/[^0-9]/g,'')" placeholder="Enter 10 digit phone number" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                                        </div>
                                    </div>
                                    <div class="form-group row" style="margin-bottom: 25px;">
                                        <label class="col-12 col-sm-3 col-form-label text-sm-right" style="color: #34495E; font-weight: 600; font-size: 1.1em;">Address</label>
                                        <div class="col-12 col-sm-8 col-lg-6">
                                            <input class="form-control" name="pass_addr" value="<?php echo htmlspecialchars($row->pass_addr);?>" type="text" required style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                                        </div>
                                    </div>
                                    <div class="form-group row" style="margin-bottom: 25px;">
                                        <label class="col-12 col-sm-3 col-form-label text-sm-right" style="color: #34495E; font-weight: 600; font-size: 1.1em;">Email</label>
                                        <div class="col-12 col-sm-8 col-lg-6">
                                            <input class="form-control" name="pass_email" value="<?php echo htmlspecialchars($row->pass_email);?>" type="email" required style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                                        </div>
                                    </div>
                                    <div class="form-group row" style="margin-bottom: 25px;">
                                        <label class="col-12 col-sm-3 col-form-label text-sm-right" style="color: #34495E; font-weight: 600; font-size: 1.1em;">Username</label>
                                        <div class="col-12 col-sm-8 col-lg-6">
                                            <input class="form-control" name="pass_uname" value="<?php echo htmlspecialchars($row->pass_uname);?>" type="text" required style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <p class="text-center" style="margin-top: 30px;">
                                            <button class="btn btn-space btn-success" type="submit" name="Create_Profile" style="border-radius: 12px; padding: 12px 30px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2); transition: all 0.3s ease; margin-right: 20px;">
                                                Update Passenger
                                            </button>
                                            <a href="emp-manage-passengers.php" class="btn btn-space btn-danger" style="border-radius: 12px; padding: 12px 30px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2); transition: all 0.3s ease;">
                                                Cancel
                                            </a>
                                        </p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
            <!--Footer-->
            <?php include('assets/inc/footer.php');?>
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
    <script src="https://kit.fontawesome.com/f766ed9c4c.js" crossorigin="anonymous"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            App.init();
            App.formElements();
        });
    </script>
</body>
</html>