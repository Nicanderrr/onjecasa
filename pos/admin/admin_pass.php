<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();





 //declare pin from form

if (isset($_POST['pincode'])) {
  $admin_pin = (($_POST['admin_pincode']));

//search into db
$sql = "SELECT * FROM rpos_admin WHERE admin_pincode = '$admin_pin'  ";
//condition and implementation
$result = mysqli_query($mysqli, $sql);
if($result){

        $num = mysqli_num_rows($result);
        if($num > 0){
   header("location:dashboard.php");
        }

 else {
  $err = "Incorrect Authentication Credentials ";
}
}
//   if ($rs) {
 
//   }
}
require_once('partials/_head.php');
?>


<!DOCTYPE html>
<html lang="en" >

<head>
  <meta charset="UTF-8">
  <meta charset="UTF-8">
  <title>Four Digit Code</title>


      <link rel="stylesheet" href="css/style.css">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" >
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
    
        div > h2{
         position: relative;
         top: 100px;   
        }
    
</style>
</head>
   <body class="bg-dark">
    
     <h1 class="alert alert-secondary text-center text-dark" > Hello,<span style="text-transform:uppercase ;"> <?php 
    $ql = "SELECT admin_name FROM rpos_admin;";
    $rr = mysqli_query($mysqli,$ql);
    $check = mysqli_num_rows($rr);
    if($check > 0){
        if($row = mysqli_fetch_assoc($rr)){
      echo $row['admin_name'];
    
           
        }
    }
    ?>
    </span>
    </h1> 
   
<div class="wel" >
  
    <h2 class="text-center text-light" >
        Enter Code to Access <span class="text-teal"> Admin dashboard</span>
    </h2>
</div>
    <div class="flex-center position-ref full-height">
        
        <div class="content card">
  
        <form action="admin_pass.php" method="post" >
        <span class="input-group-text bg-light"><i class="ni ni-lock-circle-open"></i>
          <input style=" border:none; font-size: large;" type="password" name="admin_pincode" maxlength="4" class="form-control" id="pincode" placeholder="Enter 4-digit code" required/>
        </span>
<div>
<button class="btn btn-primary btn-block my-3" type="sumbit" name="pincode">Enter</button>
</div>
</form> 

        </div>
    </div>
<script  src="script.js"></script>

</body>

</html>