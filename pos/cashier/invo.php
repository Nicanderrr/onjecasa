
	<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');


if (isset($_POST["submit"])) {
	if (isset($_POST["submit"])) {
		$order_code = $_POST["order_code"];
		$cname = $_POST["cname"];
		$grand_total = mysqli_real_escape_string($con, $_POST["grand_total"]);

		$sql = "insert into invoice (cname,order_code,GRAND_TOTAL) values ('{$cname}','{$order_code}','{$grand_total}') ";
		if ($con->query($sql)) {
			$sid = $_POST['SID'];

			$sql2 = "insert into invoice_products (SID,PNAME,PRICE,QTY,TOTAL) values ";
			$rows = [];
			for ($i = 0; $i < count($_POST["pname"]); $i++) {
				$pname = mysqli_real_escape_string($con, $_POST["pname"][$i]);
				$price = mysqli_real_escape_string($con, $_POST["price"][$i]);
				$qty = mysqli_real_escape_string($con, $_POST["qty"][$i]);
				$total = mysqli_real_escape_string($con, $_POST["total"][$i]);
				$rows[] = "('{$sid}','{$pname}','{$price}','{$qty}','{$total}')";
			}
			$sql2 .= implode(",", $rows);
			if ($con->query($sql2)) {
				$success = "Order Submitted" && header("refresh:1; url=pay_order.php?SID=$sid&pay=$grand_total&order_status=Paid");
		
			} else {
				echo "<div class='alert alert-danger'>Invoice Added Failed.</div>";
			}
		} else {
			echo "<div class='alert alert-danger'>Invoice Added Failed.</div>";
		}
	}
}
		

check_login();


require_once('partials/_head.php');
?>
<html>

	<body>





<head>
 
</head>
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
	<script src="https://code.jquery.com/jquery-3.6.0.min.js" ></script>
		
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
		
		<link rel='stylesheet' href='https://code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css'>
		<script src="https://code.jquery.com/ui/1.13.0-rc.3/jquery-ui.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
		
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" ></script> 

 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script> 


    <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;" class="header  pb-8 pt-5 pt-md-8">
    <span class="mask bg-gradient-dark opacity-8"></span>
      <div class="container-fluid">
        <div class="header-body">
        </div>
      </div>
    </div>
    <!-- Page content -->
    <div class="container-fluid mt--8">
      <!-- Table -->
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
        
		
			<form method='post' action='invo.php'>
				<div class='row'>
					<div class='col-md-6'>
						<h5 class='text-success'>Invoice Details</h5>
						<div class='form-group'>
					
							<label>Order Code</label>
							<input type='text' name='order_code' value="<?php echo $alpha?>-<?php echo $beta?>" required class='form-control'>
						</div>
						
					</div>
					<div class='col-md-6'>
						<h5 class='text-success'>Customer Details</h5>
						<div class='form-group'>
							<label>Name</label>
							<input type='text' name='cname'value = "<?= $customerID; ?>"  required class='form-control'>
						</div>
					
					</div>
				</div>
				<div class='row'>
					<div class='col-md-12'>
						<h5 class='text-success'>Product Details</h5>
						<table class='table'>
							<thead>
								<tr>
									<th>Product</th>
									<th>Price</th>
									<th>Qty</th>
									<th>Total</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody id='product_tbody'>
								<tr>
								<td><select class='form-control selectpicker' data-style="btn-grey" data-dropup-auto='false' data-live-search='true' name='pname[]' id='prodName' onchange='getPrice(this.value)'>
                     		 <option>--Select Products--</option>
									<?php
						  $ret = 'SELECT * FROM  rpos_products';
						  $stmt = $mysqli->prepare($ret);
						  $stmt->execute();
						  $res = $stmt->get_result();
									while ($prod = $res->fetch_object()) {
										?>
								<option name='pname[]'  id='prodName'  class='form-control'> <?php echo $prod->prod_name ?> </option>
									<?php } ?>
								</select>
								</td>
								<input type="hidden" value="<?php echo $alpha?>-<?php echo $beta?>" name="SID">
									<td><input type='text' required name='price[]'  id='priceI' class='form-control price'></td>
									<td><input type='text' required name='qty[]' class='form-control qty'></td>
									<td><input type='text' required name='total[]' class='form-control total'></td>
									<td><input type='button' value='x' class='btn btn-danger btn-sm btn-row-remove'> </td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<td><input type='button' value='+ Add New Sale' class='btn btn-primary btn-sm p-2' id='btn-add-row'></td>
									<td colspan='2' class='text-right'>Total</td>
									<td><input type='text' name='grand_total' id='grand_total' class='form-control' required></td>
								</tr>
							</tfoot>
						</table>
						<hr>
						<input type="submit" name="submit" value="Proceed" class="btn btn-success">
					</div>
				</div>
			
			</form>
		

                   

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
//   require_once('partials/_scripts.php');
  ?>

			<script>
			$(document).ready(function(){ 

				
				$("#btn-add-row").click(function(){
var row="<tr><td><select class='form-control selectpicker' data-live-search='true' data-dropup-auto='false' data-style='btn-grey' name='pname[]' id='prodName' onchange='getPrice(this.value)'><option> --Select Products--</option><?php  $ret = 'SELECT * FROM  rpos_products'; $stmt = $mysqli->prepare($ret); $stmt->execute(); $res = $stmt->get_result();	while ($prod = $res->fetch_object()) {?><option name='pname[]' class='form-control'> <?php echo $prod->prod_name ?> </option><?php } ?></select></td><input type='hidden' value='<?php echo $alpha?>-<?php echo $beta?>' 'name='SID'><td><input type='text' required name='price[]'  id='priceI' class='form-control price'></td><td><input type='text' required name='qty[]' class='form-control qty'></td><td><input type='text' required name='total[]' class='form-control total'></td>	<td><input type='button' value='x' class='btn btn-danger btn-sm btn-row-remove'> </td></tr>";
$("#product_tbody").prepend(row);
	$('.selectpicker').selectpicker('refresh');

});
				
				$("body").on("click",".btn-row-remove",function(){
					
						$(this).closest("tr").remove();
						grand_total();
						$('.selectpicker').selectpicker('refresh');
					
				});
				

				$("body").on(".price",function(){
					var price=Number($(this).val());
					var qty=Number($(this).closest("tr").find(".qty").val());
					$(this).closest("tr").find(".total").val(price*qty);
					grand_total();
				});
				
				$("body").on("keyup",".qty",function(){
					var qty=Number($(this).val());
					var price=Number($(this).closest("tr").find(".price").val());
					$(this).closest("tr").find(".total").val(price*qty);
					grand_total();
				});			
				
				function grand_total(){
					var tot=0;
					$(".total").each(function(){
						tot+=Number($(this).val());
					});
					$("#grand_total").val(tot);
				}
				
				
				
			});

		</script>

	</body>
</html>