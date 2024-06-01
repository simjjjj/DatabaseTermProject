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

// prepareAndBind 함수가 이미 선언된 경우를 방지하기 위해 확인 후 선언
if (!function_exists('prepareAndBind')) {
    function prepareAndBind($con, $query, $types, ...$params) {
        $stmt = $con->prepare($query);
        if ($stmt === false) {
            error_log('MySQL prepare error: ' . $con->error);
            return false;
        }

        $bind_names[] = $types;
        for ($i=0; $i<count($params);$i++) {
            $bind_name = 'bind' . $i;
            $$bind_name = $params[$i];
            $bind_names[] = &$$bind_name;
        }

        $return = call_user_func_array([$stmt, 'bind_param'], $bind_names);
        if ($return === false) {
            error_log('MySQL bind_param error: ' . $stmt->error);
            return false;
        }

        return $stmt;
    }
}

// 초기 관리자 계정 생성
$admin_username = 'admin';
$admin_password = password_hash('admin', PASSWORD_DEFAULT); // 'admin'을 해시한 값
$admin_email = 'admin@example.com';

// 기존 관리자 계정이 없는 경우에만 생성
$check_admin_query = "SELECT * FROM users WHERE username = ? AND is_admin = TRUE";
$check_stmt = prepareAndBind($con, $check_admin_query, 's', $admin_username);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows == 0) {
    $insert_admin_query = "INSERT INTO users (name, username, password, email, student_id, is_admin) VALUES (?, ?, ?, ?, '', TRUE)";
    $insert_stmt = prepareAndBind($con, $insert_admin_query, 'sssss', 'Admin', $admin_username, $admin_password, $admin_email);
    if ($insert_stmt && $insert_stmt->execute()) {
        error_log("초기 관리자 계정이 생성되었습니다.");
    } else {
        error_log("초기 관리자 계정 생성 실패: " . $con->error);
    }
}
?>
