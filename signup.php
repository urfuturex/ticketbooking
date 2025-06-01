<?php
session_start();
include('db_connect.php');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = $conn->real_escape_string(trim($_POST['name']));
    $email    = $conn->real_escape_string(trim($_POST['email']));
    $phone    = $conn->real_escape_string(trim($_POST['phone']));
    $password = $_POST['password'];

    // Check if email already exists
    $res = $conn->query("SELECT id FROM users WHERE email = '$email'");
    if ($res && $res->num_rows > 0) {
        $error = 'This email is already registered!';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, email, phone, password_hash, created_at) VALUES ('$name', '$email', '$phone', '$hash', NOW())";
        if ($conn->query($sql)) {
            $success = 'Sign up successful! You can <a href="login.php">log in</a> now.';
        } else {
            $error = 'An error occurred, please try again!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign Up – Ticketer</title>
  <link rel="stylesheet" href="login.css">
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
        <a href="login.php" class="login-btn">Login</a>
      </div>
    </div>
  </header>
  <main>
    <div class="login-center-box">
      <h2 class="login-title">Sign Up</h2>
      <div class="login-desc">Create an account to use Ticketer.</div>
      <div class="login-divider"><span>Sign up for Ticketer</span></div>
      <?php if($error): ?>
        <p class="error"><?= $error ?></p>
      <?php endif; ?>
      <?php if($success): ?>
        <p class="success"><?= $success ?></p>
      <?php endif; ?>
      <form method="post" class="login-form" action="">
        <label>Full Name</label>
        <input type="text" name="name" placeholder="Enter your full name" required>

        <label>Email</label>
        <input type="email" name="email" placeholder="Enter your email" required>

        <label>Phone Number</label>
        <input type="text" name="phone" placeholder="Enter your phone number" required>

        <label>Password</label>
        <input type="password" name="password" placeholder="Enter your password" required>

        <button type="submit" class="btn-primary">Sign Up</button>
      </form>
      <div class="login-footer">
        Already have an account? <a href="login.php">Log in</a>
      </div>
    </div>
  </main>
</body>
</html>
