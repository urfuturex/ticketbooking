<?php
include 'db_connect.php';

// Handle insert if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['update_event'])) {
    $name = $_POST['event_name'];
    $date = $_POST['event_date'];
    $location = $_POST['location'];
    $price = $_POST['price'];

    // Kiểm tra trùng tên sự kiện
    $stmt = $conn->prepare("SELECT id FROM events WHERE event_name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo "<script>alert('Tên sự kiện đã tồn tại!'); window.history.back();</script>";
        exit;
    }
    $stmt->close();

    // Upload image
    $image = $_FILES['image_file'];
    $image_name = basename($image['name']);
    $target_dir = "image/";
    $target_file = $target_dir . $image_name;

    if (move_uploaded_file($image["tmp_name"], $target_file)) {
        $sql = "INSERT INTO events (event_name, event_date, location, price, image_name)
                VALUES ('$name', '$date', '$location', '$price', '$image_name')";
        if ($conn->query($sql)) {
            $event_id = $conn->insert_id;
            // Thêm các loại vé
            if (!empty($_POST['ticket_type_name'])) {
                $count = count($_POST['ticket_type_name']);
                for ($i = 0; $i < $count; $i++) {
                    $type_name = $conn->real_escape_string($_POST['ticket_type_name'][$i]);
                    $desc = $conn->real_escape_string($_POST['ticket_description'][$i]);
                    $hall = $conn->real_escape_string($_POST['ticket_hall'][$i]);
                    $qty = intval($_POST['ticket_quantity'][$i]);
                    $tprice = intval($_POST['ticket_price'][$i]);
                    $conn->query("INSERT INTO ticket_types (event_id, type_name, description, hall, quantity, price)
                                  VALUES ($event_id, '$type_name', '$desc', '$hall', $qty, $tprice)");
                }
            }
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Optionally: delete image file here
    $conn->query("DELETE FROM events WHERE id=$id");
    header("Location: event_admin.php");
    exit;
}

// Handle edit
$edit_event = null;
$edit_ticket_types = [];
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM events WHERE id=$id");
    if ($res && $res->num_rows === 1) {
        $edit_event = $res->fetch_assoc();
        // Lấy danh sách loại vé của event này
        $ticket_res = $conn->query("SELECT * FROM ticket_types WHERE event_id=$id");
        while ($row = $ticket_res->fetch_assoc()) {
            $edit_ticket_types[] = $row;
        }
    }
}

// Handle update
if (isset($_POST['update_event'])) {
    $id = intval($_POST['event_id']);
    $name = $_POST['event_name'];
    $date = $_POST['event_date'];
    $location = $_POST['location'];

    // Update event info
    if (!empty($_FILES['image_file']['name'])) {
        $image = $_FILES['image_file'];
        $image_name = basename($image['name']);
        $target_dir = "image/";
        $target_file = $target_dir . $image_name;
        move_uploaded_file($image["tmp_name"], $target_file);
        $conn->query("UPDATE events SET event_name='$name', event_date='$date', location='$location', image_name='$image_name' WHERE id=$id");
    } else {
        $conn->query("UPDATE events SET event_name='$name', event_date='$date', location='$location' WHERE id=$id");
    }

    // Update ticket types
    if (!empty($_POST['ticket_type_name'])) {
        $count = count($_POST['ticket_type_name']);
        for ($i = 0; $i < $count; $i++) {
            $ticket_id = isset($_POST['ticket_type_id'][$i]) ? intval($_POST['ticket_type_id'][$i]) : 0;
            $type_name = $conn->real_escape_string($_POST['ticket_type_name'][$i]);
            $desc = $conn->real_escape_string($_POST['ticket_description'][$i]);
            $hall = $conn->real_escape_string($_POST['ticket_hall'][$i]);
            $qty = intval($_POST['ticket_quantity'][$i]);
            $tprice = intval($_POST['ticket_price'][$i]);
            if ($ticket_id > 0) {
                // Update existing ticket type
                $conn->query("UPDATE ticket_types SET type_name='$type_name', description='$desc', hall='$hall', quantity=$qty, price=$tprice WHERE id=$ticket_id AND event_id=$id");
            } else {
                // Add new ticket type
                $conn->query("INSERT INTO ticket_types (event_id, type_name, description, hall, quantity, price)
                              VALUES ($id, '$type_name', '$desc', '$hall', $qty, $tprice)");
            }
        }
        // Xóa các loại vé đã bị remove khỏi form
        $existing_ids = array_filter(array_map('intval', $_POST['ticket_type_id']));
        if (!empty($existing_ids)) {
            $ids_str = implode(',', $existing_ids);
            $conn->query("DELETE FROM ticket_types WHERE event_id=$id AND id NOT IN ($ids_str)");
        } else {
            $conn->query("DELETE FROM ticket_types WHERE event_id=$id");
        }
    }
    header("Location: event_admin.php");
    exit;
}

// Handle delete user
if (isset($_GET['delete_user'])) {
    $id = intval($_GET['delete_user']);
    $conn->query("DELETE FROM users WHERE id=$id");
    header("Location: event_admin.php");
    exit;
}

// Handle edit user
$edit_user = null;
if (isset($_GET['edit_user'])) {
    $id = intval($_GET['edit_user']);
    $res = $conn->query("SELECT * FROM users WHERE id=$id");
    if ($res && $res->num_rows === 1) {
        $edit_user = $res->fetch_assoc();
    }
}

// Handle update user
if (isset($_POST['update_user'])) {
    $id = intval($_POST['user_id']);
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $conn->query("UPDATE users SET name='$name', email='$email' WHERE id=$id");
    header("Location: event_admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin: Event Manager</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f2f6ff;
      color: #222;
      padding: 2rem;
    }

    h2 {
      color: #092c75;
      border-left: 5px solid #2979ff;
      padding-left: 10px;
    }

    .form-container {
      background-color: white;
      padding: 2rem 2.5rem 1.5rem 2.5rem;
      border-radius: 12px;
      max-width: 500px;
      min-width: 350px;
      margin: 30px auto 30px auto;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      display: flex;
      flex-direction: column;
      align-items: stretch;
    }

    .form-container form input[type="text"],
    .form-container form input[type="date"],
    .form-container form input[type="number"],
    .form-container form input[type="file"],
    .form-container form input[type="email"] {
      width: 100%;
      padding: 0.6rem;
      margin-top: 5px;
      margin-bottom: 1rem;
      border: 1px solid #ccc;
      border-radius: 5px;
      box-sizing: border-box;
    }

    .form-container form input[type="submit"] {
      background-color: #2979ff;
      color: white;
      border: none;
      padding: 0.7rem 1.2rem;
      border-radius: 6px;
      cursor: pointer;
      transition: background 0.3s;
      width: 100%;
      margin-top: 10px;
    }

    .form-container form input[type="submit"]:hover {
      background-color: #1c54b2;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      box-shadow: 0 4px 8px rgba(0,0,0,0.06);
      border-radius: 10px;
      overflow: hidden;
    }

    th, td {
      padding: 12px 16px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #2979ff;
      color: white;
    }

    img.event-img {
      width: 100px;
      border-radius: 6px;
    }
  </style>
</head>
<body>

  <div class="form-container">
    <?php if ($edit_event): ?>
      <h2>Edit Event</h2>
      <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="event_id" value="<?= $edit_event['id'] ?>">
        Event Name: <input type="text" name="event_name" value="<?= htmlspecialchars($edit_event['event_name']) ?>" required><br>
        Event Date: <input type="date" name="event_date" value="<?= htmlspecialchars($edit_event['event_date']) ?>" required><br>
        Location: <input type="text" name="location" value="<?= htmlspecialchars($edit_event['location']) ?>" required><br>
        Current Image: <br>
        <img src="image/<?= htmlspecialchars($edit_event['image_name']) ?>" alt="Event Image" class="event-img"><br>
        Change Image: <input type="file" name="image_file"><br>
        <hr>
        <b>Edit Ticket Types:</b><br>
        <div id="ticket-types-list">
          <?php foreach ($edit_ticket_types as $i => $t): ?>
            <div class="ticket-type-row">
              <input type="hidden" name="ticket_type_id[]" value="<?= $t['id'] ?>">
              Type Name: <input type="text" name="ticket_type_name[]" value="<?= htmlspecialchars($t['type_name']) ?>" required style="width:120px;">
              Description: <input type="text" name="ticket_description[]" value="<?= htmlspecialchars($t['description']) ?>" required style="width:180px;">
              Hall: <input type="text" name="ticket_hall[]" value="<?= htmlspecialchars($t['hall']) ?>" required style="width:120px;">
              Quantity: <input type="number" name="ticket_quantity[]" min="1" value="<?= $t['quantity'] ?>" required style="width:70px;">
              Price: <input type="number" name="ticket_price[]" min="0" value="<?= $t['price'] ?>" required style="width:90px;">
              <button type="button" onclick="removeTicketType(this)">Remove</button>
            </div>
          <?php endforeach; ?>
        </div>
        <button type="button" onclick="addTicketType()">+ Add Ticket Type</button>
        <br><br>
        <input type="submit" name="update_event" value="Update Event">
        <a href="event_admin.php" style="margin-left:20px;">Cancel</a>
      </form>
      <script>
        function addTicketType() {
          var html = `<div class="ticket-type-row">
            <input type="hidden" name="ticket_type_id[]" value="">
            Type Name: <input type="text" name="ticket_type_name[]" required style="width:120px;">
            Description: <input type="text" name="ticket_description[]" required style="width:180px;">
            Hall: <input type="text" name="ticket_hall[]" value="Main Hall" required style="width:120px;">
            Quantity: <input type="number" name="ticket_quantity[]" min="1" value="10" required style="width:70px;">
            Price: <input type="number" name="ticket_price[]" min="0" required style="width:90px;">
            <button type="button" onclick="removeTicketType(this)">Remove</button>
          </div>`;
          document.getElementById('ticket-types-list').insertAdjacentHTML('beforeend', html);
        }
        function removeTicketType(btn) {
          btn.parentElement.remove();
        }
      </script>
    <?php else: ?>
      <h2>Add New Event</h2>
      <form action="" method="post" enctype="multipart/form-data">
        Event Name: <input type="text" name="event_name" required><br>
        Event Date: <input type="date" name="event_date" required><br>
        Location: <input type="text" name="location" required><br>
        Price (VND): <input type="number" name="price" required><br>
        Image file: <input type="file" name="image_file" required><br>
        <hr>
        <b>Add Ticket Types:</b><br>
        <div id="ticket-types-list">
          <div class="ticket-type-row">
            Type Name: <input type="text" name="ticket_type_name[]" required style="width:120px;">
            Description: <input type="text" name="ticket_description[]" required style="width:180px;">
            Hall: <input type="text" name="ticket_hall[]" value="Main Hall" required style="width:120px;">
            Quantity: <input type="number" name="ticket_quantity[]" min="1" value="10" required style="width:70px;">
            Price: <input type="number" name="ticket_price[]" min="0" required style="width:90px;">
            <button type="button" onclick="removeTicketType(this)">Remove</button>
          </div>
        </div>
        <button type="button" onclick="addTicketType()">+ Add Ticket Type</button>
        <br><br>
        <input type="submit" value="Add Event">
      </form>
      <script>
        function addTicketType() {
          var html = `<div class="ticket-type-row">
            Type Name: <input type="text" name="ticket_type_name[]" required style="width:120px;">
            Description: <input type="text" name="ticket_description[]" required style="width:180px;">
            Hall: <input type="text" name="ticket_hall[]" value="Main Hall" required style="width:120px;">
            Quantity: <input type="number" name="ticket_quantity[]" min="1" value="10" required style="width:70px;">
            Price: <input type="number" name="ticket_price[]" min="0" required style="width:90px;">
            <button type="button" onclick="removeTicketType(this)">Remove</button>
          </div>`;
          document.getElementById('ticket-types-list').insertAdjacentHTML('beforeend', html);
        }
        function removeTicketType(btn) {
          btn.parentElement.remove();
        }
      </script>
    <?php endif; ?>
  </div>

  <h2>Current Events</h2>
  <table>
    <tr>
      <th>Image</th>
      <th>Name</th>
      <th>Date</th>
      <th>Location</th>
      <th>Price (VND)</th>
      <th>Actions</th>
    </tr>
    <?php
      $result = mysqli_query($conn, "SELECT * FROM events ORDER BY event_date ASC");
      while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td><img src='image/" . htmlspecialchars($row['image_name']) . "' alt='Event Image' class='event-img'></td>";
        echo "<td>" . htmlspecialchars($row['event_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['event_date']) . "</td>";
        echo "<td>" . htmlspecialchars($row['location']) . "</td>";
        echo "<td>" . number_format($row['price'], 0, '.', '.') . " đ</td>";
        echo "<td>
                <a href='?edit=" . $row['id'] . "' style='color:#2979ff;'>Edit</a> | 
                <a href='?delete=" . $row['id'] . "' style='color:#e53935;' onclick=\"return confirm('Are you sure you want to delete this event?');\">Delete</a>
              </td>";
        echo "</tr>";
      }
    ?>
  </table>

  <h2>Registered Accounts</h2>
  <table>
    <tr>
      <th>ID</th>
      <th>Full Name</th>
      <th>Email</th>
      <th>Created At</th>
      <th>Actions</th>
    </tr>
    <?php
      $result = mysqli_query($conn, "SELECT * FROM users ORDER BY id ASC");
      while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
        echo "<td>
                <a href='?edit_user=" . $row['id'] . "' style='color:#2979ff;'>Edit</a> | 
                <a href='?delete_user=" . $row['id'] . "' style='color:#e53935;' onclick=\"return confirm('Are you sure you want to delete this user?');\">Delete</a>
              </td>";
        echo "</tr>";
      }
    ?>
  </table>

  <?php if ($edit_user): ?>
    <div class="form-container">
      <h2>Edit User</h2>
      <form method="post">
        <input type="hidden" name="user_id" value="<?= $edit_user['id'] ?>">
        Full Name: <input type="text" name="name" value="<?= htmlspecialchars($edit_user['name']) ?>" required><br>
        Email: <input type="email" name="email" value="<?= htmlspecialchars($edit_user['email']) ?>" required><br>
        <input type="submit" name="update_user" value="Update User">
        <a href="event_admin.php" style="margin-left:20px;">Cancel</a>
      </form>
    </div>
  <?php endif; ?>

</body>
</html>
