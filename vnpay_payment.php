<?php
// filepath: /Applications/XAMPP/xamppfiles/htdocs/ticket_booking_php/vnpay_payment.php

// Đặt timezone chuẩn Việt Nam
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Cấu hình VNPAY
$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
$vnp_Returnurl = "http://localhost/ticket_booking_php/get_ticket.php"; // Đổi lại đúng đường dẫn của bạn
$vnp_TmnCode = "CJ67DUOI"; // Mã website tại VNPAY
$vnp_HashSecret = "N1PB3OMM43Q5J42JUDRZ7LWCGTKRQAG6"; // Chuỗi bí mật

// Lấy số tiền từ form POST
if (!isset($_POST['total_vnpay'])) {
    die('Không nhận được số tiền!');
}
$total_vnpay = intval($_POST['total_vnpay']);
if ($total_vnpay < 5000 || $total_vnpay >= 1000000000) {
    die('Số tiền không hợp lệ! Giá trị nhận được: ' . $total_vnpay);
}
$vnp_Amount = $total_vnpay * 100; // VNPAY yêu cầu nhân 100

// Tạo mã giao dịch và thời gian
$vnp_TxnRef = time() . rand(1000,9999); // Mã đơn hàng duy nhất
$vnp_OrderInfo = 'Thanh toán đơn hàng vé sự kiện';
$vnp_OrderType = 'billpayment';
$vnp_Locale = 'vn';
$vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
$vnp_CreateDate = date('YmdHis');

// Tạo mảng dữ liệu gửi sang VNPAY
$inputData = array(
    "vnp_Version" => "2.1.0",
    "vnp_TmnCode" => $vnp_TmnCode,
    "vnp_Amount" => $vnp_Amount,
    "vnp_Command" => "pay",
    "vnp_CreateDate" => $vnp_CreateDate,
    "vnp_CurrCode" => "VND",
    "vnp_IpAddr" => $vnp_IpAddr,
    "vnp_Locale" => $vnp_Locale,
    "vnp_OrderInfo" => $vnp_OrderInfo,
    "vnp_OrderType" => $vnp_OrderType,
    "vnp_ReturnUrl" => $vnp_Returnurl,
    "vnp_TxnRef" => $vnp_TxnRef,
);

// Sắp xếp dữ liệu và tạo chuỗi hash
ksort($inputData);
$query = "";
$i = 0;
$hashdata = "";
foreach ($inputData as $key => $value) {
    if ($i == 1) {
        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
    } else {
        $hashdata .= urlencode($key) . "=" . urlencode($value);
        $i = 1;
    }
    $query .= urlencode($key) . "=" . urlencode($value) . '&';
}

// Tạo URL thanh toán
$vnp_Url = $vnp_Url . "?" . $query;
if (isset($vnp_HashSecret)) {
    $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
    $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
}

// Lưu thông tin order vào session
session_start();
// Khi nhận POST từ form
include('db_connect.php');
$eventId = intval($_POST['event_id']);
$res = $conn->query("SELECT * FROM events WHERE id = $eventId");
$event = $res->fetch_assoc();

$_SESSION['order'] = [
    'event'    => $event['event_name'],
    'quantity' => intval($_POST['quantity']),
    'amount'   => intval($_POST['amount']),
    'section'  => $_POST['section'],
    'seats'    => explode(',', $_POST['seats']),
    'date'     => $event['event_date'],
    'time'     => $_POST['time'] ?? '',
    'image'    => $event['image_name']
];

// Chuyển hướng sang cổng thanh toán VNPAY
header('Location: ' . $vnp_Url);
exit;