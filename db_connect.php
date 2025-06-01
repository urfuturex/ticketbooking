<?php
$servername = "localhost";
$username = "root";
$password = ""; // Để trống nếu dùng XAMPP mặc định
$dbname = "ticket_booking"; // Tên database bạn vừa tạo

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
// echo "✅ Connected successfully!";
?>

