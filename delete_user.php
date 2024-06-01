<?php
include 'config.php';
require_once 'functions.php';
requireAdmin();

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);

    // 회원 삭제
    $stmt = $con->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['message'] = "회원이 성공적으로 삭제되었습니다.";
    } else {
        $_SESSION['message'] = "회원 삭제 중 오류가 발생했습니다.";
    }
} else {
    $_SESSION['message'] = "잘못된 요청입니다.";
}

header("Location: admin_dashboard.php");
exit();
?>
