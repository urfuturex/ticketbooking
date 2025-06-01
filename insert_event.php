<?php
include 'db_connect.php';

// Lấy dữ liệu từ form
$name     = $_POST['event_name'];
$date     = $_POST['event_date'];
$location = $_POST['location'];
$price    = $_POST['price'];

// Lấy tên ảnh và thư mục lưu
$image_name = basename($_FILES['image_file']['name']);
$target_dir = "image/";
$target_file = $target_dir . $image_name;

// Kiểm tra upload file thành công
if (move_uploaded_file($_FILES['image_file']['tmp_name'], $target_file)) {
    // Nếu thành công, insert dữ liệu vào bảng
    $sql = "INSERT INTO events (event_name, event_date, location, price, image_name)
            VALUES ('$name', '$date', '$location', $price, '$image_name')";

    if ($conn->query($sql) === TRUE) {
        echo "🎉 New event added successfully!";
    } else {
        echo "❌ SQL Error: " . $conn->error;
    }
} else {
    echo "❌ Failed to upload image file.";
}

$conn->close();
?>