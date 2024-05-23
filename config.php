<?php
// 데이터베이스 연결 설정
$db_host = "localhost";
$db_user = "root";
$db_password = "ghkd5384";
$db_name = "konkuk_petition";

// 데이터베이스 연결
$con = new mysqli($db_host, $db_user, $db_password, $db_name);
if ($con->connect_error) {
    error_log("데이터베이스 연결 실패: " . $con->connect_error);
    exit();
}

session_start();

// 회원가입 처리
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $name = $con->real_escape_string($_POST['name']);
    $username = $con->real_escape_string($_POST['username']);
    $password = password_hash($con->real_escape_string($_POST['password']), PASSWORD_DEFAULT);
    $password_confirm = $_POST['password_confirm'];
    $email = $con->real_escape_string($_POST['email']);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    if ($_POST['password'] !== $password_confirm) {
        $_SESSION['message'] = "비밀번호가 일치하지 않습니다.";
        header("Location: index.php");
        exit();
    }

    // 이메일 중복 확인
    $check_email_query = "SELECT * FROM users WHERE email = '$email'";
    $result = $con->query($check_email_query);
    if ($result->num_rows > 0) {
        $_SESSION['message'] = "이미 등록된 이메일입니다.";
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

    if ($con->query($sql) === TRUE) {
        $_SESSION['message'] = "계정이 성공적으로 생성되었습니다.";
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['message'] = "오류: " . $con->error;
        header("Location: index.php");
        exit();
    }
}
?>
