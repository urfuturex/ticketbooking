<?php
session_start();
// echo '<pre>'; print_r($_SESSION['order']); echo '</pre>'; // Đã bỏ debug này
include('db_connect.php');

$order = $_SESSION['order'] ?? null;
$is_logged_in = isset($_SESSION['user_id']);

if (!$order || !$is_logged_in) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT name, email, phone FROM users WHERE id = $user_id";
$res = $conn->query($sql);
if ($res && $res->num_rows === 1) {
    $user = $res->fetch_assoc();
    $_SESSION['user_info'] = $user;

    // Lưu vé vào bảng tickets nếu chưa lưu
    if (isset($order['event'], $order['section'], $order['quantity'], $order['amount'])) {
        $stmt = $conn->prepare("INSERT INTO tickets (user_id, event, section, quantity, amount) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issii", $user_id, $order['event'], $order['section'], $order['quantity'], $order['amount']);
        $stmt->execute();
        $stmt->close();
    }
} else {
    $user = ['name'=>'','email'=>'','phone'=>''];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Get Ticket – Ticketer</title>
  <style>
    *{margin:0;padding:0;box-sizing:border-box;font-family:Arial,sans-serif}
    body{background:#fafafa;color:#333}
    .container{width:90%;max-width:1200px;margin:20px auto}
    a{text-decoration:none;color:inherit}
    img{max-width:100%;display:block;border-radius:8px}
    button{cursor:pointer;border:none;font-family:inherit}
    header{padding:16px 0;background:#fff}
    .nav-main{display:flex;justify-content:space-between;align-items:center}
    .nav-main ul{list-style:none;display:flex;gap:20px}
    .btn-outline{padding:8px 16px;border:1px solid #502eff;border-radius:4px;color:#502eff;background:transparent}
    .progress{display:flex;justify-content:space-between;align-items:center;margin:20px 0}
    .step{flex:1;text-align:center;position:relative}
    .step:not(:last-child)::after{content:'';position:absolute;top:12px;right:-50%;width:100%;height:2px;background:#ddd;z-index:-1}
    .step.done .circle{background:#34c759;border-color:#34c759;color:#fff}
    .circle{display:inline-block;width:28px;height:28px;line-height:28px;border:2px solid #502eff;border-radius:50%;margin-bottom:6px;color:#502eff}
    .success{text-align:center;margin:40px 0}
    .success h1{color:#34c759;font-size:24px;margin-bottom:8px}
    .success p{color:#666;font-size:14px}
    .main{display:flex;gap:40px;align-items:flex-start;margin-bottom:40px}
    .left{flex:1}
    .right{flex:1;display:flex;justify-content:flex-end}
    .section-title{font-size:18px;font-weight:600;margin-bottom:8px}
    .details{background:#fff;border-radius:8px;padding:24px;font-size:14px;margin-bottom:24px}
    .details h2{margin-bottom:16px;font-size:20px}
    .details div{margin-bottom:8px}
    .details span{font-weight:600;margin-right:4px}
    .rewards{margin-top:16px}
    .reward{background:rgba(52,199,89,0.1);color:#34c759;padding:8px 12px;border-radius:8px;font-size:14px;margin-bottom:8px;display:inline-block}
    .ticket-card{position:relative;width:300px;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 8px rgba(0,0,0,0.1)}
    .ticket-card img{width:100%;height:auto}
    .ticket-info{position:absolute;bottom:16px;left:16px;color:#fff;font-size:14px;line-height:1.4;text-shadow:0 1px 3px rgba(0,0,0,0.5)}
    .ticket-info div{margin-bottom:4px}
    .btn-group{display:flex;gap:16px;justify-content:flex-end;margin-top:16px}
    .btn{padding:12px 24px;border-radius:8px;background:#502eff;color:#fff;font-size:14px;display:flex;align-items:center;gap:8px}
    .btn svg{width:16px;height:auto}
    .footer-cols{display:flex;gap:40px;margin:40px 0 16px 0}
    .footer-col{flex:1}
    .footer-col h4{margin-bottom:8px;font-size:16px}
    .footer-col ul{list-style:none;padding-left:0}
    .footer-col ul li{margin-bottom:6px}
    .footer-bottom{margin:16px 0 0 0;font-size:13px;color:#888}
  </style>
</head>
<body>
  <header class="container nav-main">
    <ul>
      <li><a href="index.php">Home</a></li>
      <li><a href="concert.php">Events</a></li>
      <li><a href="#">Singers</a></li>
    </ul>
    <div><img src="image/logo.png" alt="Ticketer" style="height:40px"></div>
    <div class="nav-right">
      <?php if ($is_logged_in): ?>
        <div class="user-dropdown" style="position: relative;">
          <a href="#" class="account-icon" id="userIcon" style="display:inline-block;">
            <img src="image/user-icon.png" alt="User" style="width:28px;height:28px;border-radius:50%;vertical-align:middle;">
          </a>
          <div class="dropdown-menu" id="dropdownMenu" style="display:none; position:absolute; right:0; top:38px; background:#fff; border:1px solid #ddd; border-radius:8px; min-width:160px; box-shadow:0 2px 8px rgba(0,0,0,0.08); z-index:100;">
            <a href="account.php" style="display:block; padding:12px 20px; color:#222; text-decoration:none; border-bottom:1px solid #eee;">Edit User</a>
            <a href="tickets_booked.php" style="display:block; padding:12px 20px; color:#222; text-decoration:none; border-bottom:1px solid #eee;">🎟 Tickets booked</a>
            <a href="logout.php" style="display:block; padding:12px 20px; color:#222; text-decoration:none;">Log out</a>
          </div>
        </div>
        <script>
          document.addEventListener('DOMContentLoaded', function() {
            var icon = document.getElementById('userIcon');
            var menu = document.getElementById('dropdownMenu');
            if(icon && menu) {
              icon.addEventListener('click', function(e) {
                e.preventDefault();
                menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
              });
              document.addEventListener('click', function(e) {
                if (!icon.contains(e.target) && !menu.contains(e.target)) {
                  menu.style.display = 'none';
                }
              });
            }
          });
        </script>
      <?php else: ?>
        <button class="login-btn" onclick="window.location.href='login.php'">Login/Register</button>
      <?php endif; ?>
    </div>
  </header>

  <div class="container progress">
    <?php $steps=['Choose Time','Choose Seat','Checkout','Get Ticket']; foreach($steps as $i=>$s): ?>
      <div class="step done">
        <div class="circle">&#10003;</div>
        <div><?= $s ?></div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="container success">
    <h1>Payment Successful!</h1>
    <p>You got your ticket. Download it here.</p>
  </div>

  <div class="container main">
    <div class="left">
      <div class="details">
        <h2>Congratulations!</h2>
        <div>You've Successfully purchased the ticket for:</div>
        <div><a href="#" style="color:#502eff;font-size:16px;"><?= htmlspecialchars($order['event']) ?></a></div>
      </div>
      <div class="details">
        <div class="section-title">Item Details</div>
        <div><span>Event:</span> <?= htmlspecialchars($order['event']) ?></div>
        <div><span>Date:</span> <?= htmlspecialchars($order['date']) ?></div>
        <div><span>Section:</span> <?= htmlspecialchars($order['section']) ?></div>
        <div><span>Quantity:</span> <?= $order['quantity'] ?> TICKETS</div>
        <div><span>Amount:</span> <?= number_format($order['amount'],0,',','.') ?> Đ</div>
      </div>
      <div class="details">
        <div class="section-title">Customer details</div>
        <div><span>Name:</span><?= htmlspecialchars($user['name']) ?></div>
        <div><span>Contact Number:</span><?= htmlspecialchars($user['phone']) ?></div>
        <div><span>Email Address:</span><?= htmlspecialchars($user['email']) ?></div>
      </div>
      <div class="rewards">
        <div>THANK YOU FOR CHOOSING TO BUY FROM TICKETER!</div>
        <div>YOU'VE UNLOCKED SPECIAL REWARDS:</div>
        <div class="reward">20% Discount on your next ticket!</div>
        <div class="reward">Earned 50 points for your purchase!</div>
      </div>
    </div>
    <div class="right">
      <div class="btn-group">
        <a href="download_pdf.php" class="btn">
          <!-- download icon -->
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="#fff" d="M5 20h14v-2H5v2zm7-18l-7 7h4v6h6v-6h4l-7-7z"/></svg>
          Download Ticket
        </a>
      </div>
    </div>
  </div>

  <footer class="container">
    <div class="footer-cols">
      <div class="footer-col">
        <h4>Who we are?</h4>
        <p>Ticketer is a global ticketing platform for live experiences that allows anyone to create, share, find and attend events that fuel their passions and enrich their lives.</p>
      </div>
      <div class="footer-col">
        <h4>Ticketer</h4>
        <ul>
          <li><a href="#">About Us</a></li>
          <li><a href="#">Contact Us</a></li>
          <li><a href="#">FAQs</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Help</h4>
        <ul>
          <li><a href="#">Concert Ticketing</a></li>
          <li><a href="#">Account Support</a></li>
          <li><a href="#">Terms & Conditions</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Legal</h4>
        <ul>
          <li><a href="#">Terms of Us</a></li>
          <li><a href="#">Acceptable</a></li>
          <li><a href="#">Privacy Policy</a></li>
        </ul>
      </div>
    </div>
    <div>Contact: Ticketercontacts@gmail.com</div>
    <div class="footer-bottom">&copy; 2024 NOT FULLTIME PVT.LTD.</div>
  </footer>
</body>
</html>