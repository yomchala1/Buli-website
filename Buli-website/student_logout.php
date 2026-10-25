<?php
session_start();
unset($_SESSION['student_id']);
unset($_SESSION['student_name']);
session_destroy();
header('Location: student_login.php');
exit;
