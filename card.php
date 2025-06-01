<?php
// card.php – Review & payment options step
session_start();
include("db_connect.php");

$is_logged_in = isset($_SESSION['user_id']);

$ticket_type_id = isset($_GET['ticket_type_id']) ? intval($_GET['ticket_type_id']) : 0;
if ($ticket_type_id <= 0) die("Invalid ticket type!");

// Lấy thông tin loại vé từ database
$sql = "SELECT * FROM ticket_types WHERE id = $ticket_type_id";
$res = $conn->query($sql);
if ($res->num_rows == 0) die("Ticket type not found!");
$item = $res->fetch_assoc();

// Lấy thông tin sự kiện từ database
$event_id = $item['event_id'];
$sql_event = "SELECT * FROM events WHERE id = $event_id";
$res_event = $conn->query($sql_event);
$event = $res_event->fetch_assoc();

$item['image'] = $event['image_name']; // Gán tên ảnh từ bảng events cho vé
$item['event_name'] = $event['event_name']; // Thêm tên sự kiện
$item['event_date'] = $event['event_date']; // Thêm ngày diễn ra sự kiện
$cartItems = [$item];
$total = $item['price'];
$fees = round($total * 0.01);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout – Ticketer</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;font-family:Arial,sans-serif}
        body{background:#fafafa;color:#333}
        .container{width:90%;max-width:1200px;margin:20px auto}
        a{text-decoration:none;color:inherit}
        img{max-width:100%;display:block}
        button{cursor:pointer;border:none}
        header{background:#fff;padding:16px 0}
        .nav-main{display:flex;justify-content:space-between;align-items:center}
        .nav-main ul{list-style:none;display:flex;gap:20px}
        .btn-outline{padding:8px 16px;border:1px solid #502eff;border-radius:4px;color:#502eff;background:transparent}
        .progress{display:flex;justify-content:space-between;margin:20px 0;font-size:14px}
        .step{flex:1;text-align:center;position:relative}
        .step:not(:last-child)::after{content:'';position:absolute;top:12px;right:-50%;width:100%;height:2px;background:#ddd;z-index:-1}
        .circle{display:inline-block;width:28px;height:28px;line-height:28px;border:2px solid #502eff;border-radius:50%;margin-bottom:6px;background:#502eff;color:#fff}
        .review-header{text-align:center;margin:30px 0}
        .review-header h1{font-size:24px;margin-bottom:8px}
        .review-header p{color:#666;font-size:14px}
        .timer{display:inline-flex;align-items:center;gap:6px;background:rgba(255,77,79,0.1);color:#ff4d4f;padding:6px 12px;border-radius:4px;font-size:14px;margin-top:8px}
        .cart-item{display:flex;align-items:center;background:#fff;border-radius:8px;padding:16px;margin-bottom:12px}
        .cart-item img{width:80px;height:80px;border-radius:4px;object-fit:cover;margin-right:16px}
        .cart-details{flex:1}
        .cart-details h3{margin-bottom:6px;font-size:16px}
        .cart-details div{font-size:14px;color:#555;display:flex;align-items:center;gap:6px;margin-bottom:4px}
        .cart-price{font-size:18px;font-weight:bold}
        .remove{cursor:pointer;font-size:18px;color:#999}
        .divider{height:1px;background:#eee;margin:16px 0}
        .summary{background:#fff;border-radius:8px;padding:16px;margin-bottom:24px;font-size:14px}
        .summary div{display:flex;justify-content:space-between;margin-bottom:8px}
        .summary strong{font-size:16px}
        .buttons{display:flex;gap:16px;margin-bottom:40px}
        .btn-primary{flex:1;padding:12px;background:#502eff;color:#fff;border-radius:8px;font-size:16px}
        .btn-card{flex:1;padding:12px;border:1px solid #502eff;border-radius:8px;display:flex;justify-content:center;align-items:center;gap:8px;background:#fff}
        .btn-card img{width:24px;height:auto}
        footer{background:#fff;padding:40px 0 0 0;border-top:1px solid #eaeaea;font-size:14px}
        .footer-cols{display:flex;gap:40px;flex-wrap:wrap;margin-bottom:20px}
        .footer-col{flex:1;min-width:180px}
        .footer-col h4{margin-bottom:12px;font-size:16px}
        .footer-col ul{list-style:none}
        .footer-col li{margin-bottom:8px}
        .footer-bottom{text-align:center;color:#999;font-size:12px}
        @media (max-width: 900px) {
            .cart-item{flex-direction:column;align-items:flex-start}
            .buttons{flex-direction:column}
        }
    </style>
</head>
<body>
    <header class="container nav-main">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="concert.php">Events</a></li>
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
            <div class="step">
                <div class="circle"><?= $i<2? '&#10003;': ($i===2? '3' : ($i===3? '4':'')); ?></div>
                <div><?= $s ?></div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="container review-header">
        <h1>Review Ticket</h1>
        <p>You can view and check your ticket here</p>
        <div class="timer" id="timer">⏱ <span>10:00</span></div>
    </div>
    <div class="container">
        <?php foreach($cartItems as $item): ?>
            <div class="cart-item">
                <img src="image/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['event_name']) ?>">
                <div class="cart-details">
                    <h3><?= htmlspecialchars($item['event_name']) ?></h3>
                    <div style="color:#888;font-size:13px;margin-bottom:4px;">
                        <span>🗓 <?= htmlspecialchars($item['event_date']) ?></span>
                    </div>
                    <div><?= htmlspecialchars($item['title']) ?></div>
                    <div>🏟 <?= htmlspecialchars($item['hall']) ?></div>
                    <div>🎟 <?= htmlspecialchars($item['available']) ?> Tickets Available!</div>
                </div>
                <div class="cart-price"><?= number_format($item['price'],0,',','.') ?> đ</div>
            </div>
        <?php endforeach; ?>
        <div class="divider"></div>
        <div class="summary">
            <div><span>Subtotal</span><span><?= number_format($total,0,',','.') ?> đ</span></div>
            <div><span>Service Fees</span><span><?= number_format($fees,0,',','.') ?> đ</span></div>
            <div class="divider"></div>
            <div><strong>Total VND (<?= count($cartItems) ?> item)</strong><strong><?= number_format($total+$fees,0,',','.') ?> đ</strong></div>
        </div>
        <div class="buttons">
            <form action="checkout.php" method="post" style="flex:1;">
                <button type="submit" class="btn-primary" style="width:100%;">Checkout Now</button>
                <?php
                // Lưu cartItems vào session khi submit
                $_SESSION['cartItems'] = $cartItems;
                ?>
            </form>
        </div>
    </div>
    <footer>
        <div class="container footer-cols">
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
        <div class="container" style="margin-top: 20px;">Contact: Ticketercontacts@gmail.com</div>
        <div class="container footer-bottom">&copy; 2024 NOT FULLTIME PVT.LTD.</div>
    </footer>
    <script>
        // Countdown timer from 10:00
        let time = 600;
        const timerEl = document.getElementById('timer').querySelector('span');
        const interval = setInterval(()=>{
            if(time<=0){ clearInterval(interval); return; }
            time--;
            const m = String(Math.floor(time/60)).padStart(2,'0');
            const s = String(time%60).padStart(2,'0');
            timerEl.textContent = `${m}:${s}`;
        },1000);
    </script>
</body>
</html>