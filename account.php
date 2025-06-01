<?php
session_start();
include('db_connect.php');
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';

// Get current user info
$sql = "SELECT name, email, phone FROM users WHERE id = $user_id";
$res = $conn->query($sql);
if ($res && $res->num_rows === 1) {
    $user = $res->fetch_assoc();
} else {
    die("Account not found!");
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $phone = $conn->real_escape_string(trim($_POST['phone']));

    // Check if email exists for another user
    $check = $conn->query("SELECT id FROM users WHERE email='$email' AND id!=$user_id");
    if ($check && $check->num_rows > 0) {
        $message = '<div class="error">This email is already in use!</div>';
    } else {
        $update = $conn->query("UPDATE users SET name='$name', email='$email', phone='$phone' WHERE id=$user_id");
        if ($update) {
            $message = '<div class="success">Update successful!</div>';
            $user['name'] = $name;
            $user['email'] = $email;
            $user['phone'] = $phone;
        } else {
            $message = '<div class="error">An error occurred, please try again!</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Account</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .account-container {
            max-width: 480px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px #eee;
            padding: 32px;
        }
        .account-container h2 {
            text-align: center;
            margin-bottom: 24px;
        }
        .account-container label {
            display: block;
            margin: 18px 0 6px;
            color: #222;
        }
        .account-container input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
        }
        .account-container button {
            margin-top: 24px;
            width: 100%;
            padding: 12px;
            background: #3f51b5;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }
        .success { color: #2e7d32; text-align: center; margin-bottom: 10px; }
        .error { color: #e53935; text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>
    <header>
        <div class="nav-container">
            <div class="nav-left">
                <a href="index.php">Home</a>
                <a href="concert.php">Events</a>
            </div>
            <div class="nav-center">
                <img src="image/logo.png" alt="Logo" class="logo">
            </div>
            <div class="nav-right">
                <a href="logout.php" class="login-btn">Log out</a>
            </div>
        </div>
    </header>
    <div class="account-container">
        <h2>Edit Account</h2>
        <?= $message ?>
        <form method="post">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <label for="phone">Phone Number</label>
            <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>

            <button type="submit">Save changes</button>
        </form>
    </div>
</body>
</html>