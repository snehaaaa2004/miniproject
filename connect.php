<?php
$conn = mysqli_connect("localhost", "root", "", "serenityconnect");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
