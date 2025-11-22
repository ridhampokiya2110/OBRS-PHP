<!-- Server side code to handle passenger sign up-->
<?php
	session_start();
	include('assets/inc/config.php');
		if(isset($_POST['pass_register']))
		{
			$pass_fname=$_POST['pass_fname'];
			#$mname=$_POST['mname'];
			$pass_lname=$_POST['pass_lname'];
			$pass_phone=$_POST['pass_phone'];
			$pass_addr=$_POST['pass_addr'];
			$pass_uname=$_POST['pass_uname'];
			$pass_email=$_POST['pass_email'];
			$pass_pwd=$_POST['pass_pwd'];
			$confirm_pwd=$_POST['confirm_pwd'];

			if($pass_pwd != $confirm_pwd) {
				$err = "Passwords do not match";
			} else {
				$pass_pwd=sha1(md5($_POST['pass_pwd']));
				//sql to insert captured values
				$query="insert into obrs_passenger (pass_fname, pass_lname, pass_phone, pass_addr, pass_uname, pass_email, pass_pwd) values(?,?,?,?,?,?,?)";
				$stmt = $mysqli->prepare($query);
				$rc=$stmt->bind_param('sssssss',$pass_fname, $pass_lname, $pass_phone, $pass_addr, $pass_uname, $pass_email, $pass_pwd);
				$stmt->execute();

				if($stmt)
				{
					$success = "Created Account Proceed To Log In";
					header("refresh:2;url=pass-login.php"); // Redirect to login page after 2 seconds
				}
				else {
					$err = "Please Try Again Or Try Later";
				}
			}
		}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="shortcut icon" href="assets/img/favicon.ico">
  <title>Online Bus Ticket Reservation System</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  <style>
    /* Hide scrollbar for Chrome, Safari and Opera */
    ::-webkit-scrollbar {
      display: none;
    }
    
    /* Hide scrollbar for IE, Edge and Firefox */
    * {
      -ms-overflow-style: none;  /* IE and Edge */
      scrollbar-width: none;  /* Firefox */
    }
  </style>
  <?php if(isset($success)) { ?>
    <script>
      setTimeout(function() {
        Swal.fire({
          icon: 'success',
          title: 'Registration Successful!',
          text: '<?php echo $success; ?>',
          showConfirmButton: true,
          timer: 3000,
          timerProgressBar: true,
          confirmButtonText: 'Continue to Login',
          customClass: {
            popup: 'animate__animated animate__fadeInDown',
            title: 'text-xl font-bold text-emerald-600',
            confirmButton: 'bg-emerald-400 hover:bg-emerald-500 text-white font-medium px-6 py-2 rounded-full transition duration-200'
          },
          showClass: {
            popup: 'animate__animated animate__fadeInDown'
          },
          hideClass: {
            popup: 'animate__animated animate__fadeOutUp'
          },
          didOpen: () => {
            Swal.showLoading();
            const successIcon = Swal.getIcon();
            successIcon.classList.add('animate__animated', 'animate__bounceIn');
          }
        });
      }, 100);
    </script>
  <?php } ?>
  <?php if(isset($err)) { ?>
    <script>
      setTimeout(function() {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: '<?php echo $err; ?>',
          showConfirmButton: true,
          confirmButtonText: 'Try Again',
          customClass: {
            popup: 'animate__animated animate__fadeInDown',
            title: 'text-xl font-bold text-red-600',
            confirmButton: 'bg-red-500 hover:bg-red-600 text-white font-medium px-6 py-2 rounded-full transition duration-200'
          },
          showClass: {
            popup: 'animate__animated animate__fadeInDown'
          },
          hideClass: {
            popup: 'animate__animated animate__fadeOutUp'
          },
          didOpen: () => {
            const errorIcon = Swal.getIcon();
            errorIcon.classList.add('animate__animated', 'animate__headShake');
          }
        });
      }, 100);
    </script>
  <?php } ?>
</head>

