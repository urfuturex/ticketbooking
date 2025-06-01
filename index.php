<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
// index.php
include("db_connect.php");
$result = mysqli_query($conn, "SELECT * FROM events ORDER BY event_date ASC LIMIT 6 ");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Ticket Booking</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <!-- NAVIGATION -->
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
    </div>

    <!-- HERO -->
    <div class="hero-text">
      <h2>Welcome to</h2>
      <p class="subtitle">Group 1 Event Ticket Booking</p>
    </div>

    <!-- SEARCH BOXES -->
    <div class="search-boxes">
      <input type="text" placeholder="Type an event" />
      <input type="date" />
      <input type="text" placeholder="Location" />
      <button class="find-btn">Find Ticket</button>
    </div>
  </header>

  <!-- UPCOMING EVENTS -->
  <section class="upcoming-section navy-bg">
    <div class="upcoming">
      <h4>Upcoming Events</h4>
      <p class="subtitle">Explore nearby concerts and events here.</p>
    </div>
  </section>

  <!-- EVENT CARDS -->
  <section class="events-section white-bg">
    <div class="event-list">
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
      <div class="event-card">
        <div class="card-image" style="background-image: url('image/<?= htmlspecialchars($row['image_name']) ?>')">
          <div class="card-overlay">
            <span>⏱ Time to end</span>
            <span><?= rand(5, 30) ?>D , <?= rand(1, 23) ?>:<?= rand(10, 59) ?>:<?= rand(10, 59) ?></span>
          </div>
        </div>
        <h4 class="event-name"><?= strtoupper($row['event_name']) ?></h4>
        <p class="event-info"><?= date("F j", strtotime($row['event_date'])) ?> <?= strtoupper($row['location']) ?></p>
        <p class="event-price">From <?= number_format($row['price'], 0, ',', '.') ?> đ</p>
        <button class="book-btn" onclick="window.location.href='book_ticket.php?event_id=<?= $row['id'] ?>'">Book Now</button>
      </div>
      <?php } ?>
    </div>
  </section>

  <!-- ======= FEATURE BLOCK ======= -->
<section class="features">
  <h4>About Us</h4>
  <p class="subtitle">We promise users with the standard of these 4 services</p>
  <div class="feature-grid">
    <div class="feature-item">
      <img src="image/install.png" alt="Instalment Icon" />
      <h5>Instalment Payment!</h5>
      <p>You can pay a ticket in 2 portions throughout a fixed period of time.</p>
    </div>
    <div class="feature-item">
      <img src="image/online.png" alt="Online Icon" />
      <h5>Online Booking!</h5>
      <p>You can book your seat and get e-ticket within 5 minutes.</p>
    </div>
    <div class="feature-item">
      <img src="image/refund.png" alt="Refund Icon" />
      <h5>Refundable Tickets!</h5>
      <p>You can cancel or reschedule your booking easily before deadline.</p>
    </div>
    <div class="feature-item">
      <img src="image/cheapest.png" alt="Cheapest Icon" />
      <h5>Cheapest Tickets!</h5>
      <p>We help you find the best price across different ticket sellers.</p>
    </div>
  </div>
  <button class="buy-btn">Buy Ticket →</button>
</section>


<!-- ======= GAP BLOCK ======= -->
<div class="gap-block"></div>

<!-- Giao diện chia block -->
<div class="newsletter-news-wrapper">
  <!-- Left: Subscribe -->
  <div class="newsletter-subscribe">
    <h4>Subscribe our news letter</h4>
    <div class="input-group">
      <input type="email" placeholder="Enter your email">
      <button>Subscribe</button>
    </div>
  </div>

  <!-- Right: News Cards -->
  <div class="news-section">
    <div class="news-cards">
      <div class="news-card">
        <img src="image/taylor.jpg" alt="Taylor Swift">
        <h5>TAYLOR SWIFT IN BIGGEST WORLD TOUR</h5>
        <p>Lorem ipsum dolor sit amet consectetur...</p>
        <div class="author-info">
          <img src="image/noo.jpg" alt="">
          <span>Jonathan Wills • July 17, 2024 • 5 min</span>
        </div>
      </div>

      <div class="news-card">
        <img src="image/weekn.jpg" alt="Royal Hall">
        <h5>ROYAL ALBERT HALL NEW EVENTS</h5>
        <p>Lorem ipsum dolor sit amet consectetur...</p>
        <div class="author-info">
          <img src="image/noo.jpg" alt="">
          <span>Maria K • June 13, 2024 • 10 min</span>
        </div>
      </div>

      <div class="news-card">
        <img src="image/unknown.jpg" alt="Yanni Show">
        <h5>YANNI WILL BE IN LONDON</h5>
        <p>Lorem ipsum dolor sit amet consectetur...</p>
        <div class="author-info">
          <img src="image/noo.jpg" alt="">
          <span>Jack Nielson • May 9, 2024 • 7 min</span>
        </div>
      </div>
    </div>

    <div class="news-footer">
      <div class="nav-arrows">
        <button>&larr;</button>
        <button>&rarr;</button>
      </div>
      <a href="#" class="all-news">ALL NEWS →</a>
    </div>
  </div>
