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
    <style>
        body.dark-mode {
            background-color: #121212;
            color: #E0E0E0;
        }
        .table-custom {
            width: 100%;
            border-collapse: collapse;
        }
        .table-custom th,
        .table-custom td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .table-custom th {
            padding-top: 12px;
            padding-bottom: 12px;
            background-color: #f2f2f2;
            color: #333;
        }
        .dark-mode .table-custom th {
            background-color: #333;
            color: #f2f2f2;
        }
        .dark-mode .table-custom td {
            border-color: #555;
        }
        .btn-approve {
            background-color: #007bff;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-approve:hover {
            background-color: #0056b3;
        }
    </style>
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
        <table class="table-custom">
            <thead>
                <tr>
                    <th>이름</th>
                    <th>아이디</th>
                    <th>이메일</th>
                    <th>승인</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($request = $requests->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($request['name']); ?></td>
                    <td><?php echo htmlspecialchars($request['username']); ?></td>
                    <td><?php echo htmlspecialchars($request['email']); ?></td>
                    <td>
                        <form method="post" action="admin_approve.php">
                            <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                            <button type="submit" name="approve" class="btn-approve">승인</button>
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
