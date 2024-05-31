<?php
include 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

    $sql = "INSERT INTO petitions (user_id, title, content, category, created_at, attachment) VALUES ('$user_id', '$title', '$content', '$category', '$created_at', '$file_dest')";

    if ($con->query($sql) === TRUE) {
        $_SESSION['message'] = "청원이 성공적으로 등록되었습니다.";
        header("Location: index.php");
        exit();
    } else {
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
    <?php include 'styles.php'; ?>
</head>
<body>
    <h2>청원 등록</h2>
    <?php
    if (isset($_SESSION['message'])) {
        echo "<p>" . $_SESSION['message'] . "</p>";
        unset($_SESSION['message']);
    }
    ?>
    <form action="" method="post" enctype="multipart/form-data">
        <label for="title">제목:</label>
        <input type="text" id="title" name="title" required class="border px-4 py-2 rounded w-full"><br><br>

        <label for="content">내용:</label>
        <textarea id="content" name="content" required class="border px-4 py-2 rounded w-full"></textarea><br><br>

        <label for="attachment">첨부 파일:</label>
        <input type="file" id="attachment" name="attachment" class="border px-4 py-2 rounded w-full"><br><br>

        <label for="category">카테고리:</label>
        <select id="category" name="category" required class="border px-4 py-2 rounded w-full">
            <option value="">카테고리를 선택하세요</option>
            <option value="학사 및 교육">학사 및 교육</option>
            <option value="캠퍼스 시설">캠퍼스 시설</option>
            <option value="학생 복지">학생 복지</option>
            <option value="행정 및 정책">행정 및 정책</option>
        </select><br><br>

        <input type="hidden" name="create_petition" value="1">
        <input type="submit" value="청원 등록" class="bg-blue-600 text-white py-2 px-4 rounded">
    </form>
</body>
</html>
