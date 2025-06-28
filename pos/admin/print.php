<?php 
ob_start();
session_start();
require ("fpdf/fpdf.php");
require ("word.php");
include('config/config.php');
include('config/checklogin.php');
check_login();

// Initialize all arrays
$info = [
    "customer" => "",
    "invoice_no" => "",
    "invoice_date" => "",
    "total_amt" => "",
    "words" => ""
];

$infoz = [
    "sales_person" => ""
];

$infow = [
    "pay_method" => ""
];

$infoy = [
    "company" => "",
    "address" => "",
    "city" => "",
    "phone" => ""
];

$products_info = [];

// Select Invoice Details From Database
$sql = "SELECT * FROM invoice WHERE order_code = '{$_GET["id"]}'";
$res = $con->query($sql);
if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $info = [
        "customer" => $row["cname"],
        "invoice_no" => $row["order_code"],
        "invoice_date" => date("d-m-y g:i", strtotime($row["created_at"])),
        "total_amt" => number_format($row["GRAND_TOTAL"])
        // "words"=> $obj->get_words(),
    ];
}

// Select Invoice Product Details From Database
$sql = "SELECT * FROM invoice_products WHERE SID = '{$_GET["id"]}'";
$res = $con->query($sql);
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $products_info[] = [
            "name" => $row["PNAME"],
            "price" => $row["PRICE"],
            "qty" => $row["QTY"],
            "total" => number_format($row["TOTAL"])
        ];
    }
}

// SALES ATTENDANT
$ql = "SELECT admin_name FROM rpos_admin;";
$rr = mysqli_query($mysqli, $ql);
if ($rr && mysqli_num_rows($rr) > 0) {
    $row = mysqli_fetch_assoc($rr);
    $infoz["sales_person"] = $row['admin_name'];
}

// PAYMENT METHOD
$ql = "SELECT pay_method FROM rpos_payments WHERE SID = '{$_GET["id"]}'";
$rr = mysqli_query($mysqli, $ql);
if ($rr && mysqli_num_rows($rr) > 0) {
    $row = mysqli_fetch_assoc($rr);
    $infow["pay_method"] = $row['pay_method'];
}

// COMPANY INFO
$ql = "SELECT * FROM company_info";
$rr = mysqli_query($mysqli, $ql);
if ($rr && mysqli_num_rows($rr) > 0) {
    $row = mysqli_fetch_assoc($rr);
    $infoy = [
        "company" => $row['company'],
        "address" => $row['address'],
        "city" => $row['city'],
        "phone" => $row['phone']
    ];
}

class PDF extends FPDF
{
    function Headz($infoy) {
        // Display Company Info
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(50, 10, $infoy["company"], 0, 1);
        $this->SetFont('Arial', '', 14);
        $this->Cell(50, 7, $infoy["address"], 0, 1);
        $this->Cell(50, 7, $infoy["city"], 0, 1);
        $this->Cell(50, 7, $infoy["phone"], 0, 1);
        
        // Display INVOICE text
        $this->SetY(15);
        $this->SetX(-40);
        $this->SetFont('Arial', 'B', 18);
        $this->Cell(50, 10, "RECEIPT", 0, 1);
        
        // Display Horizontal line
        $this->Line(0, 48, 210, 48);
    }
    
    function body($info, $products_info) {
        // Billing Details
        $this->SetY(55);
        $this->SetX(10);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(50, 10, "Bill To: ", 0, 1);
        $this->SetFont('Arial', '', 12);
        $this->Cell(50, 7, $info["customer"], 0, 1);

        // Display Invoice no
        $this->SetY(55);
        $this->SetX(-69);
        $this->Cell(50, 7, "RECEIPT ID : #" . $info["invoice_no"]);
        
        // Display Invoice date
        $this->SetY(63);
        $this->SetX(-69);
        $this->Cell(50, 7, "DATE : " . $info["invoice_date"]);
        
        // Display Table headings
        $this->SetY(95);
        $this->SetX(10);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(80, 9, "ITEMS", 1, 0);
        $this->Cell(40, 9, "PRICE", 1, 0, "C");
        $this->Cell(30, 9, "QTY", 1, 0, "C");
        $this->Cell(40, 9, "TOTAL", 1, 1, "C");
        $this->SetFont('Arial', '', 12);
        
        // Display table product rows
        foreach ($products_info as $row) {
            $this->Cell(80, 9, $row["name"], "LR", 0);
            $this->Cell(40, 9, $row["price"], "R", 0, "R");
            $this->Cell(30, 9, $row["qty"], "R", 0, "C");
            $this->Cell(40, 9, $row["total"], "R", 1, "R");
        }

        // Display table empty rows
        for ($i = 0; $i < 7 - count($products_info); $i++) {
            $this->Cell(80, 9, "", "LR", 0);
            $this->Cell(40, 9, "", "R", 0, "R");
            $this->Cell(30, 9, "", "R", 0, "C");
            $this->Cell(40, 9, "", "R", 1, "R");
        }

        // Display table total row
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(150, 9, "GRAND TOTAL", 1, 0, "R");
        $this->Cell(40, 9, 'GHC ' . $info["total_amt"], 1, 1, "R");

        // Display amount in words
        // $this->SetY(225);
        // $this->SetX(10);
        // $this->SetFont('Arial', 'B', 12);
        // $this->Cell(0, 9, "Amount in Words ", 0, 1);
        // $this->SetFont('Arial', '', 12);
        // $this->Cell(0, 9, $info["words"], 0, 1);
    }

    function bodyz($infoz) {
        // Display Sales person
        $this->SetFont('Arial', 'B', 11);
        $this->SetY(70);
        $this->SetX(-69);
        $this->Cell(50, 7, "SALES PERSON : " . $infoz["sales_person"]);
    }

    function bodyw($infow) {
        // Display Payment method
        $this->SetY(72);
        $this->SetX(10);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(50, 7, "PAYMENT METHOD: " . $infow["pay_method"], 0, 1);
    }

    function Footer() {
        // Uncomment and add footer content as needed
        // set footer position
        // $this->SetY(-50);
        // $this->SetFont('Arial', 'B', 12);
        // $this->Cell(0, 10, "for ABC COMPUTERS", 0, 1, "R");
        // $this->Ln(15);
        // $this->SetFont('Arial', '', 12);
        // $this->Cell(0, 10, "Authorized Signature", 0, 1, "R");
        // $this->SetFont('Arial', '', 10);
        // Display Footer Text
        // $this->Cell(0, 10, "This is a computer generated invoice", 0, 1, "C");
    }
}

// Create A4 Page with Portrait 
$pdf = new PDF("P", "mm", "A4");
$pdf->AddPage();
$pdf->Headz($infoy);
$pdf->body($info, $products_info);
$pdf->bodyz($infoz);
$pdf->bodyw($infow);
$pdf->Output();
ob_end_flush();
?>
