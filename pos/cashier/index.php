<?php require_once("partials\loader.php") ?>

<?php
session_start();
include('config/config.php');
//login 
if (isset($_POST['pincode'])) {

  $staff_pincode = mysqli_real_escape_string($mysqli,$_POST['staff_pincode']); //double encrypt to increase security
  $stmt = $mysqli->prepare("SELECT  staff_pincode, staff_id  FROM   rpos_staff WHERE ( staff_pincode =?)"); //sql to log in user
  $stmt->bind_param('s', $staff_pincode); //bind fetched parameters
  $stmt->execute(); //execute bind 
  $stmt->bind_result( $staff_pincode, $staff_id); //bind result
  $rs = $stmt->fetch();
  $_SESSION['staff_id'] = $staff_id;
  if ($rs) {
    //if its sucessfull
    header("location:invo.php");
  } else {
    $err = "Incorrect Authentication Credentials ";
  }
}
require_once('partials/_head.php');
?>

<body class="bg-dark">
  <div class="main-content">

    <!-- Page content -->
  
<style>
    html,
        body {
            background-color: #000;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
            overflow-y: hidden;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
            margin-bottom: 80px;

        }
      

        .title {
            font-size: 64px;
            color: rgb(114, 250, 250);
        }

        .m-b-md {
            margin-bottom: 30px;
        }
        .card{
            padding: 20px 30px;
            padding-top: 29px;
        }
        .ad{
            background:blue ;
            border: none;
            outline: none;
            
        }
        div > h2{
         position: relative;
         top: 100px;   
        }
</style>
</head>
   <body>
    
<div >
    <h2 class="text-center text-light" >
        Enter Code to Access Cashier Dashboard
    </h2>
</div>
    <div class="flex-center position-ref full-height">
        <div class="content card">
  
        <form action="index.php" method="post" >
        <span class="input-group-text"><i class="ni ni-lock-circle-open"></i>
          
          <input style="border: none;  font-size: large;"  type="password"  name="staff_pincode" maxlength="4" class="form-control" id="pincode" placeholder="Enter 4-digit code" required/>
          </span>
<div>
<button class="btn btn-primary btn-block my-3" type="sumbit" name="pincode">Enter</button>
</div>
</form> 

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
  <script>
  let pin =  document.getElementById("pincode");
   document.addEventListener("DOMContentLoaded", () =>{
pin.focus();
   }) 
  </script>
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