<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use FPDF;

// Lấy thông tin vé từ session
$order = $_SESSION['order'] ?? null;
$user = $_SESSION['user_info'] ?? null;

if (!$order || !$user) {
    die('Ticket info not found!');
}

// Sinh mã vé ngẫu nhiên (8 ký tự)
$ticket_code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));

// Tạo nội dung QR code
$ticketData = "Ticket Code: $ticket_code\n"
    . "Name: {$user['name']}\n"
    . "Event: {$order['event']}\n"
    . "Section: {$order['section']}\n"
    . "Quantity: {$order['quantity']}\n"
    . "Amount: " . number_format($order['amount'],0,',','.') . " VND\n"
    . "Email: {$user['email']}\n"
    . "Phone: {$user['phone']}";

// Tạo QR
$qrCode = new QrCode($ticketData);

$writer = new PngWriter();
$result = $writer->write($qrCode);

// Lưu QR
$tempQrPath = __DIR__ . '/qr_' . uniqid() . '.png';
$result->saveToFile($tempQrPath);

if (!file_exists($tempQrPath)) {
    die('QR code generation failed: ' . $tempQrPath);
}

// PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Event Ticket', 0, 1, 'C');
$pdf->Ln(10);
if(file_exists($tempQrPath)){
    $pdf->Image($tempQrPath, 75, 40, 60, 60);
} else {
    die('QR image not found: ' . $tempQrPath);
}
$pdf->Output('D', 'ticket_' . $ticket_code . '.pdf');
unlink($tempQrPath);
exit;
