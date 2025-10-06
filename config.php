<?php
$hostname = "localhost";
$username = "root";
$password = "";
$database = "114_games";
$conn = mysqli_connect($hostname, $username, $password, $database);
$currency = "INR"; // Set your desired currency symbol
$contact_number = "+919773186204"; // Replace with your WhatsApp number in international format
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
date_default_timezone_set("Asia/Kolkata");
?>