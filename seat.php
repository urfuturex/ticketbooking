<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$ticket_type_id = isset($_GET['ticket_type_id']) ? intval($_GET['ticket_type_id']) : 0;
if ($ticket_type_id <= 0) die("Invalid ticket type!");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['cartItems'] = $_POST['cartItems']; // cartItems là mảng dữ liệu vé đã chọn
    header('Location: card.php');
    exit;
}
// seat.php - View-only seat map
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seat Map – Ticketer</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background: #fafafa; color: #333; }
        .container { width: 90%; max-width: 1200px; margin: 20px auto; }
        a { text-decoration: none; color: inherit; }
        header { background: #fff; padding: 16px 0; }
        .nav-main { display: flex; align-items: center; justify-content: space-between; }
        .nav-main ul { list-style: none; display: flex; gap: 20px; }
        .btn-outline { padding: 8px 16px; border: 1px solid #3f51b5; border-radius: 4px; color: #3f51b5; background: transparent; }
        .progress { display: flex; justify-content: space-between; margin: 20px 0; }
        .step { text-align: center; flex: 1; position: relative; }
        .step:not(:last-child)::after { content: ''; position: absolute; top: 12px; right: -50%; width: 100%; height: 2px; background: #ddd; z-index: -1; }
        .circle { display: inline-block; width: 28px; height: 28px; line-height: 28px; border: 2px solid #3f51b5; border-radius: 50%; margin-bottom: 6px; background: #3f51b5; color: #fff; }
        .hero h1 { font-size: 24px; margin-bottom: 16px; }
        .info-row { display: flex; gap: 16px; align-items: center; margin-bottom: 32px; }
        .info-item { display: flex; align-items: center; gap: 6px; font-size: 14px; }
        .btn-secondary { padding: 8px 16px; border: none; background: #3f51b5; color: #fff; border-radius: 4px; }
        .seat-map {
            display: grid;
            grid-template-columns: 1fr 2fr 1fr;
            gap: 32px;
            margin-bottom: 24px;
            align-items: start;
        }
        .section {
            text-align: center;
        }
        .section-title { background: #eee; padding: 8px 24px; border-radius: 4px; margin-bottom: 8px; font-weight: bold; display: inline-block;}
        .seats { display: grid; gap: 6px; justify-content: center; }
        .seat { width: 24px; height: 24px; border-radius: 4px; background: rgba(63,81,181,0.12); display: inline-block; margin: 2px; }
        .seat.standing { background: #4caf50; }
        .seat.vip { background: #ffd700; }
        .legend { display: flex; justify-content: center; gap: 16px; font-size: 14px; margin-bottom: 32px;}
        .legend-item { display: flex; align-items: center; gap: 4px; }
        .legend-color { width: 16px; height: 16px; border-radius: 4px; }
        .legend-color.avail { background: rgba(63,81,181,0.12); }
        .legend-color.vip { background: #ffd700; }
        .legend-color.stand { background: #4caf50; }
        @media (max-width: 900px) {
            .seat-map { gap: 18px;}
        }
        .btn-checkout {
            display: inline-block; /* Đổi từ block sang inline-block */
            margin: 48px auto 40px auto;
            padding: 12px 32px;    /* Giảm padding cho gọn */
            font-size: 1.15rem;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(90deg, #3f51b5 60%, #5c6bc0 100%);
            border: none;
            border-radius: 24px;
            box-shadow: 0 4px 24px rgba(63,81,181,0.13);
            cursor: pointer;
            transition: background 0.18s, box-shadow 0.18s, transform 0.13s;
            letter-spacing: 0.5px;
            text-align: center;
            text-decoration: none;
        }
        .btn-checkout:hover {
            background: linear-gradient(90deg, #283593 60%, #3f51b5 100%);
            box-shadow: 0 8px 32px rgba(63,81,181,0.18);
            transform: translateY(-2px) scale(1.03);
        }
    </style>
</head>
<body>
    <header class="container nav-main">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="concert.php">Events</a></li>
        </ul>
        <div><img src="image/logo.png" alt="Ticketer" style="height:36px"></div>
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
            <div class="step"><div class="circle"><?= $i+1 ?></div><div><?= $s ?></div></div>
        <?php endforeach; ?>
    </div>

    <div class="container hero">
        <h1>Seat Map Preview</h1>
        <div class="info-row">
            <div class="info-item">📍 Main Hall</div>
            <div class="info-item">🪑 View seat and standing layout below. You will be assigned a seat/standing spot at the venue.</div>
        </div>
    </div>

    <!-- SÂN KHẤU -->
    <div class="container" style="display:flex; justify-content:center; margin-bottom:32px;">
        <div style="
            background: #222;
            color: #fff;
            font-weight: bold;
            font-size: 1.2rem;
            padding: 16px 60px;
            border-radius: 12px;
            letter-spacing: 2px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            text-align: center;
            ">
            STAGE
        </div>
    </div>

    <div class="container seat-map">
        <!-- Left CAT -->
        <div class="section">
            <div class="section-title">CAT</div>
            <div class="seats" style="grid-template-columns: repeat(4,24px)">
                <?php for($r=0;$r<5;$r++): for($c=0;$c<4;$c++): ?>
                    <div class="seat" title="CAT"></div>
                <?php endfor; endfor; ?>
            </div>
        </div>
        <!-- Center: Standing + VIP A -->
        <div style="display:flex; flex-direction:column; align-items:center; gap:40px;">
            <div class="section">
                <div class="section-title">STANDING</div>
                <div class="seats" style="grid-template-columns: repeat(12,24px)">
                    <?php for($i=0;$i<36;$i++): ?>
                        <div class="seat standing" title="Standing"></div>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="section">
                <div class="section-title">VIP A</div>
                <div class="seats" style="grid-template-columns: repeat(10,24px)">
                    <?php for($r=0;$r<3;$r++): for($c=0;$c<10;$c++): ?>
                        <div class="seat vip" title="VIP A"></div>
                    <?php endfor; endfor; ?>
                </div>
            </div>
        </div>
        <!-- Right CAT -->
        <div class="section">
            <div class="section-title">CAT</div>
            <div class="seats" style="grid-template-columns: repeat(4,24px)">
                <?php for($r=0;$r<5;$r++): for($c=0;$c<4;$c++): ?>
                    <div class="seat" title="CAT"></div>
                <?php endfor; endfor; ?>
            </div>
        </div>
    </div>

    <div class="container legend">
        <div class="legend-item"><div class="legend-color avail"></div>CAT</div>
        <div class="legend-item"><div class="legend-color stand"></div>Standing</div>
        <div class="legend-item"><div class="legend-color vip"></div>VIP A</div>
    </div>


    <div style="text-align:center">
        <a href="card.php?ticket_type_id=<?= $ticket_type_id ?>" class="btn-checkout">Continue to checkout →</a>
    </div>
</body>
</html>