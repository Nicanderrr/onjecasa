<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();





 //declare pin from form

if (isset($_POST['pincode'])) {
  $admin_pin = (($_POST['admin_pincode']));

//search into db
$sql = "SELECT admin_id FROM rpos_admin WHERE admin_pincode = ?";
//condition and implementation
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('s', $admin_pin);
$stmt->execute();
$stmt->store_result();
if($stmt->num_rows > 0){
  header("location:dashboard.php");
  exit;
} else {
  $err = "Incorrect Authentication Credentials ";
}
//   if ($rs) {
 
//   }
}
$adminName = 'Admin';
$adminId = $_SESSION['admin_id'] ?? null;
if ($adminId) {
  $nameStmt = $mysqli->prepare("SELECT admin_name FROM rpos_admin WHERE admin_id = ? LIMIT 1");
  $nameStmt->bind_param('s', $adminId);
  $nameStmt->execute();
  $nameStmt->bind_result($fetchedAdminName);
  if ($nameStmt->fetch()) {
    $adminName = $fetchedAdminName;
  }
  $nameStmt->close();
}
require_once('partials/_head.php');
?>

<body class="bg-dark pos-auth-body">
  <div id="bootOverlay" class="pos-boot-overlay">Verifying Admin Clearance...</div>
  <div class="pos-auth-bg"></div>
  <div class="pos-auth-gradient"></div>
  <div class="pos-auth-scanline"></div>
  <div id="posParticles"></div>

  <div class="main-content">
    <main class="pos-auth-container">
      <section class="pos-auth-shell">
        <div class="pos-logo-container" aria-hidden="true">POS</div>

        <h1 class="pos-auth-title">PIN <span>Check</span></h1>
        <p class="pos-auth-subtitle">Welcome back, <?php echo htmlspecialchars($adminName); ?></p>

        <div class="pos-auth-glass">
          <h2 class="pos-auth-form-title">Admin Access Code</h2>
          <p class="pos-auth-form-mini">Enter your 4 digit PIN to open the dashboard</p>

          <?php if (isset($err)) { ?>
            <div class="pos-auth-error"><?php echo htmlspecialchars($err); ?></div>
          <?php } ?>

          <form action="admin_pass.php" method="post" role="form">
            <div class="pos-auth-field">
              <label class="pos-auth-label" for="pincode">Security PIN</label>
              <input class="pos-auth-input pos-pin-input" type="password" name="admin_pincode" maxlength="4" inputmode="numeric" pattern="[0-9]{4}" id="pincode" placeholder="0000" autocomplete="one-time-code" required autofocus>
            </div>

            <button class="pos-auth-button" type="submit" name="pincode">Enter Dashboard</button>
          </form>
        </div>

        <div class="pos-auth-footer">
          Two-step admin verification.
        </div>
      </section>
    </main>
  </div>

  <?php
  require_once('partials/_footer.php');
  require_once('partials/_scripts.php');
  ?>

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
  });
  </script>

</body>

</html>
