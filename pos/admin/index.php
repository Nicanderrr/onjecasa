<?php
session_start();
include('config/config.php');
//login 
if (isset($_POST['login'])) {
  $admi_id = $_POST['admi_id'];
  $admin_email = $_POST['admin_email'];
  $admin_password = ($_POST['admin_password']); //double encrypt to increase security
  $stmt = $mysqli->prepare("SELECT *  FROM   rpos_admin WHERE (admin_email =? AND admin_password =?)"); //sql to log in user
  $stmt->bind_param('ss', $admin_email, $admin_password); //bind fetched parameters
  $stmt->execute(); //execute bind 
  $stmt->bind_result($admin_id,$admin_name, $admin_email, $admin_password, $admin_pincode); //bind result
  $rs = $stmt->fetch();
  $_SESSION['admin_id'] = $admin_id;
  if ($rs) {
    //if its sucessfull

    header("location:admin_pass.php?auth0ixss=$admi_id");
  } else {
    $err = "Incorrect Authentication Credentials ";
  }
}
require_once('partials/_head.php');
?>

<body class="bg-dark">
  <div class="main-content">
    <div class="header bg-gradient-primar py-7">
      <div class="container">
        <div class="header-body text-center mb-7">
          <div class="row justify-content-center">
            <div class="col-lg-5 col-md-6">
              <h1 class="text-white">   POS SYSTEM</h1>
                <h2 style="color:rgb(114,250,250)  ">Login As Administrator</h2>
            </div>

          </div>
        </div>
      </div>
    </div>
    <!-- Page content -->
    <div class="container mt--8 pb-5">
      <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
          <div class="card bg-secondary shadow border-0">
            <div class="card-body px-lg-5 py-lg-5">
              <form method="post" role="form">
                <div class="form-group mb-3">
                  <div class="input-group input-group-alternative">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                    </div>
                    <input class="form-control" required name="admin_email" placeholder="Email" id="a_email" type="email">
                  </div>
                </div>
                    <input class="form-control" required name="admi_id" value="<?php?>" type="hidden">

                <div class="form-group">
                  <div class="input-group input-group-alternative">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                    </div>
                    <input class="form-control" required name="admin_password" placeholder="Password" type="password">
                  </div>
                </div>
                <div class="custom-control custom-control-alternative custom-checkbox">
                  <input class="custom-control-input" id=" customCheckLogin" type="checkbox">
                  <label class="custom-control-label" for=" customCheckLogin">
                    <span class="text-muted">Remember Me</span>
                  </label>
                </div>
                <div class="text-center">
                  <button type="submit" name="login" class="btn btn-primary my-4">Log In</button>
                </div>
              
              </form>

            </div>
          </div>
          <div class="row mt-3">
            <div class="col-6">
              <!-- <a href="forgot_pwd.php" class="text-light"><small>Forgot password?</small></a> -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Footer -->
  <?php
  require_once('partials/_footer.php');
  ?>
  <!-- Argon Scripts -->
  <?php
  require_once('partials/_scripts.php');
  ?>
</body>
<script type="text/javascript">
  (function(d, t) {
      var v = d.createElement(t), s = d.getElementsByTagName(t)[0];
      v.onload = function() {
        window.voiceflow.chat.load({
          verify: { projectID: '67454a4a8d43f36981214fc6' },
          url: 'https://general-runtime.voiceflow.com',
          versionID: 'production',
          voice: {
            url: "https://runtime-api.voiceflow.com"
          }
        });
      }
      v.src = "https://cdn.voiceflow.com/widget-next/bundle.mjs"; v.type = "text/javascript"; s.parentNode.insertBefore(v, s);
  })(document, 'script');
</script>

</html>