<?php
include 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['content'])) {
    $user_id = $_SESSION['userid'];
    $petition_id = intval($_POST['petition_id']);
    $content = $con->real_escape_string($_POST['content']);

    // 이미 댓글을 단 적이 있는지 확인
    $stmt = $con->prepare("SELECT COUNT(*) FROM comments WHERE petition_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $petition_id, $user_id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        $_SESSION['message'] = "이미 이 청원에 댓글을 달았습니다.";
        header("Location: petition_detail.php?id=" . $petition_id);
        exit();
    }

    $stmt = $con->prepare("INSERT INTO comments (petition_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $petition_id, $user_id, $content);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['message'] = "댓글이 성공적으로 추가되었습니다.";
    } else {
        $_SESSION['message'] = "댓글 추가 중 오류가 발생했습니다.";
    }

    header("Location: petition_detail.php?id=" . $petition_id);
    exit();
} else {
    header("Location: petition_detail.php");
    exit();
}
?>
