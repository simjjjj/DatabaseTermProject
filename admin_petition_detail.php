<?php
include 'config.php';
require_once 'functions.php';
requireAdmin();

$petition_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $con->prepare("SELECT * FROM petitions WHERE id = ?");
$stmt->bind_param("i", $petition_id);
$stmt->execute();
$result = $stmt->get_result();
$petition = $result->fetch_assoc();

$admin_responses = [];
if ($petition) {
    $stmt = $con->prepare("SELECT pr.*, u.username AS admin_name FROM petition_responses pr JOIN users u ON pr.admin_id = u.id WHERE pr.petition_id = ?");
    $stmt->bind_param("i", $petition_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $admin_responses[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['respond'])) {
    $response_content = $con->real_escape_string($_POST['response_content']);
    $admin_id = $_SESSION['userid'];

    $stmt = $con->prepare("INSERT INTO petition_responses (petition_id, admin_id, content, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $petition_id, $admin_id, $response_content);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['message'] = "답변이 성공적으로 추가되었습니다.";
    } else {
        $_SESSION['message'] = "답변 추가 중 오류가 발생했습니다.";
    }

    header("Location: admin_petition_detail.php?id=" . $petition_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>청원 상세 및 답변</title>
    <?php include 'styles.php'; ?>
</head>
<body class="dark-mode">
    <?php include 'header.php'; ?>
    <div class="container mx-auto px-6 py-12">
        <h2 class="text-3xl font-bold mb-6">청원 상세 및 답변</h2>
        <?php
        if (isset($_SESSION['message'])) {
            echo "<p>" . $_SESSION['message'] . "</p>";
            unset($_SESSION['message']);
        }
        ?>
        <?php if ($petition): ?>
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden mb-6">
                <div class="p-6">
                    <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-200"><?php echo htmlspecialchars($petition['title']); ?></h3>
                    <p class="mb-4 text-gray-700 dark:text-gray-300"><?php echo nl2br(htmlspecialchars($petition['content'])); ?></p>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">작성자: <?php echo htmlspecialchars($petition['user_id']); ?></p>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">청원기간: <?php echo htmlspecialchars($petition['created_at']); ?></p>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4"><?php echo htmlspecialchars($petition['likes']); ?> Likes</p>
                </div>
            </div>
            <h3 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-200">관리자 답변</h3>
            <?php if ($admin_responses): ?>
                <?php foreach ($admin_responses as $response): ?>
                    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden mb-4">
                        <div class="p-4">
                            <p class="text-sm text-gray-700 dark:text-gray-300"><?php echo htmlspecialchars($response['content']); ?></p>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">작성자: <?php echo htmlspecialchars($response['admin_name']); ?>, 작성일: <?php echo htmlspecialchars($response['created_at']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-gray-700 dark:text-gray-300">답변이 없습니다.</p>
            <?php endif; ?>

            <form method="POST" action="admin_petition_detail.php?id=<?php echo $petition_id; ?>" class="mt-6">
                <textarea name="response_content" class="border px-4 py-2 rounded w-full text-gray-900 dark:text-gray-300 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600" placeholder="답변을 입력하세요" required></textarea>
                <button type="submit" name="respond" class="bg-blue-600 text-white py-2 px-4 rounded mt-4">답변 달기</button>
            </form>
        <?php else: ?>
            <p class="text-gray-700 dark:text-gray-300">청원이 존재하지 않습니다.</p>
        <?php endif; ?>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
