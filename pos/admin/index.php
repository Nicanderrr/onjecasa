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

<body class="bg-dark pos-auth-body">
  <div id="bootOverlay" class="pos-boot-overlay">Initializing POS Command Interface...</div>
  <div class="pos-auth-bg"></div>
  <div class="pos-auth-gradient"></div>
  <div class="pos-auth-scanline"></div>
  <div id="posParticles"></div>

  <div class="main-content">
    <main class="pos-auth-container">
      <section class="pos-auth-shell">
        <div class="pos-logo-container" aria-hidden="true">POS</div>

        <h1 class="pos-auth-title">POS <span>System</span></h1>
        <p class="pos-auth-subtitle">Secure admin console</p>

        <div class="pos-auth-glass">
          <h2 class="pos-auth-form-title">Admin Authentication</h2>
          <p class="pos-auth-form-mini">Authorized users only</p>

          <?php if (isset($err)) { ?>
            <div class="pos-auth-error"><?php echo $err; ?></div>
          <?php } ?>

          <form method="post" role="form">
            <input required name="admi_id" value="<?php?>" type="hidden">

            <div class="pos-auth-field">
              <label class="pos-auth-label" for="a_email">Email Address</label>
              <input class="pos-auth-input" required name="admin_email" placeholder="admin@example.com" id="a_email" type="email" autocomplete="username" autofocus>
            </div>

            <div class="pos-auth-field">
              <label class="pos-auth-label" for="passwordField">Password</label>
              <div class="pos-auth-input-wrap">
                <input class="pos-auth-input" required name="admin_password" placeholder="Enter secure password" id="passwordField" type="password" autocomplete="current-password" style="padding-right: 3rem;">
                <button type="button" id="togglePassword" class="pos-toggle-password" aria-label="Toggle password visibility">
                  <svg id="eyeIconSVG" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                  <svg id="eyeOffIconSVG" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                    <line x1="1" y1="1" x2="23" y2="23"></line>
                  </svg>
                </button>
              </div>
              <p id="capsWarning" class="pos-caps-warning">Caps lock active</p>
            </div>

            <button type="submit" name="login" class="pos-auth-button">Login</button>
          </form>
        </div>

        <div class="pos-auth-footer">
          Inventory, orders, payments, and reports.
        </div>
      </section>
    </main>
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
<script>
document.addEventListener('DOMContentLoaded', function () {
  setTimeout(function () {
    var overlay = document.getElementById('bootOverlay');
    if (!overlay) return;
    overlay.style.opacity = '0';
    setTimeout(function () { overlay.remove(); }, 520);
  }, 760);

  var particlesContainer = document.getElementById('posParticles');
  if (particlesContainer) {
    for (var i = 0; i < 58; i++) {
      var particle = document.createElement('span');
      var size = Math.random() * 3 + 1;
      particle.className = 'pos-auth-particle';
      particle.style.top = Math.random() * window.innerHeight + 'px';
      particle.style.left = Math.random() * window.innerWidth + 'px';
      particle.style.width = size + 'px';
      particle.style.height = size + 'px';
      particle.style.animationDuration = (Math.random() * 5 + 5) + 's';
      particle.style.animationDelay = '-' + (Math.random() * 6) + 's';
      particlesContainer.appendChild(particle);
    }
  }

  var togglePassword = document.getElementById('togglePassword');
  var password = document.getElementById('passwordField');
  var eyeIcon = document.getElementById('eyeIconSVG');
  var eyeOffIcon = document.getElementById('eyeOffIconSVG');
  var capsWarning = document.getElementById('capsWarning');

  if (togglePassword && password) {
    togglePassword.addEventListener('click', function () {
      var nextType = password.type === 'password' ? 'text' : 'password';
      password.type = nextType;
      eyeIcon.style.display = nextType === 'password' ? 'block' : 'none';
      eyeOffIcon.style.display = nextType === 'password' ? 'none' : 'block';
    });

    password.addEventListener('keyup', function (event) {
      if (!capsWarning) return;
      capsWarning.style.display = event.getModifierState('CapsLock') ? 'block' : 'none';
    });
  }
});
</script>
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
