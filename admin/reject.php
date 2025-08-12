<?php
include('../connect.php');
$id = $_GET['id'];

$sql = "UPDATE therapists SET approved = -1 WHERE id = $id";
if ($conn->query($sql) === TRUE) {
    header("Location: view_therapists.php");
} else {
    echo "Error updating record: " . $conn->error;
}
?>
