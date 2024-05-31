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
        <table class="min-w-full bg-white dark:bg-gray-800">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">제목</th>
                    <th class="py-2 px-4 border-b">작성자</th>
                    <th class="py-2 px-4 border-b">카테고리</th>
                    <th class="py-2 px-4 border-b">좋아요 수</th>
                    <th class="py-2 px-4 border-b">관리</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($petition = $petitions->fetch_assoc()) { ?>
                <tr>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($petition['title']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($petition['user_id']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($petition['category']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($petition['likes']); ?></td>
                    <td class="py-2 px-4 border-b">
                        <a href="petition_detail.php?id=<?php echo $petition['id']; ?>" class="text-blue-600 hover:underline">자세히 보기</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <h3 class="text-2xl font-bold mb-4 mt-6">회원 목록</h3>
        <table class="min-w-full bg-white dark:bg-gray-800">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">이름</th>
                    <th class="py-2 px-4 border-b">아이디</th>
                    <th class="py-2 px-4 border-b">이메일</th>
                    <th class="py-2 px-4 border-b">관리자 여부</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users->fetch_assoc()) { ?>
                <tr>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($user['name']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($user['username']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($user['email']); ?></td>
                    <td class="py-2 px-4 border-b"><?php echo $user['is_admin'] ? '예' : '아니오'; ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
