<?php
include 'config.php';
session_start(); // 세션 시작

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $con->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT id, username, password, is_admin FROM users WHERE username='$username'";
    $result = $con->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['userid'] = $row['id'];
            $_SESSION['username'] = $row['username']; // 여기서 설정
            $_SESSION['is_admin'] = $row['is_admin'];
            $_SESSION['message'] = "성공적으로 로그인되었습니다.";
        } else {
            $_SESSION['message'] = "잘못된 아이디 또는 비밀번호입니다.";
        }
    } else {
        $_SESSION['message'] = "잘못된 아이디 또는 비밀번호입니다.";
    }
    header("Location: index.php");
    exit();
} else {
    header("Location: index.php");
    exit();
}
?>
