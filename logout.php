<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
    session_start();
    session_destroy();
    echo json_encode(['status' => 'success', 'message' => '로그아웃되었습니다.']);
    exit();
} else {
    echo json_encode(['status' => 'error', 'message' => '잘못된 요청입니다.']);
    exit();
}
?>