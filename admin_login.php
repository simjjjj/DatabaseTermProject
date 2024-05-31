<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $con->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT id, password, is_admin FROM users WHERE username='$username' AND is_admin=1";
    $result = $con->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['userid'] = $row['id'];
            $_SESSION['is_admin'] = $row['is_admin'];
            $_SESSION['message'] = "성공적으로 로그인되었습니다.";
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $_SESSION['message'] = "잘못된 비밀번호입니다.";
        }
    } else {
        $_SESSION['message'] = "존재하지 않는 사용자입니다.";
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>관리자 로그인</title>
</head>
<body>
    <h2>관리자 로그인</h2>
    <?php
    if (isset($_SESSION['message'])) {
        echo "<p>" . $_SESSION['message'] . "</p>";
        unset($_SESSION['message']);
    }
    ?>
    <form action="admin_login.php" method="post">
        <label for="username">아이디:</label>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">비밀번호:</label>
        <input type="password" id="password" name="password" required><br><br>
        <input type="submit" value="로그인">
    </form>
</body>
</html>
