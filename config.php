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

// 초기 관리자 계정 생성
$admin_username = 'admin';
$admin_password = password_hash('admin', PASSWORD_DEFAULT); // 'admin'을 해시한 값
$admin_email = 'admin@example.com';

// 기존 관리자 계정이 없는 경우에만 생성
$check_admin_query = "SELECT * FROM users WHERE username = '$admin_username' AND is_admin = TRUE";
$result = $con->query($check_admin_query);

if ($result->num_rows == 0) {
    $insert_admin_query = $con->prepare("INSERT INTO users (name, username, password, email, student_id, is_admin) VALUES (?, ?, ?, ?, '', TRUE)");
    $insert_admin_query->bind_param('sssss', 'Admin', $admin_username, $admin_password, $admin_email);
    if ($insert_admin_query->execute()) {
        error_log("초기 관리자 계정이 생성되었습니다.");
    } else {
        error_log("초기 관리자 계정 생성 실패: " . $con->error);
    }
}
?>
