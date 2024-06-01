<?php
include 'config.php';
require_once 'functions.php';
requireAdmin();

$petitions = $con->query("SELECT * FROM petitions");
$users = $con->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>관리자 대시보드</title>
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
    </style>
</head>
<body class="dark-mode">
    <?php include 'header.php'; ?>
    <div class="container mx-auto px-6 py-12">
        <h2 class="text-3xl font-bold mb-6">관리자 대시보드</h2>
        <?php
        if (isset($_SESSION['message'])) {
            echo "<p>" . $_SESSION['message'] . "</p>";
            unset($_SESSION['message']);
        }
        ?>
        <h3 class="text-2xl font-bold mb-4">청원 목록</h3>
        <table class="table-custom">
            <thead>
                <tr>
                    <th>제목</th>
                    <th>작성자</th>
                    <th>카테고리</th>
                    <th>좋아요 수</th>
                    <th>관리</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($petition = $petitions->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($petition['title']); ?></td>
                    <td><?php echo htmlspecialchars($petition['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($petition['category']); ?></td>
                    <td><?php echo htmlspecialchars($petition['likes']); ?></td>
                    <td>
                        <a href="admin_petition_detail.php?id=<?php echo $petition['id']; ?>" class="text-blue-600 hover:underline">자세히 보기</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <h3 class="text-2xl font-bold mb-4 mt-6">회원 목록</h3>
        <table class="table-custom">
            <thead>
                <tr>
                    <th>이름</th>
                    <th>아이디</th>
                    <th>이메일</th>
                    <th>관리자 여부</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo $user['is_admin'] ? '예' : '아니오'; ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
