<?php
require ('../vendor/fpdf/fpdf.php');
require ('../gen_functions/config.php'); 

error_reporting(E_ALL);
ini_set('display_errors', 1);
class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(190, 10, 'User & Subscription Report', 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Fetch Users Data
$pdf->Cell(190, 10, 'Users List', 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 10, 'User ID', 1);
$pdf->Cell(50, 10, 'Name', 1);
$pdf->Cell(60, 10, 'Gmail', 1);
$pdf->Cell(40, 10, 'Role', 1);
$pdf->Ln();

try {
    $stmt = $pdo->query("SELECT user_id, first_name, last_name, gmail, role FROM user_tbl");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $pdf->SetFont('Arial', '', 8);
    foreach ($users as $user) {
        $pdf->Cell(30, 10, $user['user_id'], 1);
        $pdf->Cell(50, 10, $user['first_name'] . ' ' . $user['last_name'], 1);
        $pdf->Cell(60, 10, $user['gmail'], 1);
        $pdf->Cell(40, 10, ucfirst($user['role']), 1);
        $pdf->Ln();
    }
} catch (PDOException $e) {
    die("Error fetching users: " . $e->getMessage());
}

$pdf->Ln(10); 

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(190, 10, 'Subscriptions List', 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 10, 'Sub ID', 1);
$pdf->Cell(30, 10, 'Locker No', 1);
$pdf->Cell(30, 10, 'Start Date', 1);
$pdf->Cell(30, 10, 'End Date', 1);
$pdf->Cell(30, 10, 'Status', 1);
$pdf->Cell(30, 10, 'Amount', 1);
$pdf->Ln();

try {
    $stmt = $pdo->query("SELECT sub_id, locker_no, start_date, end_date, status, amount FROM subscriptions_tbl");
    $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $pdf->SetFont('Arial', '', 10);
    foreach ($subscriptions as $sub) {
        $pdf->Cell(30, 10, $sub['sub_id'], 1);
        $pdf->Cell(30, 10, $sub['locker_no'], 1);
        $pdf->Cell(30, 10, $sub['start_date'], 1);
        $pdf->Cell(30, 10, $sub['end_date'], 1);
        $pdf->Cell(30, 10, $sub['status'], 1);
        $pdf->Cell(30, 10, $sub['amount'], 1);
        $pdf->Ln();
    }
} catch (PDOException $e) {
    die("Error fetching subscriptions: " . $e->getMessage());
}

$pdf->Output();
?>
