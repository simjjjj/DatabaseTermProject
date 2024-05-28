<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['content']) && isset($_POST['petition_id'])) {
    $content = $_POST['content'];
    $petition_id = intval($_POST['petition_id']);
    $user_id = $_SESSION['userid'];
    $author = $_SESSION['username'];

    $stmt = $con->prepare("INSERT INTO comments (petition_id, user_id, author, content, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiss", $petition_id, $user_id, $author, $content);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['message'] = "댓글이 성공적으로 추가되었습니다.";
    } else {
        $_SESSION['message'] = "댓글 추가 중 오류가 발생했습니다.";
    }
    header("Location: petition_detail.php?id=" . $petition_id);
    exit();
} else {
    $_SESSION['message'] = "잘못된 요청입니다.";
    header("Location: index.php");
    exit();
}
?>