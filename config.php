<?php
// 데이터베이스 연결 설정
$db_host = "localhost";
$db_user = "root";
$db_password = "1234";
$db_name = "konkuk_petition";

$con = mysqli_connect($db_host, $db_user, $db_password, $db_name);
if (mysqli_connect_errno()) {
    error_log("데이터베이스 연결 실패: " . mysqli_connect_error());
    exit();
}

session_start();

// 회원가입 처리
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = mysqli_real_escape_string($con, password_hash($_POST['password'], PASSWORD_DEFAULT));
    $password_confirm = $_POST['password_confirm'];
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    if ($_POST['password'] !== $password_confirm) {
        $_SESSION['message'] = "비밀번호가 일치하지 않습니다.";
        header("Location: index.php");
        exit();
    }

    $student_id = '';
    if (isset($_FILES['student_id']) && $_FILES['student_id']['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['student_id']['tmp_name'];
        $file_name = basename($_FILES['student_id']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $valid_extensions = array('jpg', 'jpeg', 'png', 'pdf');
        if (in_array($file_ext, $valid_extensions)) {
            $new_file_name = uniqid('', true) . '.' . $file_ext;
            $file_dest = "uploads/" . $new_file_name;
            if (move_uploaded_file($file_tmp, $file_dest)) {
                $student_id = $file_dest;
            } else {
                $_SESSION['message'] = "파일 업로드에 실패했습니다.";
                header("Location: index.php");
                exit();
            }
        } else {
            $_SESSION['message'] = "잘못된 파일 형식입니다.";
            header("Location: index.php");
            exit();
        }
    }

    $sql = "INSERT INTO users (name, username, password, email, student_id, is_admin) VALUES ('$name', '$username', '$password', '$email', '$student_id', '$is_admin')";

    if (mysqli_query($con, $sql)) {
        $_SESSION['message'] = "계정이 성공적으로 생성되었습니다.";
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['message'] = "오류: " . mysqli_error($con);
        header("Location: index.php");
        exit();
    }
}
?>
