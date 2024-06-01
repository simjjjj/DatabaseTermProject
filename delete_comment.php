<?php
include 'config.php';
require_once 'functions.php';
requireAdmin();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment_id'])) {
    $comment_id = intval($_POST['comment_id']);
    $petition_id = intval($_POST['petition_id']);

    $stmt = $con->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->bind_param("i", $comment_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['message'] = "댓글이 성공적으로 삭제되었습니다.";
    } else {
        $_SESSION['message'] = "댓글 삭제 중 오류가 발생했습니다.";
    }

    header("Location: admin_petition_detail.php?id=" . $petition_id);
    exit();
} else {
    header("Location: admin_petition_detail.php");
    exit();
}
?>
