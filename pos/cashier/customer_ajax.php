<?php
include('config/pdoconfig.php');

if (!empty($_POST["custName"])) {
    $id = $_POST['custName'];
    $stmt = $DB_con->prepare("SELECT * FROM  rpos_customers WHERE customer_name = :id");
    $stmt->execute(array(':id' => $id));
?>
<?php
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
?>
<?php echo htmlentities($row['customer_id']); ?>
<?php
    }
}


?>
<?php
if (!empty($_POST["prodName"])) {
    $id = $_POST['prodName'];
    $stmt = $DB_con->prepare("SELECT * FROM  rpos_products WHERE prod_name = :id");
    $stmt->execute(array(':id' => $id));


    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        ?>
<?php echo htmlentities($row['prod_price']); ?>
<?php
        }
}?>
<?php
if (!empty($_POST["prodNames"])) {
    $id = $_POST['prodNames'];
    $stmt = $DB_con->prepare("SELECT * FROM  rpos_products WHERE prod_name = :id");
    $stmt->execute(array(':id' => $id));


    while ($row = $stmt->fetch()) {
        ?>
<?php echo htmlentities($row['prod_price']); ?>
<?php
        }
}