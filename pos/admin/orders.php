<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
if(isset($_POST['add_to_cart'])){
  $prod_name = $_POST['prod_name'];
  $prod_code = $_POST['prod_code'];
  $prod_price = $_POST['prod_price'];
  $prod_img = $_POST['prod_img'];
  $prod_qty = 1;
  $sql = mysqli_query($mysqli, "SELECT * FROM rpos_cart WHERE prod_name = '$prod_name'");
  if(mysqli_num_rows($sql) > 0){
    $err = "Item Already Added to Cart!" && header("refresh:1; url=orders.php");
  }else{
    $newsql = mysqli_query($mysqli, "INSERT INTO rpos_cart (prod_name,prod_code,prod_price,prod_img,prod_qty) VALUES ('$prod_name','$prod_code','$prod_price','$prod_img','$prod_qty')");
    $success = "Item Added Successfully" && header("refresh:1; url=orders.php");

  }

}



require_once('partials/_head.php');
?>

<body>
  <!-- Sidenav -->
  <?php
  require_once('partials/_sidebar.php');
  ?>
  <!-- Main content -->
  <div class="main-content">
    <!-- Top navbar -->
    <?php
    require_once('partials/_topnav.php');
    ?>
    <!-- Header -->
    <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;" class="header  pb-8 pt-5 pt-md-8">
    <span class="mask bg-gradient-dark opacity-8"></span>
      <div class="container-fluid">
        <div class="header-body">
        </div>
      </div>
    </div>
    <style>
      .aa{
        display: flex;
        align-items: center;
        justify-content: space-between;
      }
      .aa button{
        padding: 5px 20px;
       
      }
    </style>
 
    <!-- Page content -->
    <div class="container-fluid mt--8">
      <!-- Table -->
     
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header aa border-0">
              <?php
              $sel = "SELECT * FROM rpos_cart";
              $res = mysqli_query($mysqli, $sel);
              $count = mysqli_num_rows($res);
              ?>
          <span>Select Any Product To Make An Order</span>
   <a class="btn btn-warning" href="cart.php"><i class="fas fa-shopping-cart"></i> <?= $count; ?> </a>
            </div>
            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th scope="col"><b>Image</b></th>
                    <th scope="col"><b>Product Code</b></th>
                    <th scope="col"><b>Name</b></th>
                    <th scope="col"><b>Price</b></th>
                    <th scope="col"><b>Action</b></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $ret = "SELECT * FROM  rpos_products";
                  $stmt = $mysqli->prepare($ret);
                  $stmt->execute();
                  $res = $stmt->get_result();
                  while ($prod = $res->fetch_object()) {
                  ?>
                    <tr>
                      <td>
                        <?php
                        if ($prod->prod_img) {
                          echo "<img src='assets/img/products/" . htmlspecialchars($prod->prod_img) . "' class='table-product-image' alt='" . htmlspecialchars($prod->prod_name) . "'>";
                        } else {
                          echo "<img src='assets/img/products/place.png' class='table-product-image' alt='Product placeholder'>";
                        }

                        ?>

                      </td>
                  
                      <td><?php echo $prod->prod_code; ?></td>
                      <td><?php echo $prod->prod_name; ?></td>
                      <td>₵ <?php echo $prod->prod_price; ?></td>
                      <td>
                         <!-- <a href="make_oder.php?prod_id=<?php echo $prod->prod_id; ?>&prod_name=<?php echo $prod->prod_name; ?>&prod_price=<?php echo $prod->prod_price; ?>">  -->
                         <form action="" method="post">
                         <input type="hidden" name="prod_code" value="<?php echo $prod->prod_code; ?>">
                         <input type="hidden" name="prod_name" value="<?php echo $prod->prod_name; ?>">
                         <input type="hidden" name="prod_price" value="<?php echo $prod->prod_price; ?>">
                         <input type="hidden" name="prod_img" value="<?php echo $prod->prod_img; ?>">
                          <button type="sumbit" name="add_to_cart" class="btn btn-sm btn-warning">
                            <i class="fas fa-cart-plus"></i>
                            Add to cart
                          </button>
                        <!-- </a> -->
                        </form>
                      </td>
                    </tr>
                  <?php } ?>
           
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Footer -->
      <?php
      require_once('partials/_footer.php');
      ?>
    </div>
  </div>
  <!-- Argon Scripts -->
  <?php
  require_once('partials/_scripts.php');
  ?>
</body>

</html>