<body class="min-h-screen bg-gradient-to-br from-emerald-400 to-blue-600 font-[Poppins]">
  <div class="flex items-center justify-center min-h-screen p-6">
    <div class="w-full max-w-xl">
      <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-2xl overflow-hidden">
        <div class="p-8 text-center">
          <img class="mx-auto h-20 rounded-[5px]" src="assets/img/buszy-logo-xl.png" alt="logo">
          <h2 class="mt-4 text-lg font-semibold text-blue-800">Create your account</h2>
        </div>

        <div class="p-8 pt-0">
          <form method="POST">
            <div class="grid grid-cols-2 gap-6">
              <div>
                <input 
                  class="w-full px-6 py-3 rounded-full border-2 border-gray-200 focus:border-emerald-400 focus:ring focus:ring-emerald-200 transition duration-200 outline-none"
                  name="pass_fname"
                  type="text"
                  placeholder="First Name"
                  autocomplete="off">
              </div>

              <div>
                <input 
                  class="w-full px-6 py-3 rounded-full border-2 border-gray-200 focus:border-emerald-400 focus:ring focus:ring-emerald-200 transition duration-200 outline-none"
                  name="pass_lname"
                  type="text"
                  placeholder="Last Name"
                  autocomplete="off">
              </div>

              <div>
                <input 
                  class="w-full px-6 py-3 rounded-full border-2 border-gray-200 focus:border-emerald-400 focus:ring focus:ring-emerald-200 transition duration-200 outline-none"
                  name="pass_phone"
                  type="tel"
                  pattern="[0-9]{10}"
                  maxlength="10"
                  placeholder="Phone Number (10 digits)"
                  autocomplete="off"
                  oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
              </div>

              <div>
                <input 
                  class="w-full px-6 py-3 rounded-full border-2 border-gray-200 focus:border-emerald-400 focus:ring focus:ring-emerald-200 transition duration-200 outline-none"
                  name="pass_addr"
                  type="text"
                  placeholder="Address"
                  autocomplete="off">
              </div>

              <div>
                <input 
                  class="w-full px-6 py-3 rounded-full border-2 border-gray-200 focus:border-emerald-400 focus:ring focus:ring-emerald-200 transition duration-200 outline-none"
                  name="pass_uname"
                  type="text"
                  placeholder="Username"
                  autocomplete="off">
              </div>

              <div>
                <input 
                  class="w-full px-6 py-3 rounded-full border-2 border-gray-200 focus:border-emerald-400 focus:ring focus:ring-emerald-200 transition duration-200 outline-none"
                  name="pass_email"
                  type="email"
                  placeholder="Email Address"
                  autocomplete="off">
              </div>

              <div class="relative">
                <input 
                  class="w-full px-6 py-3 rounded-full border-2 border-gray-200 focus:border-emerald-400 focus:ring focus:ring-emerald-200 transition duration-200 outline-none"
                  name="pass_pwd"
                  type="password"
                  id="password"
                  placeholder="Password">
              </div>

              <div class="relative">
                <input 
                  class="w-full px-6 py-3 rounded-full border-2 border-gray-200 focus:border-emerald-400 focus:ring focus:ring-emerald-200 transition duration-200 outline-none"
                  name="confirm_pwd"
                  type="password"
                  id="confirm_password"
                  placeholder="Confirm Password">
                <button 
                  type="button"
                  onclick="togglePassword('confirm_password', 'eyeIconConfirm')"
                  class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                  <i id="eyeIconConfirm" class="fas fa-eye"></i>
                </button>
              </div>
            </div>

            <div class="mt-4 mb-4 flex items-center justify-center gap-4">
              <button type="submit" 
                      name="pass_register"
                      class="w-1/2 px-8 py-3 rounded-full bg-emerald-400 hover:bg-emerald-500 text-white font-medium transition duration-200">
                Register
              </button>
            </div>
            <p class="mt-2 text-sm text-center text-gray-600">Already have an account? <a href="pass-login.php" class="text-emerald-500 hover:text-emerald-600 font-medium">Log in</a></p>
          </form>
        </div>
      </div>

      <div class="mt-6 text-center">
        <a href="index.php" class="text-white hover:underline font-medium">Back to Home</a>
      </div>
    </div>
  </div>

  <script src="assets/js/swal.js"></script>
  <script
        src="https://kit.fontawesome.com/f766ed9c4c.js"
        crossorigin="anonymous"></script>
  <script>
    function togglePassword(inputId, iconId) {
      const passwordInput = document.getElementById(inputId);
      const eyeIcon = document.getElementById(iconId);
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
      } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
      }
    }
  </script>
</body>

</html>