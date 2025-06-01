<?php
include("db_connect.php");
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$result = mysqli_query($conn, "SELECT * FROM events ORDER BY event_date ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Concert Page</title>
  <link rel="stylesheet" href="concert.css" />
</head>
<body>

<!-- ✅ CONTAINER -->
<section class="concert-container">
  <!-- ✅ HEADER -->
  <header class="nav-container">
    <div class="nav-left">
      <a href="index.php">Home</a>
      <a href="#">Events</a>
      
    </div>
    <div class="nav-center">
      <img src="image/logoden.png" alt="Logo" class="logo">
    </div>
    <div class="nav-right">
      <?php if ($is_logged_in): ?>
        <div class="user-dropdown" style="position: relative;">
          <a href="#" class="account-icon" id="userIcon" style="display:inline-block;">
            <img src="image/user-icon.png" alt="User" style="width:28px;height:28px;border-radius:50%;vertical-align:middle;">
          </a>
          <div class="dropdown-menu" id="dropdownMenu" style="display:none; position:absolute; right:0; top:38px; background:#fff; border:1px solid #ddd; border-radius:8px; min-width:160px; box-shadow:0 2px 8px rgba(0,0,0,0.08); z-index:100;">
            <a href="account.php" style="display:block; padding:12px 20px; color:#222; text-decoration:none; border-bottom:1px solid #eee;">Edit User</a>
            <a href="tickets_booked.php" style="display:block; padding:12px 20px; color:#222; text-decoration:none;">🎟 Tickets booked</a>
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

  <!-- ✅ FILTER BAR -->
  <div class="filter-bar">
    <input type="text" placeholder="Type a singer name">
    <input type="date">
    <input type="text" placeholder="Location">
    <select>
      <option disabled selected>Sort by</option>
      <option>Newest</option>
      <option>Popularity</option>
    </select>
  </div>

  <!-- ✅ TITLE -->
  <div class="concert-title">This Month</div>
  <div class="concert-subtitle">Check out concerts that will hold in the next weeks</div>

  <!-- ✅ DYNAMIC GRID -->
  <div class="concert-grid">
    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
      <div class="concert-card">
        <img src="image/<?= htmlspecialchars($row['image_name']) ?>" alt="Concert Poster">
        <div class="concert-info">
          <h5><?= htmlspecialchars($row['event_name']) ?></h5>
          <p><?= date("F j, Y", strtotime($row['event_date'])) ?> - <?= htmlspecialchars($row['location']) ?></p>
          <div class="concert-meta">Price from <?= number_format($row['price'], 0, '.', '.') ?> đ</div>
        </div>
      </div>
    <?php } ?>
  </div>
</section>

<div class="view-more">
  <button>View More Events ➔</button>
</div>

<div class="gap-block"></div>

</body>
<footer class="footer">
  <div class="top">
    <div class="column">
      <img src="image/logoden.png" alt="Logo" class="logo">
      <h4>Who we are?</h4>
      <p>Ticketer is a global ticketing platform that allows users to create, share, and find concerts.</p>
      <h4>Contact</h4>
      <p>Ticketercontacts@gmail.com</p>
    </div>
    <div class="column">
      <h4>TICKETER</h4>
      <a href="#">About Us</a>
      <a href="#">Contact Us</a>
      <a href="#">FAQs</a>
    </div>
    <div class="column">
      <h4>Help</h4>
      <a href="#">Concert Ticketing</a>
      <a href="#">Account Support</a>
      <a href="#">Terms & Conditions</a>
    </div>
    <div class="column">
      <h4>Legal</h4>
      <a href="#">Terms of Use</a>
      <a href="#">Privacy Policy</a>
      <a href="#">Acceptable Use</a>
    </div>
  </div>

  <div class="email-subscribe">
    <input type="email" placeholder="Enter your email">
    <button>➔</button>
  </div>

  <div class="footer-bottom">
    ©2025 GROUP 1 TICKET BOOKING | 
    <a href="#">TERMS</a> | 
    <a href="#">PRIVACY</a> | 
    <a href="#">COOKIES</a>
  </div>
</footer>

</html>