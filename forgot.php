<?php
session_start();
include 'db_connect.php';

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $r = $conn->query("SELECT id FROM users WHERE email='$email'");
    if ($r->num_rows === 1) {
        $u = $r->fetch_assoc();
        $token   = bin2hex(random_bytes(16));
        $exp     = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Save to password_resets
        $stmt = $conn->prepare(
          "INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)"
        );
        $stmt->bind_param('iss', $u['id'], $token, $exp);
        $stmt->execute();

        // Send email (configure mail server first)
        $link = "https://your-domain.com/reset_password.php?token=$token";
        $sub  = "Reset your Ticketer password";
        $msg  = "Click here to reset your password:\n$link\n(This link is valid for 1 hour)";
        mail($email, $sub, $msg, "From: no-reply@your-domain.com");

        $success = 'We have sent instructions to your email.';
    } else {
        $error = 'This email was not found in our system.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password – Ticketer</title>
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
      <div class="nav-right"></div>
    </div>
  </header>
  <main>
    <div class="login-center-box">
      <h2 class="login-title">Forgot Password</h2>
      <div class="login-desc">Enter your email to receive a password reset link.</div>
      <div class="login-divider"><span>Password Recovery</span></div>
      <?php if($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
      <?php if($success): ?><p class="success"><?= $success ?></p><?php endif; ?>

      <?php if(!$success): ?>
      <form method="post" class="login-form">
        <label>Email</label>
        <input type="email" name="email" placeholder="Enter your email address" required>
        <button type="submit" class="btn-primary">Send Reset Link</button>
      </form>
      <?php endif; ?>
      <div class="login-footer">
        <a href="login.php">Back to login</a>
      </div>
    </div>
  </main>
</body>
</html>
