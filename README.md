# Ticket Booking PHP Web Application

A complete event ticket booking platform built with PHP and MySQL. Users can browse, search, and book tickets for concerts and events. Admins can manage events, ticket types, and user accounts. The project also integrates with VNPAY for online payments.

## Features

- **User Features:**
  - Browse upcoming events and concerts
  - Search and filter events by name, date, and location
  - View event details and ticket prices
  - Book tickets and pay online via VNPAY
  - User registration, login, and account management
  - View and manage booked tickets

- **Admin Features:**
  - Add, edit, and delete events
  - Manage ticket types for each event
  - Upload event images
  - Manage registered user accounts

- **Other:**
  - Responsive and modern UI
  - Testimonials, news, and FAQ sections
  - Email subscription (UI only)
  - Secure database queries using prepared statements

## Technologies Used

- PHP (Core logic)
- MySQL (Database)
- HTML, CSS, JavaScript (Frontend)
- VNPAY (Payment gateway integration)
- XAMPP (Recommended for local development)

## Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/yourusername/ticket_booking_php.git
   ```

2. **Setup the database:**
   - Import the provided SQL file (if available) into your MySQL server.
   - Update `db_connect.php` with your database credentials.

3. **Configure VNPAY:**
   - Update VNPAY credentials in `vnpay_payment.php` with your own `TmnCode` and `HashSecret` if needed.

4. **Run the project:**
   - Place the project folder in your XAMPP `htdocs` directory.
   - Start Apache and MySQL from XAMPP.
   - Access the site at [http://localhost/ticket_booking_php/index.php](http://localhost/ticket_booking_php/index.php)

## Folder Structure

```
/ticket_booking_php
  ├── index.php
  ├── concert.php
  ├── event_admin.php
  ├── book_ticket.php
  ├── vnpay_payment.php
  ├── get_ticket.php
  ├── db_connect.php
  ├── style.css
  ├── concert.css
  ├── image/
  └── ... (other files)
```

## Screenshots

![Home Page](screenshots/home.png)
![Event Booking](screenshots/booking.png)
![Admin Panel](screenshots/admin.png)

## License

This project is for educational purposes. Please contact the author for commercial use.

---

**Group 1 - Event Ticket Booking Project**
