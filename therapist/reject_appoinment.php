<?php
include('../connect.php');
$id = $_GET['id'];
mysqli_query($conn, "UPDATE appointments SET status = 'Rejected' WHERE id = $id");
header("Location: appointment.php");
?>
