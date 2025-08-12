<?php
include('../connect.php');

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
  <style>
    body {
      font-family: 'Georgia', serif;
      background: #f8f6f3;
      padding: 30px;
    }

    h2 {
      color: #2e4d3d;
      text-align: center;
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background: white;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    th, td {
      padding: 14px;
      border-bottom: 1px solid #ccc;
      text-align: left;
      font-size: 15px;
    }

    tr.unread {
      background-color: #f0f8ff;
    }

    a.btn {
      padding: 6px 12px;
      background-color: #2e4d3d;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      font-size: 14px;
      margin-right: 5px;
      display: inline-block;
    }

    a.btn:hover {
      background-color: #3a5e4f;
    }

    .delete-btn {
      background-color: #d32f2f;
    }

    .delete-btn:hover {
      background-color: #b71c1c;
    }

    .back-link {
      display: inline-block;
      margin-bottom: 20px;
      background-color: #2e4d3d;
      color: white;
      padding: 10px 18px;
      border-radius: 6px;
      text-decoration: none;
      font-size: 14px;
    }

    .back-link:hover {
      background-color: #3a5e4f;
    }
  </style>
</head>
<body>

  <a class="back-link" href="admindash.php">← Back to Dashboard</a>
  <h2>Contact Messages</h2>

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
      <tr class="<?= $row['is_read'] ? '' : 'unread' ?>">
        <td><?= $i++ ?></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
        <td><?= $row['created_at'] ?></td>
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
