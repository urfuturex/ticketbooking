<?php
session_start();
include 'db_connect.php';

$error = '';
if (!isset($_GET['token'])) {
    header('Location: forgot.php');
    exit;
}
$token = $conn->real_escape_string($_GET['token']);

// get record
$stmt = $conn->prepare(
  "SELECT user_id, expires_at FROM password_resets WHERE token=?"
);
$stmt->bind_param('s', $token);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows!==1) {
    $error = 'Invalid token.';
} else {
    $row = $res->fetch_assoc();
    if (strtotime($row['expires_at']) < time()) {
        $error = 'The link has expired.';
    } elseif ($_SERVER['REQUEST_METHOD']==='POST') {
        $p  = $_POST['password'];
        $p2 = $_POST['confirm_password'];
        if ($p !== $p2) {
            $error = 'Passwords do not match.';
        } else {
            $h = password_hash($p, PASSWORD_DEFAULT);
            $upd = $conn->prepare("UPDATE users SET password_hash=? WHERE id=?");
            $upd->bind_param('si', $h, $row['user_id']);
            $upd->execute();

            // delete token
            $del = $conn->prepare("DELETE FROM password_resets WHERE token=?");
            $del->bind_param('s', $token);
            $del->execute();

            header('Location: login.php?reset=1');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password – Ticketer</title>
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
      <h2 class="login-title">Reset Password</h2>
      <div class="login-desc">Enter your new password below.</div>
      <div class="login-divider"><span>Reset your password</span></div>
      <?php if($error): ?><p class="error"><?= $error ?></p><?php endif; ?>

      <?php if(!isset($row) || !$row || strtotime($row['expires_at'])<time()): ?>
        <p>Unable to reset password. Please try again from the beginning.</p>
      <?php else: ?>
      <form method="post" class="login-form">
        <label>New Password</label>
        <input type="password" name="password" placeholder="Enter new password" required>

        <label>Confirm New Password</label>
        <input type="password" name="confirm_password" placeholder="Re-enter new password" required>

        <button type="submit" class="btn-primary">Reset Password</button>
      </form>
      <?php endif; ?>
      <div class="login-footer">
        <a href="login.php">Back to login</a>
      </div>
    </div>
  </main>
</body>
</html>
