<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("db_connect.php");
session_start();
$is_logged_in = isset($_SESSION['user_id']);

$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
if ($event_id <= 0) die("Invalid event!");

// Get event info
$sql = "SELECT * FROM events WHERE id = $event_id";
$result = $conn->query($sql);
if ($result->num_rows == 0) die("Event not found!");
$event = $result->fetch_assoc();

// Get ticket types for this event
$tickets = [];
$res = $conn->query("SELECT * FROM ticket_types WHERE event_id = $event_id ORDER BY price ASC");
while ($row = $res->fetch_assoc()) {
    $tickets[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Tickets – <?= htmlspecialchars($event['event_name']) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Arial', sans-serif; }
        body { background: #fafafa; color: #333; line-height: 1.5; }
        a { text-decoration: none; }
        img { max-width: 100%; display: block; }
        button { cursor: pointer; }
        .container { width: 90%; max-width: 1200px; margin: auto; }

        header { padding: 20px 0; background: #fff; }
        .nav-main { display: flex; align-items: center; justify-content: space-between; }
        .nav-main ul { list-style: none; display: flex; gap: 20px; }
        .nav-main li { font-weight: 500; }
        .nav-main .logo { flex: 1; text-align: center; }
        .nav-main .actions { display: flex; gap: 15px; align-items: center; }
        .btn-outline { padding: 8px 16px; border: 1px solid #3f51b5; border-radius: 4px; color: #3f51b5; background: transparent; }

        .progress { display: flex; justify-content: space-between; align-items: center; margin: 40px 0; }
        .step { text-align: center; flex: 1; position: relative; }
        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 12px;
            right: -50%;
            width: 100%; height: 2px;
            background: #ddd;
            z-index: -1;
        }
        .step.active .circle { background: #3f51b5; color: #fff; }
        .circle { display: inline-block; width: 28px; height: 28px; line-height: 28px;
                  border: 2px solid #3f51b5; border-radius: 50%; margin-bottom: 8px; }

        .hero h1 { font-size: 24px; margin-bottom: 20px; }
        .hero img { border-radius: 8px; margin-bottom: 20px; }
        .info-row { display: flex; gap: 40px; margin-bottom: 40px; flex-wrap: wrap;}
        .info-item { display: flex; align-items: center; gap: 8px; font-size: 15px;}
        .btn-secondary { padding: 8px 16px; border: none; background: #3f51b5; color: #fff; border-radius: 4px; }

        .tickets { margin-bottom: 60px; }
        .tickets h2 { margin-bottom: 20px; }
        .ticket-card { display: flex; background: #fff; border-radius: 8px; overflow: hidden; margin-bottom: 16px; }
        .ticket-card .label { background: #3f51b5; color: #fff;
                                width: 80px; display: flex; align-items: center;
                                justify-content: center; font-weight: bold; }
        .ticket-card .details { padding: 16px; flex: 1; display: flex;
                                 justify-content: space-between; align-items: center; }
        .details-info { font-size: 14px; color: #555; }
        .details-info div { margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
        .ticket-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: #222;
            margin-bottom: 8px;
            text-align: right;
        }
        .btn-book-now {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 28px;
            font-size: 1.2rem;
            font-weight: 500;
            color: #3f51b5;
            background: #fff;
            border: 2px solid #3f51b5;
            border-radius: 10px;
            transition: background 0.18s, color 0.18s, box-shadow 0.18s;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(63,81,181,0.06);
        }
        .btn-book-now:hover {
            background: #3f51b5;
            color: #fff;
            border-color: #3f51b5;
        }
        .details {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 32px;
        }

        footer { background: #fff; padding: 40px 0; border-top: 1px solid #eaeaea; }
        .footer-cols { display: flex; gap: 40px; flex-wrap: wrap; }
        .footer-col { flex: 1; min-width: 180px; }
        .footer-col h4 { margin-bottom: 12px; font-size: 16px; }
        .footer-col ul { list-style: none; }
        .footer-col li { margin-bottom: 8px; }
        .footer-bottom { text-align: center; margin-top: 20px; font-size: 12px; color: #999; }
        @media (max-width: 900px) {
            .info-row { flex-direction: column; gap: 16px;}
            .footer-cols { flex-direction: column; gap: 24px;}
        }
    </style>
    <script>
function goToCheckout(ticketTypeId) {
    window.location.href = "seat.php?ticket_type_id=" + ticketTypeId;
}
</script>
</head>
<body>
    <header>
        <div class="container nav-main">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="concert.php">Events</a></li>
            </ul>
            <div class="logo"><img src="image/logo.png" alt="Ticketer" style="height:48px"></div>
            <div class="actions">
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
                  <a href="login.php" class="btn-outline">Login/Register</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <div class="container progress">
        <?php $steps = ['Choose Time','Choose Seat','Checkout','Get Ticket']; ?>
        <?php foreach($steps as $i => $name): ?>
            <div class="step <?= $i===0?'active':'' ?>">
                <div class="circle"><?= $i+1 ?></div>
                <div><?= $name ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="container hero">
        <h1><?= htmlspecialchars($event['event_name']) ?></h1>
        <img src="image/<?= htmlspecialchars($event['image_name']) ?>" alt="<?= htmlspecialchars($event['event_name']) ?>">
        <div class="info-row">
            <div class="info-item">
                📅 <div>DATE: <?= date('d M, Y', strtotime($event['event_date'])) ?></div>
            </div>
            <div class="info-item">
                ⏰ <div>TIME: <?= isset($event['event_time']) ? htmlspecialchars($event['event_time']) : 'N/A' ?></div>
            </div>
            <div class="info-item">
                📍 <div><?= htmlspecialchars($event['location']) ?></div>
            </div>
            <a href="https://maps.google.com/?q=<?= urlencode($event['location']) ?>" target="_blank" class="btn-secondary">Get Direction</a>
        </div>
    </div>

    <div class="container tickets">
        <h2>Ticket Types</h2>
        <?php foreach($tickets as $t): ?>
            <div class="ticket-card">
                <div class="label"><?= htmlspecialchars($t['type_name']) ?></div>
                <div class="details">
                    <div class="details-info">
                        <div><strong><?= htmlspecialchars($t['type_name']) ?></strong></div>
                        <div><?= htmlspecialchars($t['description']) ?></div>
                        <div>🏟 <?= htmlspecialchars($t['hall'] ?? 'Main Hall') ?></div>
                        <div>🎟 <?= htmlspecialchars($t['quantity']) ?> Tickets Available</div>
                    </div>
                    <div>
                        <div class="ticket-price"><?= number_format($t['price'], 0, '.', '.') ?> đ</div>
                        <button class="btn-book-now" onclick="goToCheckout(<?= $t['id'] ?>)">Book now</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($tickets)): ?>
            <div style="color:#e53935; font-size:16px;">No ticket types available for this event.</div>
        <?php endif; ?>
    </div>

    <footer>
        <div class="container footer-cols">
            <div class="footer-col">
                <h4>Who we are?</h4>
                <p>Ticketer is a global ticketing platform for live experiences...</p>
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
</body>
</html>