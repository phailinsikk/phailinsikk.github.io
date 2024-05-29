<?php
session_start();

// Clear summary data or any other session-related data (if needed)
unset($_SESSION['summary_data']); // เช่น $_SESSION['summary_data'] คือข้อมูล Summary

// Unset all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: indexx.php");
exit();
?>