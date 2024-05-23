<?php
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    if (registerPetition($title, $content)) {
        $_SESSION['message'] = "청원이 성공적으로 등록되었습니다.";
    } else {
        $_SESSION['message'] = "청원 등록 중 오류가 발생했습니다.";
    }
    header("Location: index.php");
    exit();
}
?>
