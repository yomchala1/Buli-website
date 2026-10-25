<?php
session_start();
unset($_SESSION['staff_logged_in']);
unset($_SESSION['staff_id']);
unset($_SESSION['staff_name']);
unset($_SESSION['staff_dept']);
session_destroy();
header('Location: staff_login.php');
exit;
