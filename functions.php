<?php
function isAdmin() {
    return isset($_SESSION['userid']) && $_SESSION['is_admin'];
}

function requireAdmin() {
    if (!isAdmin()) {
        $_SESSION['message'] = "관리자 권한이 필요합니다.";
        header("Location: index.php");
        exit();
    }
}
?>
