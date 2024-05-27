<?php
include 'config.php';

// 이미 세션이 시작된 경우를 확인하고 세션을 시작합니다.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// POST 요청이면서 'create_petition' 값이 설정된 경우 청원 등록을 처리합니다.
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_petition'])) {
    // 로그인이 되어 있지 않으면 로그인 메시지를 설정하고 메인 페이지로 리디렉션합니다.
    if (!isset($_SESSION['userid'])) {
        $_SESSION['message'] = "로그인이 필요합니다.";
        header("Location: index.php");
        exit();
    }

    // 입력된 데이터를 안전하게 처리합니다.
    $title = $con->real_escape_string($_POST['title']);
    $content = $con->real_escape_string($_POST['content']);
    $category = $con->real_escape_string($_POST['category']);
    $user_id = $_SESSION['userid'];
    $created_at = date('Y-m-d H:i:s');

    // 파일 업로드 처리
    $file_dest = '';
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['attachment']['tmp_name'];
        $file_name = basename($_FILES['attachment']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $valid_extensions = array('jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx');
        if (in_array($file_ext, $valid_extensions)) {
            $new_file_name = uniqid('', true) . '.' . $file_ext;
            $file_dest = "uploads_petition/" . $new_file_name;
            if (!move_uploaded_file($file_tmp, $file_dest)) {
                $_SESSION['message'] = "파일 업로드에 실패했습니다. 에러 코드: " . $_FILES['attachment']['error'];
                header("Location: index.php");
                exit();
            }
        } else {
            $_SESSION['message'] = "잘못된 파일 형식입니다.";
            header("Location: index.php");
            exit();
        }
    }

    // 데이터베이스에 청원을 등록하는 쿼리를 실행합니다.
    $sql = "INSERT INTO petitions (user_id, title, content, category, created_at, attachment) VALUES ('$user_id', '$title', '$content', '$category', '$created_at', '$file_dest')";

    if ($con->query($sql) === TRUE) {
        // 청원 등록 성공을 기록하고 메시지를 설정한 후 메인 페이지로 리디렉션합니다.
        $_SESSION['message'] = "청원이 성공적으로 등록되었습니다.";
        header("Location: index.php");
        exit();
    } else {
        // 청원 등록 오류를 기록하고 메시지를 설정한 후 메인 페이지로 리디렉션합니다.
        $_SESSION['message'] = "오류: " . $con->error;
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>청원 등록</title>
</head>
<body>
    <h2>청원 등록</h2>
    <?php
    // 세션 메시지가 설정되어 있으면 메시지를 출력하고 세션 메시지를 삭제합니다.
    if (isset($_SESSION['message'])) {
        echo "<p>" . $_SESSION['message'] . "</p>";
        unset($_SESSION['message']);
    }
    ?>
    <!-- 청원 등록 폼을 출력합니다. -->
    <form action="" method="post" enctype="multipart/form-data">
        <label for="title">제목:</label>
        <input type="text" id="title" name="title" required><br><br>

        <label for="content">내용:</label>
        <textarea id="content" name="content" required></textarea><br><br>

        <label for="attachment">첨부 파일:</label>
        <input type="file" id="attachment" name="attachment"><br><br>

        <label for="category">카테고리:</label>
        <input type="text" id="category" name="category" required><br><br>

        <input type="hidden" name="create_petition" value="1">
        <input type="submit" value="청원 등록">
    </form>
</body>
</html>
