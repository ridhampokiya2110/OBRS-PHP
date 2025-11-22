<!-- Server side code for log in-->
<?php
session_start();
include('assets/inc/config.php'); //get configuration file
if (isset($_POST['emp_login'])) {
  $admin_email = $_POST['admin_email'];
  $admin_pwd = sha1(md5($_POST['admin_pwd'])); //double encrypt to increase security
  $stmt = $mysqli->prepare("SELECT admin_email ,admin_pwd , admin_id FROM obrs_admin WHERE (admin_email=? OR admin_uname=?) and admin_pwd=? "); //sql to log in user
  $stmt->bind_param('sss', $admin_email, $admin_email, $admin_pwd); //bind fetched parameters
  $stmt->execute(); //execute bind
  $stmt->bind_result($admin_email, $admin_pwd, $admin_id); //bind result
  $rs = $stmt->fetch();
  $_SESSION['admin_id'] = $admin_id; //assaign session to admin id
  
  if ($rs) { //if its sucessfull
    if(isset($_POST['remember_me'])) {
      // Set cookies that expire in 30 days
      setcookie('admin_email', $_POST['admin_email'], time() + (86400 * 1), "/");
      setcookie('admin_pwd', $_POST['admin_pwd'], time() + (86400 * 1), "/"); 
    }
    $success = "Login Successful! Redirecting...";
    header("refresh:2;url=emp-dashboard.php");
  } else {
    $error = "Access Denied Please Check Your Credentials";
  }
}

// Check if cookies exist and pre-fill the form
$saved_email = isset($_COOKIE['admin_email']) ? $_COOKIE['admin_email'] : '';
$saved_pwd = isset($_COOKIE['admin_pwd']) ? $_COOKIE['admin_pwd'] : '';
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

    .swal2-popup {
      font-family: 'Poppins', sans-serif !important;
      border-radius: 20px !important;
      background: rgba(255, 255, 255, 0.95) !important;
      backdrop-filter: blur(10px) !important;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1) !important;
    }

    .swal2-title {
      font-size: 1.5rem !important;
      font-weight: 600 !important;
      color: #1e40af !important;
    }

    .swal2-html-container {
      font-size: 1rem !important;
      color: #4b5563 !important;
    }

    .swal2-confirm {
      background: #34d399 !important;
      border-radius: 9999px !important;
      padding: 12px 32px !important;
      font-weight: 500 !important;
      transition: all 0.2s !important;
    }

    .swal2-confirm:hover {
      background: #10b981 !important;
      transform: translateY(-2px) !important;
    }
  </style>
  <!--Trigger Sweet Alert-->
  <?php if (isset($error)) { ?>
    <script>
      setTimeout(function() {
        Swal.fire({
          icon: 'error',
          title: 'Authentication Failed',
          text: '<?php echo $error; ?>',
          showConfirmButton: true,
          confirmButtonText: 'Try Again',
          timer: 3000,
          timerProgressBar: true,
          customClass: {
            popup: 'animate__animated animate__fadeInDown',
            title: 'text-xl font-bold text-red-600',
            confirmButton: 'bg-emerald-400 hover:bg-emerald-500 text-white font-medium px-6 py-2 rounded-full transition duration-200'
          },
          showClass: {
            popup: 'animate__animated animate__fadeInDown'
          },
          hideClass: {
            popup: 'animate__animated animate__fadeOutUp'
          }
        });
      }, 100);
    </script>
  <?php } ?>
  <?php if (isset($success)) { ?>
    <script>
      setTimeout(function() {
        Swal.fire({
          icon: 'success',
          title: 'Welcome Back!',
          text: '<?php echo $success; ?>',
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true,
          customClass: {
            popup: 'animate__animated animate__fadeInDown',
            title: 'text-xl font-bold text-emerald-600'
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
</head>

<body class="min-h-screen bg-gradient-to-br from-emerald-400 to-blue-600 font-[Poppins]">
  <div class="flex items-center justify-center min-h-screen p-6">
    <div class="w-full max-w-md">
      <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-2xl overflow-hidden">
        <div class="p-8 text-center">
          <img class="mx-auto h-20 rounded-[5px]" src="assets/img/buszy-logo-xl.png" alt="logo">
          <h2 class="mt-4 text-lg font-semibold text-blue-800">Admin Login Portal</h2>
        </div>

        <div class="p-8 pt-0">
          <form method="POST">
            <div class="space-y-6">
              <div>
                <input 
                  class="w-full px-6 py-3 rounded-full border-2 border-gray-200 focus:border-emerald-400 focus:ring focus:ring-emerald-200 transition duration-200 outline-none"
                  name="admin_email" 
                  type="text" 
                  placeholder="Enter your email or username"
                  value="<?php echo $saved_email; ?>"
                  autocomplete="off">
              </div>

              <div class="relative">
                <input 
                  class="w-full px-6 py-3 rounded-full border-2 border-gray-200 focus:border-emerald-400 focus:ring focus:ring-emerald-200 transition duration-200 outline-none"
                  name="admin_pwd" 
                  type="password" 
                  id="password"
                  value="<?php echo $saved_pwd; ?>"
                  placeholder="Enter your password">
                <button 
                  type="button"
                  onclick="togglePassword()"
                  class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                  <i id="eyeIcon" class="fas fa-eye"></i>
                </button>
              </div>

              <div class="flex items-center justify-between">
                <label class="flex items-center text-sm text-gray-600">
                  <input type="checkbox" name="remember_me" class="w-4 h-4 rounded border-gray-300 text-emerald-400 focus:ring-emerald-400" <?php echo ($saved_email && $saved_pwd) ? 'checked' : ''; ?>>
                  <span class="ml-2">Remember Me</span>
                </label>
              </div>

              <div class="flex justify-center">
                <button type="submit" 
                        name="emp_login"
                        class="px-20 py-3 rounded-full bg-emerald-400 hover:bg-emerald-500 text-white font-medium transition duration-200 transform hover:-translate-y-0.5">
                  Log In
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class="mt-6 text-center">
        <a href="../index.php" class="text-white hover:underline font-medium">Back to Home</a>
      </div>
    </div>
  </div>

  <script src="assets/js/swal.js"></script>
  <script
        src="https://kit.fontawesome.com/f766ed9c4c.js"
        crossorigin="anonymous"></script>
  <script>
    function togglePassword() {
      const passwordInput = document.getElementById('password');
      const eyeIcon = document.getElementById('eyeIcon');
      
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