<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$cartItems = $_SESSION['cartItems'] ?? [];
$total = array_reduce($cartItems, fn($sum,$i)=>$sum+$i['price'],0);
$fees  = round($total * 0.01);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Checkout – Ticketer</title>
  <style>
    *{margin:0;padding:0;box-sizing:border-box;font-family:Arial,sans-serif}
    body{background:#fafafa;color:#333}
    .container{width:90%;max-width:1200px;margin:20px auto}
    a{text-decoration:none;color:inherit}
    img{max-width:100%;display:block}
    input,select,textarea,button{font-family:inherit}
    header{padding:16px 0;background:#fff}
    .nav-main{display:flex;justify-content:space-between;align-items:center}
    .nav-main ul{list-style:none;display:flex;gap:20px}
    .btn-outline{padding:8px 16px;border:1px solid #502eff;border-radius:4px;color:#502eff;background:transparent}
    .progress{display:flex;justify-content:space-between;align-items:center;margin:20px 0}
    .step{flex:1;text-align:center;position:relative}
    .step:not(:last-child)::after{content:'';position:absolute;top:12px;right:-50%;width:100%;height:2px;background:#ddd;z-index:-1}
    .step.done .circle{background:#34c759;border-color:#34c759;color:#fff}
    .circle{display:inline-block;width:28px;height:28px;line-height:28px;border:2px solid #502eff;border-radius:50%;margin-bottom:6px;color:#502eff}
    .review-header{text-align:center;margin:30px 0}
    .review-header h1{font-size:24px;margin-bottom:8px}
    .review-header p{color:#666;font-size:14px}
    .timer{display:inline-flex;align-items:center;gap:6px;background:rgba(255,77,79,0.1);color:#ff4d4f;padding:6px 12px;border-radius:4px;font-size:14px;margin-top:8px}
    .main{display:flex;gap:40px;margin-bottom:40px}
    .left{flex:1}
    .right{flex:1}
    .cart-item{display:flex;align-items:center;background:#fff;border-radius:8px;padding:16px;margin-bottom:16px}
    .cart-item img{width:80px;height:80px;border-radius:4px;object-fit:cover;margin-right:16px}
    .cart-details{flex:1}
    .cart-details h3{margin-bottom:6px;font-size:16px}
    .cart-details div{font-size:14px;color:#555;display:flex;align-items:center;gap:6px;margin-bottom:4px}
    .cart-price{font-size:18px;font-weight:bold}
    .summary{background:#fff;border-radius:8px;padding:16px;margin-bottom:24px;font-size:14px}
    .summary div{display:flex;justify-content:space-between;margin-bottom:8px}
    .summary strong{font-size:16px}
    .payment{background:#fff;border-radius:8px;padding:24px}
    .payment h3{margin-bottom:16px;font-size:18px}
    .methods{display:flex;gap:16px;margin-bottom:24px}
    .method{display:flex;align-items:center;gap:8px}
    .method input{transform:scale(1.2)}
    .method img{width:32px;height:auto}
    .field{margin-bottom:16px}
    .field label{display:block;font-size:12px;color:#555;margin-bottom:4px;font-weight:600}
    .field input{width:100%;padding:10px 12px;border:1px solid #ccc;border-radius:8px;font-size:14px}
    .checkbox{display:flex;align-items:center;gap:8px;margin-bottom:24px;font-size:14px}
    .checkbox input{transform:scale(1.2)}
    .btn-pay{display:block;width:100%;text-align:center;padding:12px;background:#502eff;color:#fff;border:none;border-radius:8px;font-size:16px}
    footer{background:#fff;padding:40px 0;border-top:1px solid #eaeaea;font-size:14px}
    .footer-cols{display:flex;gap:40px;flex-wrap:wrap;margin-bottom:20px}
    .footer-col{flex:1;min-width:180px}
    .footer-col h4{margin-bottom:12px;font-size:16px}
    .footer-col ul{list-style:none}
    .footer-col li{margin-bottom:8px}
    .footer-bottom{text-align:center;color:#999;font-size:12px}
    @media (max-width:900px){.main{flex-direction:column;gap:0}.left,.right{width:100%}}

    .method-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 24px;
        border: 2px solid #eee;
        border-radius: 12px;
        background: #fff;
        transition: border-color 0.2s, box-shadow 0.2s;
        font-size: 15px;
        font-weight: 500;
        cursor: pointer;
        box-shadow: 0 1px 4px rgba(80,46,255,0.04);
    }
    .method-btn img {
        width: 28px;
        height: 28px;
        object-fit: contain;
        border-radius: 6px;
        background: #fff;
    }
    .method-btn.active, .method-btn:focus {
        border-color: #502eff;
        box-shadow: 0 2px 8px rgba(80,46,255,0.08);
    }
    .method-btn:hover {
        border-color: #a89cff;
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
      <?php $done = $i<2; ?>
      <div class="step <?= $done?'done':'' ?>">
        <div class="circle"><?= $done? '&#10003;': $i+1 ?></div>
        <div><?= $s ?></div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="container review-header">
    <h1>Checkout</h1>
    <p>Fill Out Necessary Information here.</p>
    <div class="timer" id="timer">⏱ <span>10:00</span></div>
  </div>

  <div class="container main">
    <div class="left">
      <h2>Your Ticket List</h2>
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

      <div class="summary">
        <div><span>Subtotal</span><span><?= number_format($total,0,',','.') ?> đ</span></div>
        <div><span>Service Fees</span><span><?= number_format($fees,0,',','.') ?> đ</span></div>
        <div style="border-top:1px solid #eee;margin:8px 0"></div>
        <div><strong>Total VND (<?= count($cartItems) ?> item)</strong><strong><?= number_format($total+$fees,0,',','.') ?> đ</strong></div>
      </div>
    </div>
    <div class="right">
      <h3>Payment Details</h3>
      <div class="methods" style="gap:16px;">
        <button type="button" class="pay-btn method-btn active" id="btn-vnpay">
            <img src="image/vnpay.png" alt="VnPay">
            <span>VnPay</span>
        </button>
        <button type="button" class="pay-btn method-btn" id="btn-card">
            <img src="https://upload.wikimedia.org/wikipedia/commons/0/04/Mastercard-logo.png" alt="Credit Card">
            <span>Credit Card</span>
        </button>
        <input type="hidden" name="pay_method" id="pay_method" value="vnpay">
      </div>

      <div class="payment">
        <!-- Form VNPAY -->
        <form id="form-vnpay" action="vnpay_payment.php" method="post" style="display:block;">
            <input type="hidden" name="event_id" value="<?= htmlspecialchars($cartItems[0]['event_id']) ?>">
            <input type="hidden" name="quantity" value="<?= count($cartItems) ?>">
            <input type="hidden" name="amount" value="<?= $total+$fees ?>">
            <input type="hidden" name="section" value="<?= htmlspecialchars($cartItems[0]['hall']) ?>">
            <input type="hidden" name="seats" value="<?= htmlspecialchars(implode(',', array_column($cartItems, 'code'))) ?>">
            <input type="hidden" name="total_vnpay" value="<?= $total+$fees ?>">
            <!-- Các trường user_name, user_phone, user_email nếu cần -->
            <button type="submit" class="pay-btn">Thanh toán VNPAY</button>
        </form>
        <!-- Form Credit Card -->
        <form id="form-card" action="process_payment.php" method="POST" style="display:none;">
          <div class="field">
            <label>Card Number</label>
            <input type="text" name="card_number" placeholder="XXXX XXXX XXXX XXXX" maxlength="19" required>
          </div>
          <div class="field" style="display:flex;gap:16px">
            <div style="flex:1">
              <label>Expiration Date</label>
              <input type="text" name="exp_date" placeholder="MM/YY" maxlength="5" required>
            </div>
            <div style="flex:1">
              <label>CVV</label>
              <input type="text" name="cvv" placeholder="XXX" maxlength="3" required>
            </div>
          </div>
          <div class="field">
            <label>Name on Card</label>
            <input type="text" name="card_name" placeholder="Enter your name" required>
          </div>
          <div class="field">
            <label>Discount Code</label>
            <input type="text" name="discount" placeholder="Enter discount code">
          </div>
          <label class="checkbox">
            <input type="checkbox" name="agree" required>
            BY CLICKING THIS, I AGREE TO TICKETER <a href="#">PRIVACY POLICY</a>
          </label>
          <button type="submit" class="btn-pay">Pay <?= number_format($total+$fees,0,',','.') ?> đ</button>
        </form>
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

  <script>
    // Countdown timer from 10:00
    let time = 600;
    const timerSpan = document.querySelector('#timer span');
    const interval = setInterval(() => {
      if (time <= 0) { clearInterval(interval); return; }
      time--;
      const m = String(Math.floor(time/60)).padStart(2,'0');
      const s = String(time%60).padStart(2,'0');
      timerSpan.textContent = `${m}:${s}`;
    }, 1000);

    // Đổi màu khi chọn button
    const btnVnpay = document.getElementById('btn-vnpay');
    const btnCard = document.getElementById('btn-card');
    const payMethod = document.getElementById('pay_method');
    const formVnpay = document.getElementById('form-vnpay');
    const formCard = document.getElementById('form-card');

    btnVnpay.onclick = function() {
        btnVnpay.classList.add('active');
        btnCard.classList.remove('active');
        payMethod.value = 'vnpay';
        formVnpay.style.display = 'block';
        formCard.style.display = 'none';
    }
    btnCard.onclick = function() {
        btnCard.classList.add('active');
        btnVnpay.classList.remove('active');
        payMethod.value = 'credit_card';
        formVnpay.style.display = 'none';
        formCard.style.display = 'block';
    }
  </script>
</body>
</html>
<?php
// Sau khi lấy $event từ DB
$event_image = $event['image_name']; // Đảm bảo đây là tên file ảnh đúng của sự kiện

$_SESSION['order'] = [
    'event'    => $event_name,
    'quantity' => $quantity,
    'amount'   => $total_amount,
    'section'  => $section,
    'seats'    => $seats, // mảng số ghế
    'date'     => $date,
    'time'     => $time,
    'image'    => $event_image
];
// Sau đó chuyển hướng sang get_ticket.php
header('Location: get_ticket.php');
exit;