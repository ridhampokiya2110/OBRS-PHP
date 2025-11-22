<?php
  session_start();
  include('assets/inc/config.php');
  include('assets/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['admin_id'];
?>
<!--Server side code to handle passenger sign up-->
<?php
		if(isset($_POST['update_profile']))
		{
            $emp_id = $_GET['emp_id'];
            $emp_fname=$_POST['emp_fname'];
            #$mname=$_POST['mname'];
            $emp_lname=$_POST['emp_lname'];
            $emp_nat_idno=$_POST['emp_nat_idno'];
            $emp_phone=$_POST['emp_phone'];
            $emp_addr = $_POST['emp_addr'];
            $emp_uname=$_POST['emp_uname'];
            $emp_email=$_POST['emp_email'];
            $emp_dept=$_POST['emp_dept'];
            $emp_pwd=sha1(md5($_POST['emp_pwd']));
      //sql to insert captured values
			$query="update obrs_employee set emp_fname=?, emp_lname=?, emp_phone=?, emp_addr=?, emp_nat_idno=?, emp_uname=?, emp_email=?, emp_dept=?, emp_pwd=? where emp_id=? ";
			$stmt = $mysqli->prepare($query);
			$rc=$stmt->bind_param('sssssssssi',$emp_fname, $emp_lname, $emp_phone, $emp_addr, $emp_nat_idno, $emp_uname, $emp_email, $emp_dept, $emp_pwd, $emp_id);
			$stmt->execute();
			/*
			*Use Sweet Alerts Instead Of This Fucked Up Javascript Alerts
			*echo"<script>alert('Successfully Created Account Proceed To Log In ');</script>";
			*/ 
			//declare a varible which will be passed to alert function
			if($stmt)
			{
				$success = "Employee Account Updated";
			}
			else {
				$err = "Please Try Again Or Try Later";
			}
			
			
		}
?>
<!--End Server Side-->

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
          <h2 class="page-head-title" style="color: black;">Update Employee</h2>
          <nav aria-label="breadcrumb" role="navigation">
            <ol class="breadcrumb page-head-nav">
              <li class="breadcrumb-item"><a href="pass-dashboard.php" style="color: black;">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="#" style="color: black;">Employee</a></li>
              <li class="breadcrumb-item active" style="color: black; text-decoration: underline;">Update</li>
            </ol>
          </nav>
        </div>
            <?php if(isset($success)) {?>
                                <!--This code for injecting an alert-->
                <script>
                            setTimeout(function () 
                            { 
                                swal("Success!","<?php echo $success;?>!","success");
                            },
                                100);
                </script>

        <?php } ?>
        <?php if(isset($err)) {?>
        <!--This code for injecting an alert-->
                <script>
                            setTimeout(function () 
                            { 
                                swal("Failed!","<?php echo $err;?>!","error");
                            },
                                100);
                </script>

        <?php } ?>
        <div class="main-content container-fluid">
        <!--Employee Instance-->
        <?php
            $aid=$_GET['emp_id'];
            $ret="select * from obrs_employee where emp_id=?";
            $stmt= $mysqli->prepare($ret) ;
            $stmt->bind_param('i',$aid);
            $stmt->execute() ;//ok
            $res=$stmt->get_result();
            //$cnt=1;
            while($row=$res->fetch_object())
        {
        ?>
          <div class="row">
            <div class="col-md-12">
              <div class="card" style="background: #D8E4F8; border-radius: 20px; box-shadow: 0 8px 32px rgba(0,0,0,0.15), 0 4px 8px rgba(0,0,0,0.1); border: 1px solid rgba(0,0,0,0.05);">
                <div class="card-header card-header-divider" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-bottom: 2px solid rgba(0,0,0,0.05); padding: 25px; border-radius: 20px 20px 0 0;">Update Employee Profile<span class="card-subtitle" style="color: #2C3E50; font-size: 1.4em; font-weight: 600; letter-spacing: 0.5px;">Fill All Details</span></div>
                <div class="card-body" style="padding: 40px;">
                  <form method ="POST">
                    <div class="form-group row" style="margin-bottom: 25px;">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;"> First Name</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" name="emp_fname" value="<?php echo $row->emp_fname;?>"  id="inputText3" type="text" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                      </div>
                    </div>
                    <div class="form-group row" style="margin-bottom: 25px;">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;">Last Name</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" name="emp_lname" value="<?php echo $row->emp_lname;?>"  id="inputText3" type="text" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                      </div>
                    </div>
                    <div class="form-group row" style="margin-bottom: 25px;">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;">National ID Number</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" name="emp_nat_idno"  value="<?php echo $row->emp_nat_idno;?>" id="inputText3" type="text" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                      </div>
                    </div>
                    <div class="form-group row" style="margin-bottom: 25px;">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;">Contact Number</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" name="emp_phone" value="<?php echo $row->emp_phone;?>"  id="inputText3" type="text" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                      </div>
                    </div>
                    <div class="form-group row" style="margin-bottom: 25px;">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;">Address</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" name="emp_addr" value="<?php echo $row->emp_addr;?>"  id="inputText3" type="text" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                      </div>
                    </div>
                    <div class="form-group row" style="margin-bottom: 25px;">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;">Department</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" name="emp_dept" value = "<?php echo $row->emp_dept;?>"  id="inputText3" type="text" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                      </div>
                    </div>
                    <div class="form-group row" style="margin-bottom: 25px;">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;">Email</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" name="emp_email" value="<?php echo $row->emp_email;?>" id="inputText3" type="email" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                      </div>
                    </div>
                    <div class="form-group row" style="margin-bottom: 25px;">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;">Username</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" name="emp_uname" value="<?php echo $row->emp_uname;?>"  id="inputText3" type="text" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                      </div>
                    </div>
                    <div class="form-group row" style="margin-bottom: 25px;">
                      <label class="col-12 col-sm-3 col-form-label text-sm-right" for="inputText3" style="color: #34495E; font-weight: 600; font-size: 1.1em;">Password</label>
                      <div class="col-12 col-sm-8 col-lg-6">
                        <input class="form-control" name="emp_pwd" id="inputText3" type="password" style="border-radius: 12px; border: 2px solid #E8EEF4; padding: 12px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <p class="text-center" style="margin-top: 30px;">
                        <input class="btn btn-space btn-success" value ="Update Employee" name = "update_profile" type="submit" style="border-radius: 12px; padding: 12px 30px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2); transition: all 0.3s ease; margin-right: 20px;">
                        <a href="admin-manage-employee.php" class="btn btn-space btn-danger" style="border-radius: 12px; padding: 12px 30px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2); transition: all 0.3s ease;">Cancel</a>
                      </p>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        <!--End Employee Instance-->
            <?php }?>
        
        </div>
        <!--footer-->
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
    <script src="assets/js/swal.js" type="text/javascript"></script>

    <script type="text/javascript">
      $(document).ready(function(){
      	//-initialize the javascript
      	App.init();
      	App.formElements();
      });
    </script>
  </body>

</html>