<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db_host = "localhost";
$db_user = "root";
$db_password = "1234";
$db_name = "konkuk_petition";

$con = new mysqli($db_host, $db_user, $db_password, $db_name);
if ($con->connect_error) {
    error_log("데이터베이스 연결 실패: " . $con->connect_error);
    exit();
}
?>