</div>

<!-- White Spacer -->
<div class="white-gap"></div>

<!-- Testimonials Section -->
<section class="testimonials">
  <h2 class="testimonial-title">What People Think About Us</h2>
  <p class="testimonial-subtitle">
    Words of praise from others about our presence. You can read and also write about us here.
  </p>

  <div class="testimonial-cards">
    <div class="testimonial-card">
      <p>We got tickets for Taylor Swift when none else could. Thanks Ticketer, your reselling site made it possible to have what seemed impossible!</p>
      <div class="user">
        <img src="image/domic.jpg" alt="Emily">
        <div>
          <strong>Emily</strong><br>Manchester, UK
        </div>
      </div>
    </div>

    <div class="testimonial-card">
      <p>Ok so credit where it's due having raised the issue with they quickly got back to me and refunded the difference. They also managed to get me the tickets so my daughter got to see Taylor Swift…</p>
      <div class="user">
        <img src="image/domic.jpg" alt="William">
        <div>
          <strong>William</strong><br>Birmingham, UK
        </div>
      </div>
    </div>

    <div class="testimonial-card">
      <p>I had such a great experience!!! I bought an eras tour ticket and they promised to transfer it until the upcoming concert which is 10 days away. I got the ticket the day after.</p>
      <div class="user">
        <img src="image/domic.jpg" alt="Daisy">
        <div>
          <strong>Daisy</strong><br>Liverpool, UK
        </div>
      </div>
    </div>
  </div>

  <div class="testimonial-actions">
    <button class="outline-btn"> Read All Review</button>
    <button class="filled-btn">Leave a comment</button>
  </div>
</section>

<!-- White Gap -->
<div class="white-gap"></div>

<!-- FAQ Section -->
<section class="faq-section">
  <div class="faq-left">
    <h3>Frequent ly Asked Questions</h3>
    <div class="faq-contact">
      <img src="image/icon-mail.png" alt="Email icon">
      <p>helpcenter@ticketer.com</p>
    </div>
    <div class="faq-contact">
      <img src="image/icon-phone.png" alt="Phone icon">
      <p>(010) 123-4567</p>
    </div>
    <h4>Still Have Questions?</h4>
    <p class="small-text">Can’t find the answer you’re looking for? Please contact our help center.</p>
    <button class="contact-btn">Contact Us</button>
  </div>

  <div class="faq-right">
    <div class="faq-item">
      <h5>I haven’t received any order confirmation yet. Did my booking go through?</h5>
      <p>Lorem ipsum dolor sit amet consectetur. Eleifend nunc nibh laoreet egestas.</p>
    </div>
    <div class="faq-item">
      <h5>I am not able/do not want to attend an already booked event. Is there a possibility to cancel/rebook?</h5>
    </div>
    <div class="faq-item">
      <h5>I lost my e-Ticket. What can I do?</h5>
    </div>
    <div class="faq-item">
      <h5>An event was canceled/postponed/relocated. Can I cancel my tickets?</h5>
    </div>
    <div class="faq-item">
      <h5>I’ve already ordered tickets and now want to add another. Is it possible yet to sit together?</h5>
    </div>

    <button class="read-more">Read More ➜</button>
  </div>
</section>

<!-- Block trắng chia section -->
<div class="white-gap"></div>

<!-- Footer -->
<footer class="footer-section">
  <div class="footer-top">
    <div class="footer-col about">
      <img src="image/logoden.png" alt="logo" class="footer-logo">
      <h4>Who we are?</h4>
      <p>Ticketer is a global ticketing platform for live experiences that allows anyone to create, share, find and attend events that fuel their passions and enrich their lives.</p>
      <p class="email">Contact<br>Ticketercontacts@gmail.com</p>
    </div>

    <div class="footer-col">
      <h4>TICKETER</h4>
      <ul>
        <li>About Us</li>
        <li>Contact Us</li>
        <li>FAQs</li>
      </ul>
    </div>

    <div class="footer-col">
      <h4>Help</h4>
      <ul>
        <li>Concert Ticketing</li>
        <li>Account Support</li>
        <li>Terms & Conditions</li>
      </ul>
    </div>

    <div class="footer-col">
      <h4>Legal</h4>
      <ul>
        <li>Terms of Us</li>
        <li>Acceptable</li>
        <li>Privacy Policy</li>
      </ul>
    </div>
  </div>

  <div class="footer-subscribe">
    <p>Join our mailing list to stay in the loop with our...</p>
    <div class="subscribe-input">
      <input type="email" placeholder="Enter your email">
      <button>➔</button>
    </div>
  </div>

  <hr class="footer-line" />

  <div class="footer-bottom">
    <span>©2025 GROUP 1 TICKET BOOKING </span>
    <div class="footer-links">
      <a href="#">TERMS</a>
      <a href="#">PRIVACY</a>
      <a href="#">COOKIES</a>
    </div>
    <div class="footer-socials">
      <img src="image2" alt="twitter">
      <img src="image2" alt="facebook">
      <img src="image2" alt="telegram">
    </div>
  </div>
</footer>






</body>
</html>



