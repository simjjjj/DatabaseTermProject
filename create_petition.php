<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_petition'])) {
    if (!isset($_SESSION['userid'])) {
        $_SESSION['message'] = "로그인이 필요합니다.";
        header("Location: index.php");
        exit();
    }

    $title = $con->real_escape_string($_POST['title']);
    $content = $con->real_escape_string($_POST['content']);
    $category = $con->real_escape_string($_POST['category']);
    $user_id = $_SESSION['userid'];
    $created_at = date('Y-m-d H:i:s');

    error_log("청원 등록 시도: $title, $content, $category, $created_at, $user_id");

    $sql = "INSERT INTO petitions (user_id, title, content, category, created_at) VALUES ('$user_id', '$title', '$content', '$category', '$created_at')";

    if ($con->query($sql) === TRUE) {
        error_log("청원 등록 성공: $title");
        $_SESSION['message'] = "청원이 성공적으로 등록되었습니다.";
        header("Location: index.php");
        exit();
    } else {
        error_log("청원 등록 오류: " . $con->error);
        $_SESSION['message'] = "오류: " . $con->error;
        header("Location: index.php");
        exit();
    }
}
?>
