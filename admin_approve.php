<?php
include 'config.php';
require_once 'functions.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['approve'])) {
    $request_id = intval($_POST['request_id']);
    $stmt = $con->prepare("SELECT * FROM admin_requests WHERE id = ?");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $request = $stmt->get_result()->fetch_assoc();

    if ($request) {
        $name = $request['name'];
        $username = $request['username'];
        $password = $request['password'];
        $email = $request['email'];
        $student_id = $request['student_id'];

        $insert_user = $con->prepare("INSERT INTO users (name, username, password, email, student_id, is_admin) VALUES (?, ?, ?, ?, ?, TRUE)");
        $insert_user->bind_param("sssss", $name, $username, $password, $email, $student_id);
        if ($insert_user->execute()) {
            $delete_request = $con->prepare("DELETE FROM admin_requests WHERE id = ?");
            $delete_request->bind_param("i", $request_id);
            $delete_request->execute();
            $_SESSION['message'] = "관리자 요청이 승인되었습니다.";
        } else {
            $_SESSION['message'] = "관리자 요청 승인 중 오류가 발생했습니다.";
        }
    } else {
        $_SESSION['message'] = "잘못된 요청입니다.";
    }
    header("Location: admin_approve.php");
    exit();
}

$requests = $con->query("SELECT * FROM admin_requests");
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>관리자 승인</title>
    <?php include 'styles.php'; ?>
</head>
<body class="dark-mode">
    <?php include 'header.php'; ?>
    <div class="container mx-auto px-6 py-12">
        <h2 class="text-3xl font-bold mb-6">관리자 승인 요청</h2>
        <?php
        if (isset($_SESSION['message'])) {
            echo "<p>" . $_SESSION['message'] . "</p>";
            unset($_SESSION['message']);
        }
        ?>
        <table class="min-w-full bg-white dark:bg-gray-800">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">이름</th>
                    <th class="py-2 px-4 border-b">아이디</th>
                    <th class="py-2 px-4 border-b">이메일</th>
                    <th class="py-2 px-4 border-b">승인</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($request = $requests->fetch_assoc()) { ?>
                <tr>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($request['name']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($request['username']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($request['email']); ?></td>
                    <td class="py-2 px-4 border-b">
                        <form method="post" action="admin_approve.php">
                            <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                            <button type="submit" name="approve" class="bg-blue-600 text-white py-1 px-3 rounded">승인</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
