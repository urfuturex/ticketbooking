<?php
session_start();
include('db_connect.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $res = $conn->query("SELECT id, password_hash FROM users WHERE email = '$email'");
    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: index.php');
            exit;
        }
    }
    $error = 'Incorrect email or password';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Log In – Ticketer</title>
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
        <button id="adminBtn" style="margin-left:20px;">Admin</button>
      </div>
    </div>
  </header>

  <!-- Popup nhập mật khẩu admin -->
  <div id="adminModal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.3);z-index:999;align-items:center;justify-content:center;">
    <div style="background:#fff;padding:24px 32px;border-radius:8px;min-width:300px;box-shadow:0 2px 12px #0002;position:relative;">
      <h3 style="margin-top:0;">Admin Login</h3>
      <div id="adminError" style="color:red;margin-bottom:8px;display:none;"></div>
      <input type="password" id="adminPass" placeholder="Enter admin password" style="width:100%;padding:8px;margin-bottom:12px;">
      <div style="text-align:right;">
        <button onclick="closeAdminModal()" style="margin-right:10px;">Cancel</button>
        <button onclick="submitAdminPass()">Submit</button>
      </div>
    </div>
  </div>

  <div class="login-center-box">
    <div class="login-title">Log In</div>
    <div class="login-desc">Log in to continue your reservation.</div>
    <div class="login-divider"><span>Log in to Ticketer</span></div>
    <?php if($error): ?>
      <p class="error"><?= $error ?></p>
    <?php endif; ?>
    <form method="post" class="login-form" action="">
      <label>Email</label>
      <input type="email" name="email" placeholder="Enter your email address" required>

      <label>Password</label>
      <input type="password" name="password" placeholder="Enter your password" required>

      <div class="options">
        <label><input type="checkbox" name="remember"> Remember me</label>
        <a href="forgot.php">Forgot Password</a>
      </div>

      <button type="submit" class="btn-primary">Log in</button>
    </form>
    <div class="login-footer">
      Don’t have an account? <a href="signup.php">Sign up</a>
    </div>
  </div>

  <script>
    // Hiện modal
    document.getElementById('adminBtn').onclick = function() {
      document.getElementById('adminModal').style.display = 'flex';
      document.getElementById('adminPass').value = '';
      document.getElementById('adminError').style.display = 'none';
    };
    // Đóng modal
    function closeAdminModal() {
      document.getElementById('adminModal').style.display = 'none';
    }
    // Xử lý submit
    function submitAdminPass() {
      var pass = document.getElementById('adminPass').value;
      if (pass === '1111') {
        window.location.href = 'event_admin.php';
      } else {
        document.getElementById('adminError').innerText = 'Sai mật khẩu admin!';
        document.getElementById('adminError').style.display = 'block';
      }
    }
    // Đóng modal khi bấm ngoài vùng popup
    document.getElementById('adminModal').onclick = function(e) {
      if (e.target === this) closeAdminModal();
    };
  </script>
</body>
</html>
