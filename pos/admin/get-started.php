<?php 
ob_start();
session_start();
include('config/config.php');
include('config/code-generator.php');
include('config/checklogin.php');
check_login();
if(isset($_POST['submit'])){
  $admin_id = $customerID ;
  $admin_name = mysqli_real_escape_string($mysqli, $_POST['admin_name']);
  $admin_email = mysqli_real_escape_string($mysqli, $_POST['admin_email']);
  $admin_password = mysqli_real_escape_string($mysqli, $_POST['admin_password']);
  $repeat_password = mysqli_real_escape_string($mysqli, $_POST['repeat_password']);
  $admin_pincode = mysqli_real_escape_string($mysqli, $_POST['admin_pincode']);
  $repeat_pincode = mysqli_real_escape_string($mysqli, $_POST['repeat_pincode']);
  
  

  if(empty($admin_name) OR empty($admin_email) OR empty($admin_password) OR empty($repeat_password) OR empty($admin_pincode) OR empty($repeat_pincode) ){
    $err = "Blank fields not accepted";
    
  }
  if($admin_password != $repeat_password){
    $err  = "Password does not match";
  }
  if($admin_pincode != $repeat_pincode){
    $err  = "Pincode does not match";
  }
  if(strlen($admin_password) < 8){
    $err = "Password length should 8 or more";
  }
 

else{
{
   //Insert Captured information to a database table
   $postQuery = "INSERT INTO rpos_admin (admin_id,admin_name,admin_email, admin_password, admin_pincode) VALUES(?,?,?,?,?)";
   $postStmt = $mysqli->prepare($postQuery);
   //bind paramaters
   $rc = $postStmt->bind_param('sssss',$admin_id, $admin_name, $admin_email, $admin_password, $admin_pincode);
   $postStmt->execute();
   //declare a varible which will be passed to alert function
   if ($postStmt) {
     $success = "Registered Successfully!" && header("refresh:1; url=index.php");
   } else {
     $err = "Please Try Again Or Try Later";
   }
 }
}
}


  


require_once('partials/_head.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>

    <style>
    body{
    padding:50px;
}
.container{
  max-width: 600px;
    margin:0 auto;
    padding:50px;
    box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
}
.form-group{
    margin-bottom:30px;
}
</style>
<body class="bg-dark">
  <h3 class="text-light text-center m-4" >Register as Administrator at 1POS for free</h3>
  <div class="container bg-light">
  <form action="" method="post">
            <div class="form-group">
              <input type="text" class="form-control" name="admin_name" placeholder="Admin Name:">
            </div>
            <div class="form-group">
                <input type="email" class="form-control" name="admin_email" placeholder="Email:">
            </div>
           
            <div class="form-group">
                <input type="password" class="form-control" name="admin_password" placeholder="Password:">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="repeat_password" placeholder="Confirm Password:">
            </div>
           
            <div class="form-group">
                <input type="password" class="form-control" maxlength="4" name="admin_pincode" placeholder="pincode:">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" maxlength="4" name="repeat_pincode" placeholder="Confirm Pincode:">
            </div>

            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Register" name="submit">
            </div>
        </form>
        <div>
        <div class="py-4" ><p>Already Registered ? <a style="text-decoration: underline; color: blue; "href="index.php">Login Here</a></p></div>
      </div>
    </div>
</body>
<?php 


?>
</html>