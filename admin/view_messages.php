<?php
include('../connect.php');
session_start();

// Ensure only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
    include('adminnav.php');
}

// Mark as read
if (isset($_GET['read_id'])) {
  $id = intval($_GET['read_id']);
  mysqli_query($conn, "UPDATE contact_messages SET is_read = 1 WHERE id = $id");
  header("Location: view_messages.php");
  exit;
}

// Delete message
if (isset($_GET['delete_id'])) {
  $id = intval($_GET['delete_id']);
  mysqli_query($conn, "DELETE FROM contact_messages WHERE id = $id");
  header("Location: view_messages.php");
  exit;
}

// Fetch messages
$result = mysqli_query($conn, "SELECT * FROM contact_messages ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - View Messages</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #103e12ff;
      --primary-dark: #18461cff;
      --accent: #66bb6a;
      --secondary: #f1c40f;
      --light: #ffffff;
      --dark: #212121;
      --text: #34495e;
      --text-light: #7f8c8d;
      --border-radius: 8px;
      --box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      --transition: all 0.3s ease;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(120deg, #f5f9f6, #ecfdf5);
      padding: 30px;
      margin: 0;
      color: var(--text);
    }

    h2 {
      color: var(--primary-dark);
      text-align: center;
      margin-bottom: 25px;
      font-size: 26px;
      font-weight: 700;
    }

    .back-link {
      display: inline-block;
      margin-bottom: 20px;
      background-color: var(--primary);
      color: white;
      padding: 10px 18px;
      border-radius: var(--border-radius);
      text-decoration: none;
      font-size: 14px;
      box-shadow: var(--box-shadow);
      transition: var(--transition);
    }

    .back-link:hover {
      background-color: var(--primary-dark);
      transform: translateY(-2px);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background: white;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    }

    th {
      background: var(--primary);
      color: white;
      font-weight: 600;
      text-align: left;
      padding: 14px 18px;
      text-transform: uppercase;
      font-size: 13px;
      letter-spacing: 1px;
    }

    td {
      padding: 14px 18px;
      border-bottom: 1px solid #f0f0f0;
      vertical-align: top;
      font-size: 15px;
    }

    tr.unread td {
      background-color: #f0fdf4;
      font-weight: 500;
    }

    /* Style for read messages */
    tr.read-message td {
      background-color: #f9f9f9;
      color: #888;
    }
    
    tr.read-message td:first-child:before {
      content: "✓ ";
      color: var(--accent);
      font-weight: bold;
    }

    tr:hover td {
      background: #f9fdf9;
    }

    a.btn {
      padding: 8px 14px;
      background-color: var(--primary);
      color: green;
      text-decoration: none;
      border-radius: 6px;
      font-size: 13px;
      margin-right: 5px;
      display: inline-block;
      box-shadow: var(--box-shadow);
      transition: var(--transition);
    }

    a.btn:hover {
      background-color: var(--primary-dark);
      transform: translateY(-2px);
      color: red;
    }

    .delete-btn {
      background-color: #d32f2f;
      color: white;
    }

    .delete-btn:hover {
      background-color: #b71c1c;
      color: red;
    }
  </style>
</head>
<body>
 <?php include('adminnav.php'); ?>

  
  <h2>📩 Contact Messages</h2>

  <table>
    <tr>
      <th>Sl No</th>
      <th>Name</th>
      <th>Email</th>
      <th>Message</th>
      <th>Received</th>
      <th>Action</th>
    </tr>

    <?php $i = 1; while ($row = mysqli_fetch_assoc($result)) : ?>
      <tr class="<?= $row['is_read'] ? 'read-message' : 'unread' ?>">
        <td><?= $i++ ?></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
        <td><?= date('M j, Y g:i a', strtotime($row['created_at'])) ?></td>
        <td>
          <?php if (!$row['is_read']) : ?>
            <a class="btn" href="?read_id=<?= $row['id'] ?>">Mark as Read</a>
          <?php endif; ?>
          <a class="btn delete-btn" href="?delete_id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this message?')">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>

</body>
</html>