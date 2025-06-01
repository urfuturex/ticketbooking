<?php

session_start();
include('db_connect.php');
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$res = $conn->query("SELECT * FROM tickets WHERE user_id = $user_id ORDER BY booked_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Tickets – Ticketer</title>
  <style>
    body{background:#fafafa;font-family:Arial,sans-serif;}
    .container{max-width:700px;margin:40px auto;background:#fff;padding:32px;border-radius:12px;box-shadow:0 4px 16px #0001;}
    h2{color:#502eff;margin-bottom:24px;}
    table{width:100%;border-collapse:collapse;}
    th,td{padding:12px 8px;text-align:left;}
    th{background:#f2f2ff;color:#502eff;}
    tr:nth-child(even){background:#fafaff;}
    .no-ticket{color:#888;text-align:center;padding:40px 0;}
    /* Menu styles */
    .nav-main{background:#fff;padding:16px 0 8px 0;}
    .nav-container{max-width:1200px;margin:0 auto;display:flex;align-items:center;}
    .nav-main ul{list-style:none;display:flex;gap:24px;margin:0;padding:0;}
    .nav-main li{display:inline;}
    .nav-main a{color:#222;text-decoration:none;font-weight:500;font-size:16px;padding:4px 0;transition:.2s;}
    .nav-main a:hover{color:#502eff;}
    .nav-main .nav-right{margin-left:auto;}
  </style>
</head>
<body>
  <header class="nav-main">
    <div class="nav-container">
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="concert.php">Events</a></li>
      </ul>
      <div class="nav-right"></div>
    </div>
  </header>
  <div class="container">
    <h2>Your Tickets Booked</h2>
    <?php if ($res && $res->num_rows > 0): ?>
      <table>
        <tr>
          <th>Event</th>
          <th>Section</th>
          <th>Quantity</th>
          <th>Amount</th>
          <th>Booked At</th>
        </tr>
        <?php while($row = $res->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['event']) ?></td>
          <td><?= htmlspecialchars($row['section']) ?></td>
          <td><?= $row['quantity'] ?></td>
          <td><?= number_format($row['amount'],0,',','.') ?> Đ</td>
          <td><?= $row['booked_at'] ?></td>
        </tr>
        <?php endwhile; ?>
      </table>
    <?php else: ?>
      <div class="no-ticket">You haven't booked any tickets yet.</div>
    <?php endif; ?>
  </div>
</body>
</html>